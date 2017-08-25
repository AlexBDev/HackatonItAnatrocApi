<?php
/**
 * Created by PhpStorm.
 * User: apprenant
 * Date: 05/07/17
 * Time: 15:55
 */


namespace AppBundle\Model\Velov;

use AppBundle\Model\Localisation;
use AppBundle\Utils\LocalisationUtils;

abstract class VelovParc
{
    static public $park = array();

    static public function returnFirstsInArray( int $number_record, array $datas): array
    {
        $result = array();
        for ($i = 0; $i < $number_record; $i++) {
            $data = array();
            $data['type'] = "transport.Velov";
            $data['errors'] = array();
            $datas[$i]->returnJson($data['data']);

            $result[] = $data;

        }

        return $result;
    }

    /**
    * @param array $datas
    * @param Localisation $loc
    * @return array :
    *      - "distance" : return the distance between out position and the nearest velov stand. ( integer )
    *      - "arret" : return the nearest arret ( object instance )
    *
    *
    * Exemple :
    *
    *      $localisation = new Localisation(45.770356799999995, 4.8637349);
    *      dump(VelovParc::getNearArret($data,$localisation));
    *
    */
    static public function getNearStop( array $datas ,Localisation $loc):array
    {
        $resultArret = null;
        $distanceResult = null;
        $result = array();
        foreach ($datas as $data)
        {
            if ($data instanceof VelovArret)
            {
                if(is_null($distanceResult) || $distanceResult > LocalisationUtils::distance($loc, $data->getLocalisation()))
                {
                    $resultArret = $data;
                    $distanceResult = LocalisationUtils::distance($loc, $data->getLocalisation());
                }
            }
        }

        if (is_null($resultArret))
        {
            return null;
        }

        $result["distance"] = $distanceResult;
        $result["arret"] = $resultArret;

        return $result;
    }
}