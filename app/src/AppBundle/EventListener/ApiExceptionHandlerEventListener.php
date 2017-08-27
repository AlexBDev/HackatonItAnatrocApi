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
     * @var bool
     */
    private $handlingGeneralException;

    /**
     * @var bool
     */
    private $isDev;

    /**
     * ApiExceptionHandlerEventListener constructor.
     * @param ApiExceptionCollector $collector
     */
    public function __construct(ApiExceptionCollector $collector, bool $isDev, bool $handlingGeneralException)
    {
        $this->collector = $collector;
        $this->handlingGeneralException = $handlingGeneralException;
        $this->isDev = $isDev;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->isDev || $this->handlingGeneralException) {
            $exception = $event->getException();
            $code = ($exception->getCode() === 0) ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode();
            $data = (new ApiData())->setErrors([$exception->getMessage()]);

            $event->setResponse(ApiResponse::response($data, [], $code));
        }
    }

    /**
     * @param ApiExceptionHandlerEvent $event
     */
    public function onApiException(ApiExceptionHandlerEvent $event)
    {
        $this->collector->addError($event->getException()->getMessage());
    }
}