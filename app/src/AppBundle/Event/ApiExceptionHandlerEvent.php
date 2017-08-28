<?php
/**
 * Created by PhpStorm.
 * User: alexis
 * Date: 25/08/17
 * Time: 20:41
 */

namespace AppBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class ApiExceptionHandlerEvent extends Event
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * ApiExceptionHandler constructor.
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException(): \Exception
    {
        return $this->exception;
    }
}