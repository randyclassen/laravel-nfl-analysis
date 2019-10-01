<?php

namespace App\Services;

class Distances
{

    public static function getTravelDistance($awayLongitude, $awayLatitude, $homeLongitude, $homeLatitude)
    {
        $theta = $awayLongitude - $homeLongitude;

        $dist = sin(deg2rad($awayLatitude)) * sin(deg2rad($homeLatitude)) 
            + cos(deg2rad($awayLatitude)) * cos(deg2rad($homeLatitude)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);

        $miles = $dist * 60 * 1.1515;

        return round($miles);
    }

    public static function getCoordinates($address)
    {
        $apiKey = env('GOOGLE_GEO_API_KEY');

        $formattedAddress = str_replace(' ', '+', $address);
        
        $geocode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$formattedAddress.'&sensor=false&key='.$apiKey);
        $output = json_decode($geocode);
        if (!empty($output->error_message)) {
            return $output->error_message;
        }

        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;

        return [$latitude, $longitude];        
    }

}