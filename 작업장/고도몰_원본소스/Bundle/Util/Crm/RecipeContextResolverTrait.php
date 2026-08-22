<?php
/*
 * Copyright (C) 2026 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Util\Crm;

trait RecipeContextResolverTrait
{
    protected function resolveRecipeContext(): array
    {
        $raw = (string) \Request::post()->get('recipeContext', '');
        if ($raw === '') {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * 레시피 컨텍스트에서 호출되었는지 여부 (데이터가 비어 있어도 파라미터가 전달되면 레시피 화면)
     */
    protected function hasRecipeContext(): bool
    {
        return (string) \Request::post()->get('recipeContext', '') !== '';
    }
}
