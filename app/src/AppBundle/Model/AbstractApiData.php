<?php

namespace AppBundle\Model;


abstract class AbstractApiData
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
     * @param array $errors
     * @return AbstractApiData
     */
    public function setErrors(array $errors): AbstractApiData
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function addError(string $error)
    {
        $this->addErrors([$error]);

        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function addErrors(array $errors)
    {
        foreach ($errors as $error) {
            $this->errors[] = $error;
        }

        return $this;
    }

    /**
     * @return array|AbstractApiData
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     * @return AbstractApiData
     * @throws \UnexpectedValueException
     */
    public function setData($data): AbstractApiData
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param $data
     * @return AbstractApiData
     * @throws \UnexpectedValueException
     */
    public function addData($data): AbstractApiData
    {

        if (is_array($data) || $data instanceof AbstractApiData) {
            if (!is_array($data)) {
                array_push($this->data, $data);
            } else {
                // Petit hack pour les arrets Velov
                if (isset($data['type']) && stristr($data['type'], "transport.velov")){
                    array_push($this->data, $data);
                } else {
                    $this->data = array_merge($this->getData(), $data);
                }
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
     * @return AbstractApiData
     */
    public function setType(string $type): AbstractApiData
    {
        $this->type = $type;

        return $this;
    }
}