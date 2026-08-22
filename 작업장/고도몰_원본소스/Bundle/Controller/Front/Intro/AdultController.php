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

namespace Bundle\Controller\Front\Intro;

use Bundle\Component\Policy\AppleLoginPolicy;
use Bundle\Component\Policy\GoogleLoginPolicy;
use Bundle\Component\Policy\KakaoLoginPolicy;
use Component\Facebook\Facebook;
use Component\Member\Util\MemberUtil;
use Component\Policy\PaycoLoginPolicy;
use Component\Policy\SnsLoginPolicy;
use Component\Policy\NaverLoginPolicy;
use Component\Policy\WonderLoginPolicy;
use Component\Godo\GodoNaverServerApi;
use Component\Godo\GodoWonderServerApi;
use Component\SiteLink\SiteLink;
use Framework\Debug\Exception\AlertOnlyException;
use Globals;
use Origin\Service\MyApp\MyappAuth;
use Origin\Service\MyApp\MyappBridge;
use Origin\Service\MyApp\MyappUtil;
use Request;
use Session;

/**
 * 인트로 - 성인
 * @author Shin Donggyu <artherot@godo.co.kr>
 */
class AdultController extends \Controller\Front\Controller
{
    const DEPRECATED_PATH_MODULE_SCRIPT = "DEPRECATED_PATH_MODULE_SCRIPT";
    /**
     * index
     *
     */
    public function index()
    {
        $request = \App::getInstance('request');
        if ($request->isFromMyapp() && !MemberUtil::isLogin()) {
            $returnUrl = $request->get()->get('returnUrl', $request->getReferer());
            if (!empty($returnUrl)) {
                $returnUrl = MyappUtil::getUri($returnUrl);
            }
            $isQuickLoginEnabled = MyappUtil::isQuickLoginEnabled();
            \Logger::channel('myapp')->info("Myapp AdultController add loginView isQuickLoginEnabled: " . ($isQuickLoginEnabled ? 'true' : 'false'));

            $myappBridge = \App::load(MyappBridge::class);
            $loginViewScript = $myappBridge->loginView($returnUrl, 'ONLY_ADULT');

            if ($isQuickLoginEnabled) {
                $this->js($loginViewScript);
                exit;
            }

            echo $myappBridge->wrapScriptTag($loginViewScript);
        }

        $scripts = ['gd_payco.js'];
        try {
            /** @var \Bundle\Controller\Front\Controller $this */
            $returnUrl = MemberUtil::getLoginReturnURL();
            $siteLink = new SiteLink();
            $this->setData('loginActionUrl', $siteLink->link('../member/member_ps.php', 'ssl'));
            $this->setData('returnUrl', $returnUrl);        // 스킨에서 오류가 나서 추가
            $this->setData('domainUrl', Request::getDomainUrl());
            $this->setData('ipinFl', gd_use_ipin());
            $this->setData('authCellphoneFl', gd_use_auth_cellphone());
            $this->setData('authShopUrl', URI_AUTH_PHONE_MODULE);
            $this->setData('authDataCpCode', gd_get_auth_cellphone());
            $this->setData(
                [
                    'script' => [
                        ['url' => self::DEPRECATED_PATH_MODULE_SCRIPT . 'jquery.number_only.js']
                        ,
                        ['url' => self::DEPRECATED_PATH_MODULE_SCRIPT . 'identification.js'],
                    ],
                ]
            );
            if (Session::has('member')) {
                $this->getView()->setPageName('intro/adult_member');
            } else {
                $this->getView()->setPageName('intro/adult_guest');
                $paycoPolicy = new PaycoLoginPolicy();
                $usePaycoLogin = $paycoPolicy->usePaycoLogin();
                $this->setData('usePaycoLogin', $usePaycoLogin);

                $snsLoginPolicy = new SnsLoginPolicy();
                $useFacebook = $snsLoginPolicy->useFacebook();
                $this->setData('useFacebookLogin', $useFacebook);

                if ($useFacebook) {
                    $facebook = new Facebook();
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
                if($useNaver) {
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
                $this->setData('useNaverLogin', $useNaver);

                $kakaoPolicy = \App::getInstance(KakaoLoginPolicy::class);
                $useKakao = $kakaoPolicy->useKakaoLogin();
                if($useKakao) {
                    $scripts[] = 'gd_kakao.js';
                }
                $this->setData('useKakaoLogin', $useKakao);

                $wonderPolicy = new WonderLoginPolicy();
                $useWonderLogin = $wonderPolicy->useWonderLogin();
                if ($useWonderLogin) {
                    $wonder = new GodoWonderServerApi();
                    $scripts[] = 'gd_wonder.js';
                    $this->setData('useWonderLogin', $useWonderLogin);
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

                    $googleReturnUrl = $returnUrl;
                    if ($request->isFromMyapp()) {
                        $tokenData = ['next' => $returnUrl];
                        $token = MyappUtil::encryptCustomTabToken($tokenData);
                        $googleReturnUrl = MyappUtil::getCustomTabUrl('google', MyappAuth::CUSTOM_TAB_LOGIN_ACTIVE, $token);
                    }
                    $this->setData('googleReturnUrl', $googleReturnUrl);
                }
                $this->setData('useGoogleLogin', $useGoogleLogin);
            }
        } catch (\Exception $e) {
            \Logger::error(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage(), $e->getTrace());
            throw new AlertOnlyException($e->getMessage(), $e->getCode(), $e);
        } finally {
            $this->addScript($scripts);
        }
    }
}
