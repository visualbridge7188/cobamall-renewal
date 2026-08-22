<link type="text/css" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/board/plus-preview.css')?>" rel="stylesheet"/>

<div class="preview-layer">
    <header class="preview-layer-head">
        <div class="preview-layer-head-image">
            <img src="<?=$data['goodsImageSrc']?>" alt="<?=$data['goodsNm']?>">
        </div>
        <div class="preview-layer-head-info">
            <div><b><?=$data['goodsNm']?></b><br><?=gd_currency_display($data['goodsPrice'])?></div>
        </div>
        <div class="clear-both"></div>
    </header>
    <main class="preview-layer-scroll">
        <!-- ncua 미적용 ( 기존디자인 유지 )-->
        <table class="table table-cols">
            <colgroup>
                <col class="width-lg">
            </colgroup>
            <tbody>
            <tr>
                <th>
                    <?php foreach($data['addFormData'] as $key=>$val) {?>
                        <div class="pdt5"><?=$key?> : <?=$val?></div>
                    <?php }?>
                    <?php foreach($data['option'] as $val) {?>
                    <div class="pdt5"><?=$val['name']?> : <?=$val['value']?></div>
                    <?php }?>
                </th>
            </tr>
            <tr>
                <td>
                    <?= $data['viewContents'] ?>
                </td>
            </tr>
            </tbody>
        </table>
    </main>
    <div class="preview-layer-contents-length"><?=mb_strlen($data['viewContents'])?>/<?=$formCheckMinLength?></div>
    <footer class="preview-layer-uploaded-images">
        <?php
        if ($data['uploadedFile']) {
            ?>
            <ul>
                <?php foreach ($data['uploadedFile'] as $val) { ?>
                    <li>
                        <img src="<?=$val['thumSrc']?>" alt="미리보기 이미지">
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </footer>
</div>
