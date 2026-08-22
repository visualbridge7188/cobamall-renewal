<?php

/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */
namespace Bundle\Widget\Front\Share;

/**
 * Class AltchaWidget
 *
 * 자동등록방지(ALTCHA) 위젯 공통 파셜(share/_altcha.html) 렌더 진입점.
 * 정적 마크업만 출력하며, challenge URL 등은 includeWidget 인자로 setData 되어 파셜에서 사용된다.
 *
 * @package Bundle\Widget\Front\Share
 */
class AltchaWidget extends \Widget\Front\Widget
{
    /**
     * @inheritdoc
     */
    public function index()
    {
        // challenge 발급 mode 토큰은 서버 사이드에서 주입
        $this->setData('altchaMode', 'altchaChallenge');
    }
}
