<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 05/07/17
 * Time: 15:55
 */

namespace AppBundle\Model\velov;



abstract class VelovParc
{
    static public $park = array();

    static public function returnFirstsInArray( int $number_record, array $datas): array
    {
        $result = array();
        for ($i = 0; $i < $number_record; $i++) {
            $data = array();
            $data['type'] = "transport.velov";
            $data['errors'] = array();
            $datas[$i]->returnJson($data['data']);

            $result[] = $data;

        }

        return $result;
    }
}