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

class MailGateBridgeController extends \Controller\Admin\Controller
{
    public function index()
    {

        $pMail = \App::load('\\Component\\Mail\\Pmail');
        $pMail->setPmail();

        $getData = $pMail->gateToPmail(Request::getHost());

        echo '<div id="progressDiv"style="position:absolute;top:0;left:0;background:#FFFFFF;width:100%;height:700px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;">
        <img src="'.PATH_ADMIN_GD_SHARE.'img/icon_loading.gif" border="0" style="margin-top:280px;"/></div>'. chr(10);

        echo '<form name="frmMail" method="post" action="' . $pMail->login_url . '">' . chr(10);
        if (empty($getData) === false) {
            Logger::debug(__METHOD__, $getData);
            foreach ($getData as $key => $val) {
                echo '<input type="hidden" name="' . $key . '" value="' . $val . '" />' . chr(10);
            }
        }
        echo '</form>'. chr(10);

        echo '<form name="frmMailMake" method="post" target="iframeHidden" action="mail_gate.php">' . chr(10);
        foreach (Request::post()->all() as $key => $value) {
            if(is_array($value)) {
                foreach ($value as $arrValue) {
                    echo '<input type="hidden" name="' . $key . '[]" value="' . $arrValue . '">' . chr(10);
                }
            }else{
                echo '<input type="hidden" name="' . $key . '" value="' . $value . '">' . chr(10);
            }
        }
        echo '</form>'. chr(10);;

        echo '<iframe name="iframeHidden" src="/blank.php" width="100%" height="200" style="visibility:hidden;"></iframe>'. chr(10);
        echo '<script>document.frmMailMake.submit();</script>';

        exit;
    }
}
