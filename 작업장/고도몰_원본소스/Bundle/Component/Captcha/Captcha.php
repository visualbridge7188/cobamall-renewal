<?php
// @codingStandardsIgnoreFile
/**
 * Captcha class
 * 캡차 출력/검증 클래스
 */

namespace Bundle\Component\Captcha;

use App;
use Session;

class Captcha
{
    public function __construct()
    {
    }

    protected function rgb($color, $basic)
    {
        if ($color == '') $color = $basic;
        $color = str_replace("#", "", $color);
        $c = $c ?? [];
        sscanf($color, "%2x%2x%2x", $c['r'], $c['g'], $c['b']);
        return $c;
    }

    ### 캡차 이미지 출력
    public function output($bgColor, $color)
    {
        ob_start();
        $bc_rgb = $this->rgb($bgColor, 'FFFFFF');
        $fc_rgb = $this->rgb($color, '262626');

        ## Defined
        $canvas = array('w' => 120, 'h' => 40);
        $strpit = array('x' => 7, 'y' => 3);

        ## Get string of Captcha
        $seed = 1;
        /*
         if (isset($_GET['seed']) && is_numeric($_GET['seed'])) {
         $seed = $_GET['seed'];
         }
         */

        $captchaChars = "";
        for ($i = 0; $i < 5; $i++)
        {
            if (($t = mt_rand(0, 1)) == 0) {
                $captchaChars .= mt_rand(1, 9);
            } elseif ($t == 1) {
                $captchaChars .= chr(mt_rand(0, 25) + 65);
            }
        }
        //$captchaGraph = $_SESSION['captchaGraph' . $seed] = md5(md5($captchaChars)); // PHP 버전별 차이를 없애기 위한 방법
        Session::set('captchaGraph' . $seed, md5(md5($captchaChars)));

        ## Create Canvas
        $im = @imageCreateTrueColor($canvas['w'], $canvas['h']);
        $trans_colour = imagecolorallocate($im, $bc_rgb['r'], $bc_rgb['g'], $bc_rgb['b']);
        imagefill($im, 0, 0, $trans_colour);

        ## Paint Arc
        for ($i = 0; $i < 15; $i++)
        {
            $color = imageColorAllocate($im, mt_rand(160, 250), mt_rand(160, 250), mt_rand(200, 250));
            $cx = mt_rand(0, $canvas['w']);
            $cy = mt_rand(0, $canvas['h']);
            $size = mt_rand(5, 20);
            $half = $size / 2;

            # Reset coordinate
            if ($cx < $half) $cx = $half; // Left
            if ($cy < $half) $cy = $half; // Top
            if (($cx + $size) > $canvas['w']) $cx = $canvas['w'] - ($half + 1); // Right
            if (($cy + $size) > $canvas['h']) $cy = $canvas['h'] - ($half + 1); // Bottom

            imageFilledArc($im, $cx, $cy, $size, $size, 0, 360, $color, ($i%2 == 0 ? IMG_ARC_NOFILL : IMG_ARC_EDGED));
        }

        ## Paint String
        $font = imageloadfont(__DIR__. "/captcha.dimurph2.gdf");
        $text_color = imagecolorallocate($im, $fc_rgb['r'], $fc_rgb['g'], $fc_rgb['b']);
        $captchaChars = strtoupper($captchaChars);
        for ($i = 0; $i < strlen($captchaChars); $i++)
        {
            imageChar($im, $font, $strpit['x'], $strpit['y'], $captchaChars[$i], $text_color);
            $strpit['x'] += imagefontwidth($font) - 4; // 폰트 넓이가 커서 조정.
        }
        ## Output Image
        ob_end_clean();

        //header("Content-type: image/png");
        imagePng($im);
        imageDestroy($im);
    }

    ### 캡차 검증
    function verify($captchaKey, $seed = 1)
    {
        $seed = 1;
        ## Checked Exists
        if (!Session::has('captchaGraph' . $seed)) {
            return [
                'code'=>'4001',
                'msg'=>'Fail to verify CAPTCHA. (Not registered session)',
            ];
        }
        if (empty($captchaKey) === true) {
            return [
                'code'=>'4002',
                'msg'=>'Fail to verify CAPTCHA. (captchaKey is empty)',
            ];
        }

        ## Compared Key
        $captcha_key = md5(md5($captchaKey));
        if (strcmp(Session::get('captchaGraph' . $seed), $captcha_key) !== 0) {
            return [
                'code'=>'4003',
                'msg'=>'Fail to verify CAPTCHA. (Not equal)',
            ];
        }

        return [
            'code'=>'0000',
            'msg'=>'Succeed in verifing',
        ];
    }

}
