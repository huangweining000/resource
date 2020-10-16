<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2020/10/16
 * Time: 16:06
 */

namespace Tools;


class Map
{
    #高德地图KEY
    public $KEY = '2346ad626742196594a63f28158cfb17';

    public function __construct()
    {

    }

    /**
     * 根据经纬度查地点，（高德地图）
     * @param string $lng 默认经度 114.05  （广东省-深圳市-福田区）
     * @param string $lat 默认纬度 22.54   （广东省-深圳市-福田区）
     * @param int $radius 默认范围 500
     * @param bool $simple
     * @return array|mixed
     */
    public function locationInfo($lng = '114.05', $lat = '22.54', $radius = 500, $simple = false)
    {
        $location = $lng . ',' . $lat;
        $url      = "http://restapi.amap.com/v3/geocode/regeo?output=json&location=" . $location . "&key=" . $this->KEY . "&radius=$radius&extensions=base";
        $content  = file_get_contents($url);
        $data     = $arr = json_decode($content, true);
        if ($simple) {
            $data = array(
                "err_code" => $arr['infocode'], //错误编码，如出现错误可去高德地图查询原因
                "address"  => $arr['regeocode']['formatted_address'],
                "country"  => $arr['regeocode']['addressComponent']['country'],
                "province" => $arr['regeocode']['addressComponent']['province'],
                "city"     => $arr['regeocode']['addressComponent']['city'],
                "district" => $arr['regeocode']['addressComponent']['district'],
                "township" => $arr['regeocode']['addressComponent']['township'],
                "citycode" => $arr['regeocode']['addressComponent']['citycode'],
                "adcode"   => $arr['regeocode']['addressComponent']['adcode'],
            );
        }
        return $data;
    }

    /**
     * 统计距离
     * @param $lng1 经度1
     * @param $lat1 纬度1
     * @param $lng2 经度2
     * @param $lat2 纬度2
     * @return float 单位（米）
     */
    public function countDistance($lng1, $lat1, $lng2, $lat2)
    {
        $earthRadius = 6367000;
        //approximate radius of earth in meters  /* Convert these degrees to radians to work with the formula */
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        /* Using the Haversine formula  http://en.wikipedia.org/wiki/Haversine_formula  calculate the distance */
        $calcLongitude      = $lng2 - $lng1;
        $calcLatitude       = $lat2 - $lat1;
        $stepOne            = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo            = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance, 2);
    }

    /**距离米转换成单位（千米）
     * @param $distance
     * @return string
     */
    function distanceSwitchKM($distance){
        if($distance<1000){
            return $distance.'米';
        }
        if($distance>=1000){
            $distance_km = $distance/1000;
            return $distance_km.'千米';
        }
    }
}