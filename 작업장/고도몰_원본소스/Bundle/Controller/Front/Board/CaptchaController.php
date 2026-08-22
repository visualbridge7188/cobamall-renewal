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
 * @link http://www.godo.co.kr
 */
namespace Bundle\Controller\Front\Board;

use Bundle\Component\Captcha\Captcha;
use Request;
use Framework\Utility\Strings;

class CaptchaController extends \Controller\Front\Controller
{

    public function index()
    {
        try {
            // ALTCHA 위젯이 GET 으로 챌린지를 요청하면 JSON 으로 응답
            if (Request::get()->get('mode') === 'altchaChallenge') {
                $this->json(\Bundle\Component\Board\BoardUtil::getAltchaChallenge());
            }

            $captcha = new Captcha();

            $db = \App::load('DB');
            $getData = array();
            $sno = Request::get()->get('sno');
            $arrBind=[];
            if ($sno && is_numeric($sno)) {
                $query = "SELECT bdCaptchaBgClr, bdCaptchaClr FROM " . DB_BOARD . " WHERE sno = ?";
                $db->bind_param_push($arrBind, 'i', $sno);
                $getData = $db->query_fetch($query, $arrBind, false);
            } else {
                if (Request::get()->get('bgColor')) {
                    $getData['bdCaptchaBgClr'] = Request::get()->get('bgColor');
                }
                if (Request::get()->get('color')) {
                    $getData['bdCaptchaClr'] = Request::get()->get('color');
                }
            }

            $captcha->output($getData['bdCaptchaBgClr'], $getData['bdCaptchaClr']);
            exit;
        } catch (\Exception $e) {
            debug($e->getMessage());
        }
    }
}
