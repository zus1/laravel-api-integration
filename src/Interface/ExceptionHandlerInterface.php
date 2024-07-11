<?php

namespace Zus1\Api\Interface;

use Illuminate\Http\Client\Response;

interface ExceptionHandlerInterface
{
    public function handleException(Response $response, RequestInterface $request): void;
}
