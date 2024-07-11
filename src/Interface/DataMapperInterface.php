<?php

namespace Zus1\Api\Interface;

use Zus1\Api\Dto\ApiModel;
use Illuminate\Support\Collection;

interface DataMapperInterface
{
    /**
     * @return ApiModel|ApiModel[]
     */
    public function map(array $data): ApiModel|Collection;
}
