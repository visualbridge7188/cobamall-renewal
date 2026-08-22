<link type="text/css" href="<?= gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-preview.css') ?>" rel="stylesheet"/>

<div class="mobile-preview-wrap">
    <section class="mobile-preview mobile-preview--myapp">
        <span class="ncua-profile ncua-profile--float ncua-profile--myapp"></span>
        <figure class="ncua-prev-myapp">
            <figcaption class="ncua-prev-myapp__caption">
                <?= gd_htmlspecialchars_stripslashes($title) ?><br />
                <?= nl2br(gd_htmlspecialchars_stripslashes($content)) ?><br />
                <?= $unsubscribeGuide ?>
            </figcaption>
            <?php if (!empty($imageUrl)): ?>
                <span class="ncua-prev-myapp__media">
                    <img src="<?= $imageUrl ?>" alt="" />
                </span>
            <?php endif; ?>
        </figure>
    </section>
    <section class="mobile-preview-table">
        <div class="ncua-table ncua-table--vertical">
            <table>
                <tbody>
                    <tr>
                        <th><div>푸시 URL</div></th>
                        <td><div class="ncua-left-align"><?= $pushUrl ?></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>


