<?php

namespace AppBundle\EventListener;

use AppBundle\Collector\ApiExceptionCollector;
use AppBundle\Event\ApiExceptionHandlerEvent;
use AppBundle\Model\ApiData;
use JMS\Serializer\SerializerBuilder;
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

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $data = new ApiData();
        $data->setErrors([$exception->getMessage()]);
        $code = ($exception->getCode() === 0) ? Response::HTTP_INTERNAL_SERVER_ERROR : $exception->getCode();

        $serializer = SerializerBuilder::create()->build();
        $content = $serializer->serialize($data, 'json');

        $response = new Response(
            $content,
            $code,
            [
                "ContentType" => "application/json",
            ]
        );

        $event->setResponse($response);
    }

    /**
     * @param ApiExceptionHandlerEvent $event
     */
    public function onApiException(ApiExceptionHandlerEvent $event)
    {
        $this->collector->addError($event->getException()->getMessage());
        $event->stopPropagation();
    }
}