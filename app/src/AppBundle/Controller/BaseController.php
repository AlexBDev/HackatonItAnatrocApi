<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 24/08/17
 * Time: 17:04
 */

namespace AppBundle\Controller;

use AppBundle\Collector\ApiExceptionCollector;
use AppBundle\Model\ApiData;
use AppBundle\Response\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    public function response($data, array $headers  = [], int $status = 200, $json = false)
    {
        if ($data instanceof ApiData) {
            $errors = $this->get(ApiExceptionCollector::class)->getErrors();
            $data->addErrors($errors);
        }

        return ApiResponse::response($data, $headers, $status, $json);
    }
}