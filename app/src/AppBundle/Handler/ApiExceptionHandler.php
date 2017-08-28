<?php

namespace AppBundle\Handler;


use AppBundle\AppEvents;
use AppBundle\Event\ApiExceptionHandlerEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApiExceptionHandler
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * ApiExceptionHandler constructor.
     * @param EventDispatcherInterface $dispatcher
     * @param bool $disabled If throw event is not dispatch and exception is throwing
     */
    public function __construct(EventDispatcherInterface $dispatcher, bool $disabled)
    {
        $this->dispatcher = $dispatcher;
        $this->disabled = $disabled;
    }

    /**
     * @param $callback
     * @return \Exception|mixed
     * @throws \Exception
     */
    public function handle($callback)
    {
        try {
            return call_user_func($callback);
        } catch (\Exception $e) {
            if ($this->disabled) {
                throw $e;
            }

            $this->dispatch($e);

            return $e;
        }
    }

    /**
     * @param \Exception $exception
     */
    private function dispatch(\Exception $exception)
    {
        $this->dispatcher->dispatch(
            AppEvents::API_CATCH_EXCEPTION,
            new ApiExceptionHandlerEvent($exception)
        );
    }
}