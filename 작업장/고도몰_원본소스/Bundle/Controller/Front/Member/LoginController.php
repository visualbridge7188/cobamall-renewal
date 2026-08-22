<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Bundle\Controller\Front\Member;

use Bundle\Component\Policy\AppleLoginPolicy;
use Bundle\Component\Policy\GoogleLoginPolicy;
use Bundle\Component\Policy\KakaoLoginPolicy;
use Component\Godo\GodoNaverServerApi;
use Component\Godo\GodoWonderServerApi;
use Component\Goods\Goods;
use Component\Member\Util\MemberUtil;
use Component\Policy\NaverLoginPolicy;
use Component\Policy\PaycoLoginPolicy;
use Component\Policy\WonderLoginPolicy;
use Component\Policy\SnsLoginPolicy;
use Component\SiteLink\SiteLink;
use Framework\Debug\Exception\AlertBackException;
use Origin\Service\MyApp\MyappAuth;
use Origin\Service\MyApp\MyappBridge;
use Origin\Service\MyApp\MyappUtil;
use Validation;

/**
 * Class 프론트-로그인 컨트롤러
 * @package Bundle\Controller\Front\Member
 * @author  yjwee
 */
class LoginController extends \Controller\Front\Controller
{
    const SECRET_KEY = '!#dbpassword'; // 로그인 계정 정보 암호화 처리 (암호키)

