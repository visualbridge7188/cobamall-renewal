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

namespace Bundle\Controller\Front\Member\Naver;

use Bundle\Component\Attendance\Exception\AttendanceValidationException;
use Component\Attendance\AttendanceCheckLogin;
use Component\Godo\GodoNaverServerApi;
use Component\Member\Member;
use Component\Member\MemberSnsService;
use Component\Member\MyPage;
use Component\Member\Util\MemberUtil;
use Exception;
use Framework\Debug\Exception\AlertCloseException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\AlertRedirectCloseException;
use Framework\Utility\StringUtils;
use Message;
use Origin\Service\MyApp\MyappAuth;
use Origin\Service\MyApp\MyappBridge;
use Origin\Service\MyApp\MyappUtil;
use Request;
use Session;

/**
 * 네이버 아이디 로그인 컨트롤러
 * @package Bundle\Controller\Front\Member\Naver
 */
class NaverLoginController extends \Controller\Front\Controller
{
    public function index()
    {
        $naverApi = new GodoNaverServerApi();

        $request = \App::getInstance('request');
        $session = \App::getInstance('session');
        $logger = \App::getInstance('logger');

        $logger->info(sprintf('start controller: %s', __METHOD__));
        try {
            // 팝업 부모창(opener) 판정 — 스킨 세대(popup/gd_popup)·skin_version 비의존
            $openerGuard = "window.opener === null || (typeof window.opener.gd_popup !== 'function' && typeof window.opener.popup !== 'function')";

            if ($request->isMyapp() && $request->get('saveAutoLogin')) {
                $tempArray = explode('?', $request->get()->get('saveAutoLogin'));
                $request->get()->set('saveAutoLogin', $tempArray[0]);
                $tempArray = explode('=', $tempArray[1]);
                $request->get()->set($tempArray[0], $tempArray[1]);
            }

            if ($request->isFromMyapp() && $request->get()->has("saveAutoLogin")) {
                $saveAutoLogin = $request->get()->get('saveAutoLogin', 'n');
                $session->set(MyappAuth::MYAPP_ENABLED_AUTO_LOGIN_IN_BY_WEB_PAGE, $saveAutoLogin);
            }

            // 콜백은 고정 redirect_uri로 돌아와 mode 파라미터가 없으므로 state에서 복원
            $mode = $request->get()->get('mode', '');
            if (empty($mode)) {
                $stateData = $naverApi->decodeState((string) $request->get()->get('state', ''));
                $mode = !empty($stateData['mode']) ? $stateData['mode'] : 'login';
            }

            switch ($mode) {
                case 'login':
                    // 동적값은 state에서 복원
                    $state = (string) $request->get()->get('state', '');
                    $stateData = $naverApi->decodeState($state);

                    // state는 외부 입력이므로 비문자열 위조값 방어를 위해 문자열 캐스트
                    $referer = (string) ($stateData['referer'] ?? '');
                    $returlUrl = (string) ($stateData['returnUrl'] ?? '');
                    $naverType = (string) ($stateData['naverType'] ?? '');

                    if($naverApi->hasError()) {
                        throw new Exception($request->get()->get('error_description'));
                    }

                    if($naverApi->isAuthorizationResponse()) {
                        // CSRF: state 토큰 검증
                        if (!$naverApi->verifyState($state)) {
                            throw new Exception(__('잘못된 접근입니다.'));
                        }
                        $naverToken = $naverApi->getToken($request->get()->get('code'));
                        $session->set(GodoNaverServerApi::SESSION_ACCESS_TOKEN, $naverToken);

                        if ($naverApi->isSuccess($naverToken)) {
                            $logger->info('naver api success');
                            $memberSnsService = new MemberSnsService();
                            $memberSns = $memberSnsService->getMemberSnsByUUID($naverToken['response']['id']);

                            // SNS 회원 검증
                            if ($memberSnsService->validateMemberSns($memberSns)) {
                                $logger->info('validateMemberSns pass');
                                if ($session->has(SESSION_GLOBAL_MALL)) {
                                    $mallBySession = $session->get(SESSION_GLOBAL_MALL);
                                    $logger->info(sprintf('has session %s', \Component\Member\Member::SESSION_MEMBER_LOGIN));
                                    if ($memberSns['mallSno'] != $mallBySession['sno']) {
                                        $logger->info(sprintf('member join mall number[%s], mall session sno[%d]', $memberSns['mallSno'], $mallBySession['sno']));
                                        $js = "
                                            alert('" . __('회원을 찾을 수 없습니다.') . "');
                                            if (" . $openerGuard . ") {
                                                location.href='../../main/index.php';
                                            } else {
                                                opener.location.href='../../main/index.php';
                                                self.close();
                                            }
                                        ";
                                        $this->js($js);
                                    }
                                }
                                if ($session->has(Member::SESSION_MEMBER_LOGIN)) {
                                    if ($memberSns['memNo'] != $session->get(Member::SESSION_MEMBER_LOGIN . '.memNo', 0)) {
                                        $logger->info('not eq memNo');
                                        $js = "
                                            alert('" . __('로그인 시 인증한 정보와 다릅니다 .') . "');
                                            if (" . $openerGuard . ") {
                                                location.href='../../mypage/my_page_password.php';
                                            } else {
                                                opener.location.href='../../mypage/my_page_password.php';
                                                self.close();
                                            }
                                        ";
                                        $this->js($js);
                                    }
                                    if (StringUtils::contains($referer, 'my_page_password') || gd_in_array($naverType, ['my_page_password', 'hack_out']) === true) {
                                        if ($naverType == 'my_page_password') {
                                            $logger->info('move my page');
                                            $session->set(MyPage::SESSION_MY_PAGE_PASSWORD, true);
                                            $js = "
                                                if (" . $openerGuard . ") {
                                                    location.href='../../mypage/my_page.php';
                                                } else {
                                                    opener.location.href='../../mypage/my_page.php';
                                                    self.close();
                                                }
                                            ";
                                        } elseif ($naverType == 'hack_out') {
                                            $logger->info('move hack out');
                                            $session->set(GodoNaverServerApi::SESSION_NAVER_HACK, true);
                                            $js = "
                                                if (" . $openerGuard . ") {
                                                    location.href='../../mypage/hack_out.php';
                                                } else {
                                                    opener.location.href='../../mypage/hack_out.php';
                                                    self.close();
                                                }
                                            ";
                                        }
                                        $this->js($js);
                                    }
                                    $logger->info('move main or reload');
                                    $js = "
                                        alert('" . __('회원으로 로그인 된 상태입니다.') . "');
                                        if (" . $openerGuard . ") {
                                            location.href='../../main/index.php';
                                        } else {
                                            opener.location.href='../../main/index.php';
                                            self.close();
                                        }
                                    ";
                                    $this->js($js);
                                }
                                $memberSnsService->saveToken($naverToken['response']['id'], $naverToken['access_token'], $naverToken['refresh_token']);
                                $logger->info('success save Token');
                                $memberSnsService->loginBySns($naverToken['response']['id']);
                                $logger->info('success login by sns');
                                $naverApi->logByLogin();
                                $logger->info('success send naver api loin log');

                                $request = \App::getInstance('request');
                                if ($request->isFromMyapp()) {
                                    $snsProvider = 'naver';
                                    $session = \App::getInstance('session');
                                    $memberData = $session->get(Member::SESSION_MEMBER_LOGIN);

                                    $myappAuth = \App::load(MyappAuth::class);
                                    $solutionAuthenticationKey = $myappAuth->createAuthenticationKey($memberData);

                                    if (StringUtils::contains($referer, 'join_method') || $naverType == 'join_method') {
                                        $nextUrl = MyappUtil::getHomeUrl();
                                    } else {
                                        $nextUrl = MemberUtil::getLoginReturnURL();
                                    }

                                    $myappBridge = \App::load(MyappBridge::class);
                                    $autoLogin = MyappUtil::getEnabledAutoLoginSession();
                                    $this->js($myappBridge->login($memberData, $solutionAuthenticationKey, $request->getReferer(), $nextUrl, $snsProvider, false, $autoLogin));
                                    exit;
                                }

                                $db = \App::getInstance('DB');
                                try {
                                    $db->begin_tran();
                                    $check = new AttendanceCheckLogin();
                                    $message = $check->attendanceLogin();
                                    $db->commit();

                                    $logger->info('commit attendance login');
                                    if ($message) {
                                        $logger->info(sprintf('has attendance message: %s', $message));
                                        $js = "
                                            alert('" . $message . "');
                                            if (" . $openerGuard . ") {
                                                location.href='" . MemberUtil::getLoginReturnURL() . "';
                                            } else {
                                                opener.location.href='" . MemberUtil::getLoginReturnURL() . "';
                                                self.close();
                                            }
                                        ";
                                        $this->js($js);
                                    }
                                } catch (AttendanceValidationException $e) {
                                    $db->rollback();
                                    $logger->warning(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage());
                                } catch (Exception $e) {
                                    $db->rollback();
                                    $logger->error(__METHOD__ . ', ' . $e->getFile() . '[' . $e->getLine() . '], ' . $e->getMessage());
                                }

                                if (StringUtils::contains($referer, 'join_method') || $naverType == 'join_method') {
                                    $logger->info('join member');
                                    $js = "
                                        alert('" . __('이미 가입한 회원입니다.') . "');
                                        if (" . $openerGuard . ") {
                                            location.href='../../member/login.php';
                                        } else {
                                            opener.location.href='../../member/login.php';
                                            self.close();
                                        }
                                    ";
                                    $this->js($js);
                                }
                                $logger->info('move return url');

                                // $returlUrl 이 있을경우 $returlUrl을 사용하도록 수정 MemberUtil::getLoginReturnURL() 의 경우 메인으로 리턴함
                                if ($returlUrl) {
                                    $loginReturnURL = $returlUrl;
                                } else {
                                    $loginReturnURL = MemberUtil::getLoginReturnURL();
                                }

                                // location.href JS 문자열 컨텍스트 인젝션 방지: URL을 JSON 리터럴로 인코딩
                                $loginReturnUrlJs = json_encode((string) $loginReturnURL, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                                $js = "
                                    if (typeof(window.top.layerSearchArea) == 'object') {
                                        parent.location.href=" . $loginReturnUrlJs . ";
                                    } else if (window.opener === null) {
                                        location.href=" . $loginReturnUrlJs . ";
                                    } else {
                                        opener.location.href=" . $loginReturnUrlJs . ";
                                        self.close();
                                    }
                                ";
                                $this->js($js);
                            }
                            // 성인인증 로그인 시도 회원정보 없음
                            if (StringUtils::contains($referer, 'intro/adult')) {
                                $js = "
                                    if (" . $openerGuard . ") {
                                        if (confirm('" . __('가입되지 않은 회원정보입니다. 회원가입을 진행하시겠습니까?') . "')) {
                                            location.href = '../join_agreement.php';
                                        } else {
                                            location.href='/intro/adult.php';
                                        }
                                    } else {
                                        if(confirm('" . __('가입되지 않은 회원정보입니다. 회원가입을 진행하시겠습니까?') . "')) {
                                            window.opener.location.href='../join_agreement.php';
                                        }
                                        self.close();
                                    }
                                ";
                                $this->js($js);
                            }
                            // 회원가입에서의 로그인 약관 동의 화면 이동
                            if (StringUtils::contains($referer, 'member/join_method') || $naverType == 'join_method') {
                                $js = "
                                    if (" . $openerGuard . ") {
                                        location.href='../join_agreement.php';
                                    } else {
                                        opener.location.href='../join_agreement.php';
                                        self.close();
                                    }
                                ";
                                $this->js($js);
                            }
                            // 내정보수정에서의 로그인 sns 인증 계정 다를 경우
                            if (StringUtils::contains($referer, 'mypage/my_page_password')) {
                                $js = "
                                    alert('" . __('가입된 계정이 아닙니다. 가입하신 계정으로 재인증 진행해주세요.') . "');
                                    if (" . $openerGuard . ") {
                                        location.href='" . $request->getReferer() . "';
                                    } else {
                                        self.close();
                                    }
                                ";
                                $this->js($js);
                            }
                            // 회원가입을 하지 않은 경우
                            $js = "
                                if (" . $openerGuard . ") {
                                    if (confirm('" . __('가입되지 않은 회원정보입니다. 회원가입을 진행하시겠습니까?') . "')) {
                                        location.href = '../join_agreement.php';
                                    } else {
                                        location.href = '../login.php';
                                    }
                                } else {
                                    if(confirm('" . __('가입되지 않은 회원정보입니다. 회원가입을 진행하시겠습니까?') . "')) {
										window.opener.location.href='../join_agreement.php';
									}
                                    self.close();
                                }
                            ";
                            $this->js($js);
                        }
                        // 응답 실패인 경우
                        $js = "
                            if (" . $openerGuard . ") {
                                location.href='../join_method.php';
                            } else {
                                opener.location.href='../join_method.php';
                                self.close();
                            }
                        ";
                        $this->js($js);
                    }

                    // redirect_uri는 등록 콜백URL과 동일한 고정 경로 (strict match)
                    $redirectUri = $request->getDomainUrl() . parse_url($request->getRequestUri(), PHP_URL_PATH);

                    // 동적값은 state로 이관
                    $state = $naverApi->createState([
                        'referer'   => $request->getReferer(),
                        'returnUrl' => MemberUtil::getLoginReturnURL(),
                        'naverType' => $request->get()->get('naverType', ''),
                    ]);

                    $logger->info(sprintf('NaverLogin Return Login. redirect_uri is %s', $redirectUri));
                    $loginURL = $naverApi->getLoginURL($redirectUri, $state);

                    $logger->info(sprintf('Redirect naver login. url is %s', $loginURL));
                    $this->redirect($loginURL);
                    break;
                case 'connect':
                    if($naverApi->hasError()) {
                        throw new Exception($request->get()->get('error_description'));
                    }
                    if (Request::get()->has('code') && Request::get()->has('state')) {
                        // connect도 동일하게 state 검증 (CSRF)
                        if (!$naverApi->verifyState((string) Request::get()->get('state'))) {
                            throw new Exception(__('잘못된 접근입니다.'));
                        }
                        $NaverToken = $naverApi->getToken(Request::get()->get('code'));
                        Session::set(GodoNaverServerApi::SESSION_ACCESS_TOKEN, $NaverToken);
                        if ($naverApi->isSuccess($NaverToken)) {
                            $memberSnsService = new MemberSnsService();
                            if ($memberSnsService->hasSnsMember($NaverToken['response']['id'])) {
                                $js = "
                                    alert('" . __('이미 다른 회원정보와 연결된 계정입니다. 다른 계정을 이용해주세요.') . "');
                                    if (" . $openerGuard . ") {
                                        location.href='../../main/index.php';
                                    } else {
                                        self.close();
                                    }
                                ";
                                $this->js($js);
                            }
                            $userProfile = $naverApi->getUserProfile($NaverToken);
                            $memberSnsService->connectSns(Session::get(Member::SESSION_MEMBER_LOGIN . '.memNo'), $userProfile['id'], $NaverToken['access_token'], 'naver');
                            $naverApi->logByLink();
                            $js = "
                                alert('" . __('계정 연결이 완료되었습니다. 로그인 시 연결된 계정으로 로그인 하실 수 있습니다.') . "');
                                if (" . $openerGuard . ") {
                                    location.href='../../mypage/my_page.php';
                                } else {
                                    opener.location.href='../../mypage/my_page.php';
                                    self.close();
                                }
                            ";
                            $this->js($js);
                        }
                    }
                    // redirect_uri는 등록 콜백URL과 동일한 고정 경로 (strict match), mode는 state로 복원
                    $returnURL = Request::getDomainUrl() . parse_url(Request::getRequestUri(), PHP_URL_PATH);
                    $state = $naverApi->createState(['mode' => 'connect']);
                    $loginURL = $naverApi->getLoginURL($returnURL, $state);
                    $this->redirect($loginURL);
                    break;
                case 'disconnect':
                    if($naverApi->hasError()) {
                        throw new Exception($request->get()->get('error_description'));
                    }
                    $member = $session->get(Member::SESSION_MEMBER_LOGIN);
                    if ($member['snsJoinFl'] == 'y') {
                        $logger->info('Impossible disconnect member joined by naver');
                        $this->json(
                            [
                                'error'   => 'naver',
                                'message' => __('네이버로 가입한 회원님은 연결을 해제 할 수 없습니다.'),
                            ]
                        );
                    }

                    if ($session->has(GodoNaverServerApi::SESSION_ACCESS_TOKEN)) {
                        $logger->info('Has naver access token');
                        $naverToken = $session->get(GodoNaverServerApi::SESSION_ACCESS_TOKEN, []);
                        $logger->debug('session access token', $naverToken);
                        $session->del(GodoNaverServerApi::SESSION_ACCESS_TOKEN);
                        $memberSnsService = new MemberSnsService();
                        $memberSnsService->disconnectSns($member['memNo']);
                        $naverApi->logByDrop();
                        $logger->info('Disconnect naver');
                        $this->json(
                            [
                                'message' => __('네이버 연결이 해제되었습니다.'),
                                'url'     => '../mypage/my_page.php',
                            ]
                        );
                    } else {
                        $logger->info('Disconnect naver fail. not found disconnect information');
                        $this->json(
                            [
                                'error'   => 'naver',
                                'message' => __('네이버 연결해제에 필요한 정보를 찾을 수 없습니다.'),
                                'url'     => '../mypage/my_page_password.php',
                            ]
                        );
                    }
                    break;
            }
        } catch (AlertRedirectCloseException $e) {
            throw $e;
        } catch(\Exception $e) {
            switch (isset($mode) ? $mode : 'login') {
                case 'login':
                    if (Request::isMobile()) {
                        $redirectUrl = '../login.php';
                        if ($request->isFromMyapp()) {
                            $error = $request->get()->get('error');
                            $errorDescription = $request->get()->get('error_description');
                            // 최초 로그인 및 회원 가입 시, 약관 페이지에서 취소 버튼 클릭 시 홈으로 이동 처리 (페이지 루프 이슈 개선)
                            if ($error == 'access_denied' && $errorDescription == 'Canceled By User') {
                                $redirectUrl = MyappUtil::getHomeUrl();
                            }
                        }
                        throw new AlertRedirectException($e->getMessage(), null, null, $redirectUrl);
                    } else {
                        throw new AlertCloseException($e->getMessage(), $e->getCode(), $e);
                            /*
                            if ($e->getTarget() == 'opener') {
                                throw $e;
                            } else {
                                throw new AlertCloseException($e->getMessage(), $e->getCode(), $e);
                            }
                            */
                    }
                    break;
                case 'connect':
                    MemberUtil::logoutNaver();
                    throw new AlertCloseException($e->getMessage(), $e->getCode(), $e);
                    break;
                case 'disconnect':
                    break;
            }
        }
    }
}
