<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */
namespace Bundle\Controller\Admin\Crm;

use Framework\Debug\Exception\LayerException;
use Logger;
use Request;

class MailGateController extends \Controller\Admin\Controller
{
    public function index()
    {
        // --- 모듈 호출
        $data = null;

        /** @var  \Bundle\Component\Member\MemberAdmin $memberAdmin */
        $memberAdmin = \App::load('\\Component\\Member\\MemberAdmin');
        /** @var \Bundle\Component\Mail\Pmail $pMail */
        $pMail = \App::load('\\Component\\Mail\\Pmail');
        $pMail->setPmail();

        $start = Request::get()->get('start');
        $charge = Request::get()->get('charge');
        $sendTarget = Request::post()->get('sendTarget');
        $chk = Request::post()->get('chk');
        $rejectMailingFl = Request::post()->get('rejectMailingFl');
        if ($start == 'y') {
            exit();
        }

        if ($charge != 'y') {
            Logger::debug(__METHOD__ . ', sendTarget=' . $sendTarget);
            if ($sendTarget == 'query') {
                /** @var  \Bundle\Component\Member\MemberSearchList $memberSearchList */
                $memberSearchList = \App::load('\\Component\\Member\\MemberSearchList');
                $requestPostParams = Request::post()->all();

                $tmp = $memberAdmin->searchMemberWhere($requestPostParams);
                $arrBind = $tmp['arrBind'];
                $search = $tmp['search'];
                $arrWhere = $tmp['arrWhere'];
                @set_time_limit(RUN_TIME_LIMIT);
                $list = $memberAdmin->getMemberList($arrWhere, 'entryDt desc', $arrBind);

                if (empty($list) === false) {
                    foreach ($list as $val) {
                        // 파워메일 메일링 수신거부자 제외하고 발송 && 개인회원 메일 수신거부 일 경우 continue
                        if ($rejectMailingFl === 'y' && $val['maillingFl'] === 'n') {
                            continue;
                        }
                        $data[] = $val['memNo'];
                    }
                }
                unset($list);
                unset($memberSearchList);
            } else {
                $data = $chk;
            }
        }

        try {
            $getData = $pMail->gateToPmail(Request::getHost());

            if ($data) {
                $pMail->makeList($data);

                echo '<script type="text/javascript">parent.frmMail.submit();</script>';
                exit;
            }

            if ($charge == 'y') {
                $v = '';
                echo '<form name="frmMail" method="post"  action="' . $pMail->login_url . '">' . chr(10);
                if (empty($getData) === false) {
                    foreach ($getData as $key => $val) {
                        echo '<input type="hidden" name="' . $key . '" value="' . $val . '">' . chr(10);
                    }
                }
                echo '</form>';
                echo '<script>document.frmMail.submit();</script>';
                exit();
            }

            if($charge != 'y' && !$data) {

                echo '<script type="text/javascript">alert("' . __('전송가능한 회원이 없습니다.') . '");parent.close();</script>';
                exit();

            }

        } catch (\Exception $e) {
            if ($charge == 'y') {
                echo '<script type="text/javascript">self.close();</script>';
            }
            throw new LayerException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
