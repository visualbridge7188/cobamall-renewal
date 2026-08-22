<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Exception;
use Framework\Http\Response;
use Origin\Enum\AutoSend\AutoSendSupport;
use Origin\Service\AutoSend\Request\UpdateAutoSendConfigRequest;
use Origin\Service\AutoSend\SaveAutoSendConfigService;
use Request;

class AutoSendPsController extends \Controller\Admin\Controller
{
    public function index() {
        $request = Request::post()->toArray();
        try {
            switch ($request['mode']) {
                case 'updateEnabledFlags':
                    if (!empty($request['codes'])) {
                        /** @var SaveAutoSendConfigService $saveAutoSendConfigService */
                        $saveAutoSendConfigService = \App::getInstance(SaveAutoSendConfigService::class);
                        $saveAutoSendConfigService->updateAutoSendConfig(
                            new UpdateAutoSendConfigRequest(
                                codesWithFlag: $request['codes'],
                            )
                        );
                    }
                    $this->json(['result'=> true, 'message' => '']);
                    break;
                case 'saveRecommendCollapseState':
                    $collapsed = ($request['collapsed'] ?? '') === 'true';
                    AutoSendSupport::saveRecommendCollapseState($collapsed);
                    $this->json(['result' => true, 'message' => '']);
                    break;
                default:
                    break;
            }
        } catch (Exception $exception) {
            // todo logging
            $this->json(['result'=> false, 'message' => $exception->getMessage()]);
        }
    }


}