<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Repository;

use Repository\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected string $model;
    
    /**
     * INSERT INTO ($this->model) VALUES (?, ?, ...)
     *
     * @param array $insertData
     * @return void
     */
    public function insert(array $insertData): void
    {
        ($this->model)::query()->insert($insertData);
    }

    /**
     * DELETE FROM ($this->model)
     *
     * @return void
     */
    public function delete(): void
    {
        ($this->model)::query()->delete();
    }

    /**
     * TRUNCATE ($this->model)
     *
     * @return void
     */
    public function truncate(): void
    {
        ($this->model)::query()->truncate();
    }
}
