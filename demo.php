<?php
require_once './vendor/autoload.php';

/***HTTP服务***/
$HttpService = new \Tools\Http\HttpService();
#GET请求
$HttpService::https_get($url, $header_data = array());
#POST请求
$HttpService::https_post($url, $data = null, $header_data = array());

/***文件上传下载服务***/
$fileService = new \Tools\File\FileService();
#base64文件上传
$fileService->uploadBinary('fileDir','$fileBast64Data');
#文件流下载
$fileService->downloadStream('fileDir','fileName');

/***地图服务***/
$mapService = new \Tools\Map\MapService();
#根据经纬度查地点，（高德地图）
$mapService->locationInfo($lng = '114.05', $lat = '22.54', $radius = 500, $simple = false);
#经纬度计算距离
$mapService->countDistance($lng1, $lat1, $lng2, $lat2);

/***银行卡服务***/
$bankService = new \Tools\Bank\BankService();
#银行卡号查询（用阿里巴巴免费的银行卡检测接口）
$bankService->cardCheck($bank_card_number="",$detail=1);
#银行卡四要素验证(阿里云市场购买的第三方验证接口，收费接口)
$bankService->cardVerify($bank_card_number,$idCard,$mobile,$name);

/***身份证服务***/
$idCardService = new \Tools\IdCard\IdCardService();
#身份证 人脸面验证
$idCardService->verify($fileBast64Data,'face');
#身份证 国徽面验证
$idCardService->verify($fileBast64Data,'back');