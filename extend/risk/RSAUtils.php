<?php
namespace risk;
use risk\RSAUtil\KeyFormat;
use risk\RSAUtil\KeyWorker;

class RSAUtils
{
    public static function sign($str, $selfPrivateKey){
        $privateWorker = new KeyWorker($selfPrivateKey, KeyFormat::ASN);
        $sign = $privateWorker->sign($str);
        return $sign;
    }

    public static function encryptByPublicKey($data, $pubKey) {
        $publicWorker = new KeyWorker($pubKey, KeyFormat::ASN);
        $encData = $publicWorker->encrypt($data);
        return $encData;
    }

    public static function verify($origin, $sign, $pubKey) {
        $publicWorker = new KeyWorker($pubKey, KeyFormat::ASN);
        return $publicWorker->verify($origin, $sign);
    }

    public static function decrptByPrivateKey($data, $priKey) {
        $privateWorker = new KeyWorker($priKey, KeyFormat::ASN);
        $decData = $privateWorker->decrypt($data);
        return $decData;
    }

}