<?php
/**
 * Created by PhpStorm.
 * User: alexis
 * Date: 25/08/17
 * Time: 21:11
 */

namespace AppBundle\Collector;


class ApiExceptionCollector
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param string $error
     */
    public function addError(string $error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}