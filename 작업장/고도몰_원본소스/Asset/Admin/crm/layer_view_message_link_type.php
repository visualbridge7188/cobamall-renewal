<link rel="stylesheet" href="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/css/crm/layer-view-message-link-type.css')?>">

<article class="ncua-content layer-view-message-link-type">
    <div class="ncua-table ncua-table--horizontal">
        <table>
            <colgroup>
                <col width="100px">
                <col >
                <col >
            </colgroup>
            <thead>
                <tr>
                    <th><div></div></th>
                    <th><div>WEB으로 보내기</div></th>
                    <th><div>APP으로 보내기</div></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div>
                            <div>설명</div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div>카카오톡 인앱 브라우저에서 새창으로 열립니다.</div>
                        </div>
                    </td>
                    <td>
                        <div class="ncua-flex ncua-gap-8">
                            <div>
                                <div class="description-title">디바이스에 앱이 설치된 경우</div>
                                <div>- 앱이 실행되어 페이지가 열립니다.</div>
                            </div>
                            <div>
                                <div class="description-title">디바이스에 앱이 설치되지 않은 경우</div>
                                <div>- 카카오톡 인앱 브라우저에서 새창으로 열립니다.</div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <div>링크 입력</div>
                        </div>
                    </td>
                    <td>
                        <div>
                            쇼핑몰 또는 외부 링크의 전체 URL을 입력할 수 있습니다.
                        </div>
                    </td>
                    <td>
                        <div>
                            앱 링크 또는 앱스킴 전체 URI를 iOS, AOS 각각 입력할 수 있습니다.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>입력 화면</div>
                    </td>
                    <td>
                        <div class="input-view-web">
                            <img src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/image/message-link-input-view-web.png')?>" alt="WEB 입력 화면" class="layer-view-message-link-type__input-image">
                        </div>
                    </td>
                    <td>
                        <div class="input-view-app">
                            <img src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/image/message-link-input-view-app.png')?>" alt="APP 입력 화면" class="layer-view-message-link-type__input-image">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>
                            <div>화면 예시</div>
                        </div>
                    </td>
                    <td>
                        <div class="example-view-web">
                            <img src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/image/message-link-type-web-example.png')?>" alt="WEB 화면 예시" class="layer-view-message-link-type__example-image">
                        </div>
                    </td>
                    <td>
                        <div class="example-view-app">
                            <img src="<?=gd_set_browser_cache(PATH_ADMIN_GD_SHARE . 'ncds/image/message-link-type-app-example.png')?>" alt="APP 화면 예시" class="layer-view-message-link-type__example-image">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</article>
