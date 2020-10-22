<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2020/10/21
 * Time: 18:29
 */

namespace Tools\IdCard;


class IdCardService
{

    private $appCode = '';

    public function __construct()
    {
        $this->appCode = 'ad97e84018db44fabaf234991c86c72c';
    }

    /**
     * 阿里云云市场 身份证图片识别信息
     * 购买地址 https://market.aliyun.com/products/57124001/cmapi010401.html?spm=a2c4g.11186623.2.7.VojbRu#sku=yuncode440100000
     * @param $fileBast64Data
     * @param $zf_type
     * @return bool|string
     */
    public function verify($fileBast64Data,$zf_type){
        #我的身份证 base64

        $url = "https://dm-51.data.aliyun.com/rest/160601/ocr/ocr_idcard.json";
        //如果输入带有inputs, 设置为True，否则设为False
        $is_old_format = false;
        //如果没有configure字段，config设为空
        $config = array(
            "side" => "$zf_type"
        );
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appCode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        if($is_old_format == TRUE){
            $request = array();
            $request["image"] = array(
                "dataType" => 50,
                "dataValue" => "$fileBast64Data"
            );

            if(count($config) > 0){
                $request["configure"] = array(
                    "dataType" => 50,
                    "dataValue" => json_encode($config)
                );
            }
            $body = json_encode(array("inputs" => array($request)));
        }else{
            $request = array(
                "image" => "$fileBast64Data"
            );
            if(count($config) > 0){
                $request["configure"] = json_encode($config);
            }
            $body = json_encode($request);
        }
        $method = "POST";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$url, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rheader = substr($result, 0, $header_size);
        $rbody = substr($result, $header_size);

        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        if($httpCode == 200){
            if($is_old_format){
                $output = json_decode($rbody, true);
                $result_str = $output["outputs"][0]["outputValue"]["dataValue"];
            }else{
                $result_str = $rbody;
            }
            #判断反面身份证，如果过期，提醒
            if($zf_type == 'back'){
                $result_str_arr = json_decode($result_str,1);
                $end_date = $result_str_arr['end_date'];
                $now_date = date('Ymd');
                if($end_date < $now_date){
                    die(json_encode(['status'=>0,'msg'=>'身份证已经过有效期，请使用未过期的身份证']));
                }
            }
            #身份证识别成功
            return $result_str;
        }else{
            die(json_encode(['status'=>0,'msg'=>'图片模糊，请重新上传']));
        }
    }
}