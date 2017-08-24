<?php

namespace risk;


class HttpsUtils
{
    function curlPost($url, $data = array(), $cacert, $timeout = 30, $CA = true){

        $SSL = substr($url, 0, 8) == "https://" ? true : false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2);
        if ($SSL && $CA) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
            curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
        } else if ($SSL && !$CA) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //避免data数据过长问题
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $ret = curl_exec($ch);

        //var_dump(curl_error($ch));  //查看报错信息

        curl_close($ch);
        return $ret;
    }

    static function uploadFile($url, $paramMap, $filepath, $filename, $fileBodyName, $cacert, $timeout = 30, $CA = true){

        $SSL = substr($url, 0, 8) == "https://" ? true : false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2);
        if ($SSL && $CA) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);   // 只信任CA颁布的证书
            curl_setopt($ch, CURLOPT_CAINFO, $cacert); // CA根证书（用来验证的网站证书是否是CA颁布）
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名，并且是否与提供的主机名匹配
        } else if ($SSL && !$CA) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 检查证书中是否设置域名
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $BOUNDARY = CryptAES::gen32Key(); #"1234567890";
        $PREFIX = "--";
//        $LINEND = "\r\n";
        $LINEND = "\x0D\x0A";
        $QUOTA = "\x22";

        $headers = array();
        $headers[] = 'Connection: Keep-Alive';
        $headers[] = 'Charset: UTF-8';
        $headers[] = 'Content-Type: multipart/form-data; boundary=' . $BOUNDARY;
        $headers[] = 'User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $post_data = "";
        foreach ($paramMap as $key => $value) {
            $post_data = $post_data . $PREFIX . $BOUNDARY . $LINEND;
            $post_data = $post_data . 'Content-Disposition: form-data; name=' . $QUOTA . $key . $QUOTA . $LINEND;
            $post_data = $post_data . "Content-Type: text/plain; charset=UTF-8" . $LINEND;
            $post_data = $post_data . "Content-Transfer-Encoding: 8bit" . $LINEND;
            $post_data = $post_data . $LINEND . $value . $LINEND;
        }
        $post_data = $post_data . $PREFIX . $BOUNDARY . $LINEND;
        $post_data = $post_data . "Content-Disposition: form-data;name=" . $QUOTA . $fileBodyName . $QUOTA . ";filename=" . $QUOTA . $filename . $QUOTA . $LINEND;
        $post_data = $post_data . "Content-Type: application/octet-stream; charset=UTF-8" . $LINEND;
        $post_data = $post_data . $LINEND;
        $post_data = $post_data . file_get_contents($filepath) . $LINEND;
        $post_data = $post_data . $PREFIX . $BOUNDARY . $PREFIX . $LINEND;

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        $ret = curl_exec($ch);
        //var_dump(curl_error($ch));  //查看报错信息

        curl_close($ch);
        return $ret;
    }

}