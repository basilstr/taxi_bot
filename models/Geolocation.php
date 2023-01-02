<?php
namespace app\models;

class Geolocation
{
    public static function address($lat, $lon)
    {
        $geo = new self();
        $address = $geo->getAddress($lat, $lon);
        $coord = $geo->getCoord($address);
        if (self::calculateDistance($lat, $lon, $coord['lat'], $coord['lon']) < 100) return $address;
        return '';
    }

    private function getAddress($lat, $lon)
    {
        $url = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=uk&lat=$lat&lon=$lon";

        $address = self::nominatim($url);
        return $address['display_name'];
    }

    private function getCoord($address)
    {
        $url = "https://nominatim.openstreetmap.org/search?q=$address&format=json&polygon_geojson=1&addressdetails=1";

        $address = self::nominatim($url);
        return ['lat' => $address['0']['lat'], 'lon' => $address['0']['lon']];
    }


    private function nominatim($url)
    {
        $ch = curl_init();
        $useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept-Language: ru']);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Радиус земли
        $earth_radius = 6372795;

        // перевести координаты в радианы
        $lat1 = self::GeoFix($lat1) * M_PI / 180;
        $lat2 = self::GeoFix($lat2) * M_PI / 180;
        $lon1 = self::GeoFix($lon1) * M_PI / 180;
        $lon2 = self::GeoFix($lon2) * M_PI / 180;

        // косинусы и синусы широт и разницы долгот
        $cl1 = cos($lat1);
        $cl2 = cos($lat2);
        $sl1 = sin($lat1);
        $sl2 = sin($lat2);
        $delta = $lon2 - $lon1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);

        // вычисления длины большого круга
        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

        //
        $ad = atan2($y, $x);
        $dist = $ad * $earth_radius;

        return round($dist);
    }

    public static function GeoFix($geo){
        $geo= str_replace(',','.',$geo);
        return $geo;
    }


}