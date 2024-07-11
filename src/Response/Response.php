<?php

namespace Zus1\Api\Response;

use Zus1\Api\DataMapper\NullDataMapper;
use Zus1\Api\DataMapper\RawDataMapper;
use Zus1\Api\Exception\DefaultHandler;
use Zus1\Api\Helper\JsonHelper;
use Zus1\Api\Interface\DataMapperInterface;
use Zus1\Api\Interface\ExceptionHandlerInterface;
use Zus1\Api\Interface\RequestInterface;
use Zus1\Api\Interface\ResponseInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Zus1\Api\Dto\ApiModel;

class Response implements ResponseInterface
{
    public function __construct(
        private JsonHelper $jsonHelper
    ){
    }

    public function handleResponse(RequestInterface $request, \Illuminate\Http\Client\Response $response): ApiModel|Collection
    {
        if($response->successful() === false) {
            $exceptionHandler = $this->determineExceptionHandler($request);

            $exceptionHandler->handleException($response, $request);
        }

        if($response->noContent()) {
            /** @var DataMapperInterface $dataMapper */
            $dataMapper = App::make(NullDataMapper::class);

            return $dataMapper->map([]);
        }

        $body = $response->body();

        if($this->jsonHelper->isJson($body) === false) {
            return $this->handleNonJsonResponse($body);
        }

        $decoded = json_decode($body, true, JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR);;

        $dataMapper = $this->determineDataMapper($request);

        return $dataMapper->map($decoded);
    }

    private function handleNonJsonResponse(string $body): ApiModel
    {
        /** @var DataMapperInterface $dataMapper */
        $dataMapper = App::make(RawDataMapper::class);

        return $dataMapper->map(Arr::wrap($body));
    }

    private function determineExceptionHandler(RequestInterface $request): ExceptionHandlerInterface
    {
        $handlers = (array) config('api-integration.exception_handlers');

        /** @var ExceptionHandlerInterface $exceptionHandler */
        $exceptionHandler = isset($handlers[$request::class]) === false ?
            App::make(DefaultHandler::class) : App::make($handlers[$request::class]);

        return $exceptionHandler;
    }

    private function determineDataMapper(RequestInterface $request): DataMapperInterface
    {
        $mappers = (array) config('api-integration.data_mappers');

        if(!isset($mappers[$request::class])) {
            throw new HttpException(
                500,
                'Unknown data mapper, did you forgot to add it to api-integration config file?'
            );
        }

        /** @var DataMapperInterface $dataMapper */
        $dataMapper = App::make($mappers[$request::class]);

        return $dataMapper;
    }
}
