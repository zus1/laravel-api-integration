<?php

namespace Zus1\Api\DataMapper;

use App\Services\ApiIntegration\Dto\ApiModel;
use App\Services\ApiIntegration\Interface\DataMapperInterface;
use Illuminate\Support\Collection;

/**
 * Implementation of null object pattern
 * https://designpatternsphp.readthedocs.io/en/latest/Behavioral/NullObject/README.html
 */
class NullDataMapper implements DataMapperInterface
{
    public function map(array $data): ApiModel|Collection
    {
        return new ApiModel();
    }
}
