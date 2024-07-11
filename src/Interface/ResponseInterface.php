<?php

namespace Zus1\Api\Interface;

use Illuminate\Http\Client\Response;

interface ResponseInterface
{
    public function handleResponse(RequestInterface $request, Response $response);
}