    /**
     * @inheritdoc
     */
    public function index()
    {
        /** @var \Bundle\Controller\Front\Controller $this */
        try {
            $request = \App::getInstance('request');
            $logger = \App::getInstance('logger');
            $scripts = ['gd_payco.js'];
            $urlDefaultCheck = true;

            $memberUtil = \App::load('Component\\Member\\Util\\MemberUtil');
            $memberUtil::logoutPayco();
            $memberUtil::logoutNaver();
            $memberUtil::logoutKakao();
            $memberUtil::logoutWonder();
            $memberUtil::logoutApple();
            $memberUtil::logoutGoogle();

            $facebook = \App::load('Component\\Facebook\\Facebook');
            $facebook->clearSession();

            if ($request->request()->has('returnUrl')) {
                try {
                    Validation::setExitType('throw');
                    Validation::defaultCheck(gd_isset($request->request()->get('returnUrl')), 'url');
                } catch (\Exception $e) {
                    $urlDefaultCheck = false;
                    $returnUrl = $request->getReferer();
                }

                if ($urlDefaultCheck) {
                    $parseGetReturnUrl = parse_url($request->request()->get('returnUrl'));
                    $getReturnUrl = (isset($parseGetReturnUrl['host']) === false) ? $parseGetReturnUrl['path'] : $parseGetReturnUrl['host'];
                    if (!preg_match('/' . $getReturnUrl . '/i', $request->getReferer())) {
                        $request->request()->set('returnUrl', $request->getReferer());
                    }
                    $returnUrl = $request->getReturnUrl();// url의 특이한 형태로 인해 치환코드 설정
                }
                $logger->info(sprintf('Request has return url %s', $returnUrl));
            } else {
                $returnUrl = $request->getReferer();
                if (empty($returnUrl)) {
                    $returnUrl = $request->getDomainUrl() . '/member/login.php';
                }
                $logger->info(sprintf('Request has not return url. get referer %s', $returnUrl));
            }

            // 마이앱 > 퀵 로그인 사용 안함 → 앱 로그인 버튼 통해서 로그인 하는 경우 next 쿼리 파라미터로 전달 → returnURL 세팅
            if ($request->isFromMyapp() && $request->get()->has('next')) {
                if (preg_match('/[?&]next=([^&#]+)/', $request->getRequestUri(), $matches)) {
                    $returnUrl = urldecode($matches[1]);
                } else {
                    $returnUrl = URI_MOBILE;
                }
            }

            $snsLoginPolicy = new SnsLoginPolicy();
            $useFacebook = $snsLoginPolicy->useFacebook();
            if ($useFacebook) {
                $scripts[] = 'gd_sns.js';
                if ($snsLoginPolicy->useGodoAppId()) {
                    $facebookUrl = $facebook->getGodoLoginUrl($returnUrl);
                } else {
                    $facebookUrl = $facebook->getLoginUrl($returnUrl);
                }

                $userAgent = $request->server()->get('HTTP_USER_AGENT');
                if ($request->isFromMyapp() && MyappUtil::isAndroid($userAgent)) {
                    $tokenData = ['next' => $returnUrl];
                    $token = MyappUtil::encryptCustomTabToken($tokenData);
                    $facebookUrl = MyappUtil::getCustomTabUrl('facebook', MyappAuth::CUSTOM_TAB_LOGIN_ACTIVE, $token);
                }

                $this->setData('facebookUrl', $facebookUrl);
            }

            $naverLoginPolicy = new NaverLoginPolicy();
            $useNaver = $naverLoginPolicy->useNaverLogin();
            if ($useNaver) {
                $naver = new GodoNaverServerApi();
                $scripts[] = 'gd_naver.js';
                // redirect_uri는 등록 콜백URL과 동일한 고정 경로 (strict match), 동적값은 state로 이관
                $naverRedirectUri = $request->getDomainUrl() . '/member/naver/naver_login.php';
                $naverState = $naver->createState([
                    'referer'   => $request->getDomainUrl() . parse_url($request->getRequestUri(), PHP_URL_PATH),
                    'returnUrl' => $returnUrl,
                    'naverType' => '',
                ]);
                $this->setData('naverUrl', $naver->getLoginURL($naverRedirectUri, $naverState));
            }

            $kakaoPolicy = \App::getInstance(KakaoLoginPolicy::class);
            $useKakaoLogin = $kakaoPolicy->useKakaoLogin();
            if ($useKakaoLogin) {
                $scripts[] = 'gd_kakao.js';
                //$this->setData('kakaoReturnUrl', $request->getRequestUri()); // 기존 페이지로 넘어 가지 않아 수정
                $this->setData('kakaoReturnUrl', $returnUrl);
            }

            $wonderPolicy = new WonderLoginPolicy();
            $useWonderLogin = $wonderPolicy->useWonderLogin();
            if ($useWonderLogin) {
                \Request::get()->set('returnUrl', $returnUrl);
                $wonder = new GodoWonderServerApi();
                $scripts[] = 'gd_wonder.js';
                $this->setData('wonderReturnUrl', $wonder->getAuthUrl('login'));
            }

            // apple login use check
            $appleLoginPolicy = new AppleLoginPolicy();
            if ($appleLoginPolicy->useAppleLogin() === true) {
                $this->setData('useAppleLogin', $appleLoginPolicy->useAppleLogin());
                $this->setData('client_id', $appleLoginPolicy->getClientId());
                $this->setData('redirectURI', $appleLoginPolicy->getRedirectURI());

                $this->setData('state', 'sign_in');
            }

            $googlePolicy = new GoogleLoginPolicy();
            $useGoogleLogin = $googlePolicy->useGoogleLogin();
            if ($useGoogleLogin) {
                $scripts[] = 'gd_google.js';

                $googleReturnUrl = urlencode($returnUrl);
                if ($request->isFromMyapp()) {
                    $tokenData = ['next' => $returnUrl];
                    $token = MyappUtil::encryptCustomTabToken($tokenData);
                    $googleReturnUrl = MyappUtil::getCustomTabUrl('google', MyappAuth::CUSTOM_TAB_LOGIN_ACTIVE, $token);
                }
                $this->setData('googleReturnUrl', $googleReturnUrl);
            }

            if (MemberUtil::isLogin()) {
                $this->redirect(URI_HOME);
            }

            $data = MemberUtil::getCookieByLogin();

            $hasGuestOrder = $request->get()->has('guestOrder');
            $isMemberOrder = false;
            $data['guestLogin'] = 'y';
            if (gd_count($request->request()->all()) == 0) {
                $isMemberOrder = true;
            }
            if ($request->get()->get('goodsNo', '') != '') {
                $goods = new Goods();
                $goodsView = $goods->getGoodsView($request->get()->get('goodsNo'));

                $isMemberOrder = $goodsView['goodsPermission'] != 'all';
            }
            if (gd_policy('member.access.buyAuthGb') == 'member') {
                $isMemberOrder = true;
                $data['guestLogin'] = 'n';
            }
            if ($hasGuestOrder == false) {
                $data['guestLogin'] = 'n';
            }

            // --- 본인인증 없는 휴면해제 전환의 경우 설정 값 초기화
            $data['wakeType'] = $request->get()->get('wakeType', 'normal');
            $sleepPolicy = gd_policy('member.sleep');
            if ($data['wakeType'] !== 'normal' && $sleepPolicy['wakeType'] !== $data['wakeType']) {
                foreach ($sleepPolicy as $key => $value) {
                    $sleepPolicy[$key] = '';
                }
            }

            // 로그인 아이디 저장 쿠키
            if ($data[MemberUtil::COOKIE_LOGIN_ID]) {
                $data['loginId'] =  $data[MemberUtil::COOKIE_LOGIN_ID];
            }

            // 마이앱 로그인 url
            $siteLink = new SiteLink();
            $loginActionUrl = $siteLink->link('../member/login_ps.php', 'ssl');
            if ($request->isMyapp()) {
                $loginActionUrl = $siteLink->link('../member/myapp/myapp_login.php', 'ssl');
            }

            // 로그인 계정 정보 암호화 처리 (보안 이슈)
            $this->setData('secretKey', md5(self::SECRET_KEY));
            $this->addScript(['../../../../crypto-js/pbkdf2.js', '../../../../crypto-js/aes.js', '../../../../crypto-js/sha512.js']);

            $paycoPolicy = new PaycoLoginPolicy();
            $this->setData('loginActionUrl', $loginActionUrl);
            $this->setData('sleepPolicy', $sleepPolicy);
            $this->setData('data', $data);
            $this->setData('saveId', MemberUtil::KEY_SAVE_LOGIN_ID);
            $this->setData('returnUrl', urlencode($returnUrl));
            $this->setData('modeLogin', LoginPsController::MODE_LOGIN);
            $this->setData('usePaycoLogin', $paycoPolicy->usePaycoLogin());
            $this->setData('useKakaoLogin', $kakaoPolicy->useKakaoLogin());
            $this->setData('useFacebookLogin', $useFacebook);
            $this->setData('useNaverLogin', $useNaver);
            $this->setData('useWonderLogin', $useWonderLogin);
            $this->setData('useGoogleLogin', $useGoogleLogin);
            $this->setData('hasGuestOrder', $hasGuestOrder);
            $this->setData('isMemberOrder', $isMemberOrder);
            $this->setData('orderNoMaxLength', 16);
            $this->setData('isMyapp', $request->isMyapp());
            $this->addScript($scripts);

            if ($request->isFromMyapp()) {
                \Logger::channel('myapp')->info("Myapp add loginView(NONE) returnUrl is : " . $returnUrl);

                $loginViewType = 'NONE';
                $showGuestOrder = false;
                $loginViewReferer = $returnUrl;
                if (strpos($loginViewReferer, '/order/order.php')) {
                    $session = \App::getInstance('session');
                    if (!$session->has('guest')) {
                        $showGuestOrder = true;
                    }

                    if (strpos($loginViewReferer, '?') !== false) {
                        $loginViewReferer .= '&myappGuestOrder=y';
                    } else {
                        $loginViewReferer .= '?myappGuestOrder=y';
                    }
                }

                \Logger::channel('myapp')->info("Myapp add loginView(NONE) bridge script and referer : " . $loginViewReferer);
                $isQuickLoginEnabled = MyappUtil::isQuickLoginEnabled();
                \Logger::channel('myapp')->info("Myapp LoginController add loginView isQuickLoginEnabled: " . ($isQuickLoginEnabled ? 'true' : 'false'));
                $skipBack = !$isQuickLoginEnabled;
                $myappBridge = \App::load(MyappBridge::class);
                $loginViewScript = $myappBridge->loginView($loginViewReferer, $loginViewType, $showGuestOrder, $skipBack);

                if ($isQuickLoginEnabled) {
                    $this->js($loginViewScript);
                    exit;
                }

                echo $myappBridge->wrapScriptTag($loginViewScript);
            }
        } catch (\Exception $e) {
            throw new AlertBackException($e->getMessage());
        }
    }
}
