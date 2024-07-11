<?php

namespace Zus1\Api\Exception;

use App\Services\ApiIntegration\Helper\JsonHelper;
use App\Services\ApiIntegration\Interface\ExceptionHandlerInterface;
use App\Services\ApiIntegration\Interface\RequestInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class DefaultHandler implements ExceptionHandlerInterface
{
    public function __construct(
        private JsonHelper $jsonHelper,
    ){
    }

    public function handleException(Response $response, RequestInterface $request): void
    {
        $body = $response->body();

        if($this->jsonHelper->isJson($body)) {
            $body = json_decode($body, true, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR);
        }

        Log::channel(config('api-integration.error_log_channel'))->error(sprintf(
            'Got exception for api call %s%s',
            $request->getBaseUrl(),
            $request->getEndpoint()),
            Arr::wrap($body),
        );

        $response->throw();
    }
}
