<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Crm;

use Framework\Http\Request;
use Origin\Service\Crm\CrmRecipeClickLogCollectorService;

class CrmRecipeClickLogPsController extends \Controller\Admin\Controller
{
    private const ALLOWED_ACTIONS = [
        CrmRecipeClickLogCollectorService::ACTION_SEND,
        CrmRecipeClickLogCollectorService::ACTION_CLOSE,
    ];

    public function index()
    {
        $this->callMenu('crm', 'crm', 'crmRecipe');
        $this->setMenuCode('crm', 'crm', 'crmRecipe');

        /** @var Request $request */
        $request = \App::getInstance('request');
        $postData = $request->post()->toArray();

        $action = $postData['action'] ?? '';
        if (!in_array($action, self::ALLOWED_ACTIONS, true)) {
            $this->json(['success' => false]);
            return;
        }

        /** @var CrmRecipeClickLogCollectorService $clickLogCollector */
        $clickLogCollector = \App::getInstance(CrmRecipeClickLogCollectorService::class);
        $clickLogCollector->collect([
            'action' => $action,
            'recipeType' => $postData['recipeType'] ?? '',
            'currentPage' => $postData['currentPage'] ?? '',
        ]);

        $this->json(['success' => true]);
    }
}
