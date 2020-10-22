<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2020/10/22
 * Time: 11:35
 */

namespace Tools\File;


class FileService
{
    private $mimeTypeArr = [];

    public function __construct()
    {
        #文件MimeType
        $mimeTypeArr       = require_once('commonMimeType.php');
        $this->mimeTypeArr = array_flip($mimeTypeArr);
    }

    /**
     * 二进制上传
     * @param $file_path
     * @param $fileBast64Data
     * @return array
     */
    public function uploadBinary($file_path,$fileBast64Data)
    {
        #获取文件mimeType
        list($fileTypeInfo, $fileData) = explode(',', $fileBast64Data);
        $mimeType = substr($fileTypeInfo, 5, -7);
        #判断文件mimeType
        if (!isset($this->mimeTypeArr[$mimeType])) {
            return ['status' => 0, 'msg' => "文件类型 $mimeType 不存在mimeType列表"];
        }

        #整理保存的图片路径
        $saveFilePath = $file_path . uniqid() . '.' . $this->mimeTypeArr[$mimeType]; #图片路径

        #创建文件夹
        !is_dir($file_path) && mkdir($file_path, 0777, 1);

        $saveBool = file_put_contents($saveFilePath, base64_decode($fileData));

        if ($saveBool) {
            return ['status' => 1, 'filePathName' => $saveFilePath];
        } else {
            return ['status' => 0, 'msg' => "上传失败"];
        }

    }

    public function downloadStream($file_path = "./", $file_name)
    {
        #避免中文文件名出现检测不到文件名的情况，进行转码utf-8->gbk
        $filename = iconv('utf-8', 'gb2312', $file_name);
        $path     = $file_path . $filename;
        if (!file_exists($path)) {//检测文件是否存在
            return ['status' => 0, 'msg' => "文件不存在!"];
        }
        $fp       = fopen($path, 'r');//只读方式打开
        $fileSize = filesize($path);//文件大小
        //返回的文件(流形式)
        header("Content-type: application/octet-stream");        //按照字节大小返回
        header("Accept-Ranges: bytes");        //返回文件大小
        header("Accept-Length: $fileSize");        //这里客户端的弹出对话框，对应的文件名
        header("Content-Disposition: attachment; filename=" . $filename);
        //================重点====================
        ob_clean();
        flush();
        //================重点====================
        //设置分流
        $buffer = 1024;        //来个文件字节计数器
        $count  = 0;
        while (!feof($fp) && ($fileSize - $count > 0)) {
            $data  = fread($fp, $buffer);
            $count += $buffer;//计数
            echo $data;//传数据给浏览器端
        }
        fclose($fp);
    }

}