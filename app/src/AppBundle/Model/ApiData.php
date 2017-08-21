<?php

namespace AppBundle\Model;


class ApiData
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $type;

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $error
     * @return ApiData
     */
    public function addError(string $error): ApiData
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * @param array $errors
     * @return ApiData
     */
    public function setErrors(array $errors): ApiData
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return array|ApiData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return ApiData
     * @throws \UnexpectedValueException
     */
    public function setData($data): ApiData
    {
        if (is_array($data) || $data instanceof ApiData) {
            $this->data = $data;

            return $this;
        }

        throw new \UnexpectedValueException();
    }

    /**
     * @param $data
     * @return ApiData
     * @throws \UnexpectedValueException
     */
    public function addData($data): ApiData
    {
        if (is_array($data) || $data instanceof ApiData) {
            if (!is_array($data)) {
                array_push($this->data, $data);
            } else {
                $this->data = array_merge($this->getData(), $data);
            }

            return $this;
        }

        throw new \UnexpectedValueException();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ApiData
     */
    public function setType(string $type): ApiData
    {
        $this->type = $type;

        return $this;
    }
}