<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2020/10/21
 * Time: 17:06
 */

namespace Tools\Http;


class HttpService
{
    /**
     * @param $url
     * @param array $header_data
     * @return bool|string
     */
    public static  function  https_get($url, $header_data = array())
    {
        if (!function_exists('curl_init')) {
            throw new Exception('server not install curl');
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);//启用时会将头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//返回原生的（Raw）输出
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。//禁用后cURL将终止从服务端进行验证
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);//设为FALSE表示不检查证书
        if ($header_data) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
        }
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }


    /**
     * @param $url
     * @param null $data
     * @param array $header_data
     * @return bool|string
     */
    public static  function https_post($url, $data = null, $header_data = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        if ($header_data) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header_data);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}