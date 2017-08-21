<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 04/07/17
 * Time: 13:59
 */

namespace AppBundle\Api;

abstract class AbstractApi implements ApiKeywordInterface
{
    /**
     * @var null|string
     */
    protected $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
