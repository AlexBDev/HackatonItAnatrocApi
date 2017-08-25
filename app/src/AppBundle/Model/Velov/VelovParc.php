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
    * @param Localisation $localisation*
    * @return array :
    *      - "distance" : return the distance between out position and the nearest velov stand. ( integer )
    *      - "arret" : return the nearest arret ( object instance )
    * @throws \Exception
    *
    * Exemple :
    *
    *      $localisation = new Localisation(45.770356799999995, 4.8637349);
    *      dump(VelovParc::getNearArret($data,$localisation));
    *
    */
    static public function getNearStop( array $datas ,Localisation $localisation):array
    {
        $resultArret = null;
        $distanceResult = null;
        $result = array();

        foreach ($datas as $data) {
            if ($data instanceof VelovArret) {
                if(is_null($distanceResult)
                    || $distanceResult > LocalisationUtils::distance($localisation, $data->getLocalisation())
                ) {
                    $resultArret = $data;
                    $distanceResult = LocalisationUtils::distance($localisation, $data->getLocalisation());
                }
            }
        }

        if ($resultArret === null) {
            throw new \Exception(
                'Any stop has been found for localisation "'.$localisation->getStringPosition().'""',
                404
            );
        }

        $result["distance"] = $distanceResult;
        $result["arret"] = $resultArret;

        return $result;
    }
}