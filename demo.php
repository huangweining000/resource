<?php
require_once './vendor/autoload.php';

#二维码使用
//use \Tools\QRCode;
//QRCode::Hello();

#地图使用
use Tools\Map;
$map = new Map();
//$info = $map->locationInfo();
//$info = $map->countDistance(22.54,114.05,22,114.4);
$info = $map->distanceSwitchKM(5555.55);

print_r($info);