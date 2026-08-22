<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8" />
    <title><?=$_mallName_?> :: 쇼핑몰 관리자 - 고도몰</title>
    <meta name="robots" content="noindex">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="viewport" content="minimal-ui, width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta http-equiv="cleartype" content="on">
    <?php if($gSecure && $gReferrer) { ?>
        <meta name="referrer" content="origin">
    <?php } ?>
    <script type="module" src="<?=$designEditorSdkUrl?>/<?=$designEditorJsVersion?>/main.js"></script>
    <link rel="stylesheet" href="<?=$designEditorSdkUrl?>/<?=$designEditorCssVersion?>/main.css">
</head>
<body>
<div id="root"></div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const baseDir = location.origin + location.pathname.replace(/\/[^/]+$/, '');

        NDE.init('#root', {
            platform: 'godo',
            mallName: '<?=$mallName?>',
            mallDomain: '<?=$mallDomain?>',
            skinName: '<?=$skinName?>',
            skinCode: '<?=$skinCode?>',
            skinSno: <?=$skinSno ?? 0?>,
            skinVersion: '<?=$skinVersion?>',
            skinCountry: '<?=$skinCountry?>',
            skinLiveFl: <?=$skinLiveFl ? 'true' : 'false'?>,
            rootSkinCode: '<?=$rootSkinCode?>',
            requestUrl: baseDir + '/design_editor_ps.php'
        });
    });
</script>
</body>
</html>
