<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2020/10/16
 * Time: 17:46
 */

namespace Tools\Bank;

use Tools\Http\HttpService;

class BankService
{

    private $bankNameArr = [];
    private $appCode = '';
    public function __construct()
    {
        #银行名称数组
        $this->bankNameArr = require_once('BankName.php');
        #银行卡APP_CODE
        $this->appCode = 'ad97e84018db44fabaf234991c86c72c';
    }

    /**
     * 银行卡类型 解析成中文
     * @param $card_type
     * @return mixed|string
     */
    public function cardTypeToCn($card_type){
        $card_type_arr = [
            'DC'=>"储蓄卡",
            'CC'=>"信用卡",
            'SCC'=>"准贷记卡",
            'PC'=>"预付费卡"
        ];
        if(isset($card_type_arr[$card_type])){
            return $card_type_arr[$card_type];
        }else{
            return '未知卡种';
        }
    }

    /**
     * 银行卡号查询（用阿里巴巴免费的银行卡检测接口）
     *
     * @param string $bank_card_number
     * @param int $detail  0:阿里巴巴接口原始返回值；1:转化中文；2:转化中文且有图标LOGO
     * @return array|mixed
     */
    public function cardCheck($bank_card_number="",$detail=1){
        !$bank_card_number&&die();

        $url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=$bank_card_number&cardBinCheck=true";
        $card_info = https_get($url);
        $card_info_arr = json_decode($card_info,1);
        if($card_info_arr&&$card_info_arr['validated'] == true){
            $return_card_info = [];
            switch ($detail){
                case 0:
                    $return_card_info = $card_info_arr;
                    break;
                case 1:
                    $return_card_info['cardNo'] = $card_info_arr['key'];
                    $return_card_info['cardType'] = $card_info_arr['cardType'];
                    $return_card_info['cardTypeName'] =$this->cardTypeToCn($card_info_arr['cardType']);
                    $return_card_info['bank'] = $card_info_arr['bank'];
                    $return_card_info['bankName'] = $this->bankNameArr[$card_info_arr['bank']];
                case 2:
                    $return_card_info['logo_url'] = "https://apimg.alipay.com/combo.png?d=cashier&t=".$card_info_arr['bank'];
                    $return_card_info['logo_url_small'] = '/static/small_logo/'.$card_info_arr['bank'].'.png';
                    break;
            }
            return $return_card_info;
        }else{
            return [];
        }
    }

    /*
 * 银行四要素 银行卡号&姓名&身份证号&银行预留手机号
 * 购买地址 https://market.aliyun.com/products/57000002/cmapi028807.html?spm=5176.730005.productlist.d_cmapi028807.11963524N1HwjY&innerSource=search_%E9%93%B6%E8%A1%8C%E5%8D%A1%20%E5%9B%9B%E8%A6%81%E7%B4%A0#sku=yuncode2280700001
 */
    /**
     * 银行卡四要素验证(阿里云市场购买的第三方验证接口，收费接口)
     * @param $bank_card_number
     * @param $idCard
     * @param $mobile
     * @param $name
     * @return array
     */
    public function cardVerify($bank_card_number,$idCard,$mobile,$name){
        //四要素验证
        $host = "https://bcard3and4.market.alicloudapi.com";
        $path = "/bankCheck4";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $this->appCode);
        $querys = "accountNo=$bank_card_number&idCard=$idCard&mobile=$mobile&name=$name";
        $url = $host . $path . "?" . $querys;

        $out_put = HttpService::https_get($url,$headers);
        $out_put_info = json_decode($out_put,1);
        if($out_put_info&&$out_put_info['status']=="01"){#验证通过返回数组
            return ['status'=>1,'msg'=>'银行卡验证通过','data'=>$out_put_info];
        }else{#验证不通过，提醒信息
            return ['status'=>0,'msg'=>'银行卡验证不通过','data'=>$out_put_info];
        }
    }
}