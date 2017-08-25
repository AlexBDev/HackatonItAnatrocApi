<?php

namespace AppBundle\EventListener;

use AppBundle\Collector\ApiExceptionCollector;
use AppBundle\Event\ApiExceptionHandlerEvent;
use AppBundle\Model\ApiData;
use AppBundle\Response\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiExceptionHandlerEventListener
{
    /**
     * @var ApiExceptionCollector
     */
    private $collector;

    /**
     * ApiExceptionHandlerEventListener constructor.
     * @param ApiExceptionCollector $collector
     */
    public function __construct(ApiExceptionCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $code = ($exception->getCode() === 0) ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode();
        $data = (new ApiData())->setErrors([$exception->getMessage()]);

        $event->setResponse(ApiResponse::response($data, [], $code));
    }

    /**
     * @param ApiExceptionHandlerEvent $event
     */
    public function onApiException(ApiExceptionHandlerEvent $event)
    {
        $this->collector->addError($event->getException()->getMessage());
    }
}