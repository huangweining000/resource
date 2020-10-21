<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2020/10/16
 * Time: 17:46
 */

namespace Tools\Bank;

class BankService
{

    private $bankNameArr = [];
    public function __construct()
    {
        #银行名称数组
        $this->bankNameArr = require_once('BankName.php');
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
}