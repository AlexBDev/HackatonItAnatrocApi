<?php

namespace AppBundle\Utils;
 
 /**
  * Created by PhpStorm.
  * User: apprenant
  * Date: 07/07/17
  * Time: 15:33
  */
 use AppBundle\Model\Localisation;
 
 
 
 abstract class LocalisationUtils
 {
        /**
         * return the distance between 2 localisation in kilometer with 3 decimals
         *
         * @param $lat1
         * @param $lon1
         * @param $lat2
         * @param $lon2
         * @return int : in km round at 3
         */
        static public function distance(Localisation $loc1, Localisation $loc2): float
     {
                //rayon de la terre
                $r = 6366;
                $lat1 = deg2rad($loc1->getLat());
                $lat2 = deg2rad($loc2->getLat());
                $lon1 = deg2rad($loc1->getLng());
                $lon2 = deg2rad($loc2->getLng());
        
        
                //calcul précis
         $dp= 2 * asin(sqrt(pow (sin(($lat1-$lat2)/2) , 2) + cos($lat1)*cos($lat2)* pow( sin(($lon1-$lon2)/2) , 2)));
        
                //sortie en km
                $d = $dp * $r;
        
                //arondis au mètre
                $d = round($d,3);
        
                return $d;
     }


     public static function getCoordonateByAddress($address)
     {
         // url encode the address
         $address = urlencode($address);

         // google map geocode api url
         $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

         // get the json response
         $resp_json = file_get_contents($url);

         // decode the json
         $resp = json_decode($resp_json, true);

         // response status will be 'OK', if able to geocode given address
         if($resp['status']=='OK'){

             // get the important data
             $lati = $resp['results'][0]['geometry']['location']['lat'];
             $longi = $resp['results'][0]['geometry']['location']['lng'];

             // verify if data is complete
             if(!is_null($lati) && !is_null($longi)){

                 $loc = new Localisation($lati,$longi);

                 return $loc;
             }else{
                 return false;
             }

         }else{
             return false;
         }
     }


 } 