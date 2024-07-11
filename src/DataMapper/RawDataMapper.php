<?php

namespace Zus1\Api\DataMapper;

use App\Services\ApiIntegration\Dto\ApiModel;
use App\Services\ApiIntegration\Dto\RawApiModel;
use App\Services\ApiIntegration\Interface\DataMapperInterface;
use Illuminate\Support\Collection;

class RawDataMapper implements DataMapperInterface
{

    public function map(array $data): ApiModel|Collection
    {
        $model = new RawApiModel();
        $model->setData($data);

        return $model;
    }
}
