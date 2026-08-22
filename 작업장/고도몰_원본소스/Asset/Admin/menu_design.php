<!-- close/open -->
<div class="js-adminmenu-toggle"></div>
<div class="js-listgroup-toggle"></div>
<!-- //close/open -->

<div class="menu-header <?php echo $naviMenu->cd[0]; ?>">
    <h2><?php echo reset($naviMenu->location); ?></h2>
</div>
<div class="panel ">
    <?php
    $oldDepth = 2;
    foreach ($naviMenu->leftMenus as $leftKey => $leftVal) {
        if (empty($leftVal['sNo']) === false && $naviMenu->accessMenu[0] != 'all') {
            $menuAccessDisplay = true;
            if (gd_array_key_exists($leftVal['sNo'], $naviMenu->accessMenu) === false) {
                $menuAccessDisplay = false;
            } else if (gd_count($naviMenu->accessMenu[$leftVal['sNo']]) < 1) {
                $menuAccessDisplay = false;
            } else if ($leftVal['depth'] == 3 && gd_in_array($leftVal['tNo'], $naviMenu->accessMenu[$leftVal['sNo']]) === false) {
                $menuAccessDisplay = false;
            }
            if ($menuAccessDisplay === false) {
                continue;
            }
        }

        if ($leftVal['depth'] == 2) {
            if ($leftVal['sDisplay'] == 'y') {
                echo $oldDepth != $leftVal['depth'] ? '</ul>' : '';
                ?>
                <div class="panel-heading menu-icon-minus <?php echo $naviMenu->menuSelected['mid'][$leftVal['sNo']]; ?>"><?php echo $leftVal['sName']; ?></div>
                <?php
                $oldDepth = 2;
            }
        } else if ($leftVal['depth'] == 3) {
            if ($leftVal['tDisplay'] == 'y') {
                echo $oldDepth != $leftVal['depth'] ? '<ul class="list-group">' : '';
                ?>
                <li <?php if ($leftVal['tName'] === 'Web FTP') : ?>id="tutorial-step-guide-web-ftp"<?php endif; ?> class="list-group-item <?php echo $naviMenu->menuSelected['this'][$leftVal['tNo']]; ?>">
                    <a href="<?php echo $leftVal['tUrl']; ?>"><?php echo $leftVal['tName']; ?></a> <?php echo $leftVal['levelLimit']; ?>
                    <?php if (in_array($leftVal['tNo'], $adminMenuFlagService->getAdminMenuFlagsFromSession())): ?>
                        <span class="new-menu">N</span>
                    <?php endif; ?>
                </li>
                <?php
                $oldDepth = 3;
            }
        }
    }
    ?>
    </ul>
</div>
