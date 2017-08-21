<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 05/07/17
 * Time: 14:26
 */

namespace AppBundle\Api;


/**
 * If you want to use the ApiServiceResolver you need to implement this interface in your api service.
 * Provide a list of keyword that identify your service.
 *
 * Interface ApiKeywordInterface
 * @package AppBundle\Api
 */
interface ApiKeywordInterface
{
    public static function getApiKeywords(): array;
}