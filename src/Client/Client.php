<?php

namespace Zus1\Api\Client;

use Zus1\Api\Interface\RequestInterface;
use Zus1\Api\Interface\ResponseInterface;
use Zus1\Api\Response\Response;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\App;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;

class Client
{
    public function __construct(
        private PendingRequest $pendingRequest
    ){
    }

    public function call(RequestInterface $request)
    {
        if($request->getHeaders() !== []) {
            $this->pendingRequest->withHeaders($request->getHeaders());
        }
        if($request->getQuery() !== []) {
            $this->pendingRequest->withQueryParameters($request->getQuery());
        }
        if($request->getBody() !== []) {
            $this->setBody($request);
        }
        if($request->getOptions() !== []) {
            $this->pendingRequest->withOptions($request->getOptions());
        }

        $response = $this->pendingRequest->send(
            $request->getMethod(),
            sprintf('%s%s', $request->getBaseUrl(), $request->getEndpoint())
        );

        $responseHandler = $this->determineResponseHandler($request);

        return $responseHandler->handleResponse($request, $response);
    }

    private function determineResponseHandler(RequestInterface $request): ResponseInterface
    {
        $handlers = (array) config('api-integration.response_handlers');

        /** @var ResponseInterface $responseHandler */
        $responseHandler = isset($handlers[$request::class]) === false ? App::make(Response::class) :
            App::make($handlers[$request::class]);

        return $responseHandler;
    }

    private function setBody(RequestInterface $request): void
    {
        $headers = $request->getHeaders();
        if(!isset($headers['Content-Type'])) {
            return;
        }

        $contentType = $headers['Content-Type'];

        if($contentType === 'application/json') {
            $this->pendingRequest->withBody(json_encode($request->getBody(), JSON_PRETTY_PRINT|JSON_THROW_ON_ERROR));
        }
        if($contentType === 'application/x-www-form-urlencoded') {
            $this->pendingRequest->withBody(http_build_query($request->getBody()));
        }
        if($contentType === 'multipart/form-data') {
            $this->handleMultipartRequest($request);
        }
    }

    private function handleMultipartRequest(RequestInterface $request): void
    {
        if(!isset($request->getBody()['file'])) {
            return;
        }

        $file = $request->getBody()['file'];

        $mime =  (new FileinfoMimeTypeGuesser())->guessMimeType($file['path']);

        $this->pendingRequest->asMultipart();
        $this->pendingRequest->attach('file', file_get_contents($file['path']), $file['name'], [
            'Content-Type' => $mime,
        ]);
    }
}
