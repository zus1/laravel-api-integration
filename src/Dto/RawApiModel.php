<?php

namespace Zus1\Api\Dto;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RawApiModel extends ApiModel
{
    protected array $data;

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function get(string $key): mixed
    {
        $keyArr = explode('.', $key);

        $value = $this->data;

        foreach ($keyArr as $keyPart) {
            if(!array_key_exists($keyPart, $value)) {
                throw new HttpException(500, sprintf(
                    'Unknown nested key %s. Please check did you compose you key correctly',
                    $keyPart
                ));
            }

            $value = $this->data[$keyPart];
        }

        return $value;
    }

    public function getString(): string
    {
        return $this->data[0];
    }
}
