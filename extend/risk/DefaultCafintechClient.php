<?php
namespace risk;


class DefaultCafintechClient implements CafintechHttpClient
{
    const DEFULT_CHARSET = "UTF-8";
    //系统原来默认的链接超时间
    private $DEFAULT_CONNECT_TIME_OUT = 30000;
    //系统原来默认的读取超时间
    private $DEFAULT_READ_TIMEOUT = 45000;
    private $serverCertPath = "";
    private $serverUrl;
    private $partnerId;
    private $selfPrivateKey;
    private $cafintechPubKey;
    const transform = "AES/CBC/PKCS5Padding";

    public function __construct($serverUrl, $partnerId, $selfPrivateKey, $cafintechPubKey, $serverCertPath,
                                $connectTimeout, $readTimeout)
    {
        $this->serverUrl = $serverUrl;
        $this->partnerId = $partnerId;
        $this->selfPrivateKey = $selfPrivateKey;
        $this->cafintechPubKey = $cafintechPubKey;
        $this->serverCertPath = $serverCertPath;
        $this->DEFAULT_CONNECT_TIME_OUT = $connectTimeout;
        $this->DEFAULT_READ_TIMEOUT = $readTimeout;
    }

    private function transeferString($str)
    {
        if ($str != null) {
            $temp = str_replace("&", "%26", $str);
            return str_replace("=", "%3D", $temp);
        }
        return null;
    }

    private function parseParam($param)
    {
        $buffer = "";
        if (null === $param) {
            return $buffer . toString();
        } else {
            foreach ($param as $key => $value) {
                $buffer = $buffer . $this->transeferString($key) . "=" . $this->transeferString($value) . "&";
            }
            return $buffer;
        }
    }

    public function authenReq($param, $productCode)
    {
        $sb = $this->parseParam($param) . "&productCode=" . $productCode . "&partnerId=" . $this->partnerId;
        $sb = $sb . "&sign=" . RSAUtils::sign($sb, $this->selfPrivateKey);

        $aeskey = CryptAES::gen32Key();
        $aes = new CryptAES();
        $aes->set_key($aeskey);
        $aes->require_pkcs5();
        $enc = "s=" . $aes->encrypt($sb);

        $rsaEncrypt = RSAUtils::encryptByPublicKey($aeskey, $this->cafintechPubKey);
        $enc = $enc . "&p=" . $rsaEncrypt . "&version=" . CafintechHttpClient::VERSION;

        $httpClient = new HttpsUtils();
        $result = $httpClient->curlPost($this->serverUrl, $enc, $this->serverCertPath);

        $retJson = json_decode($result, true);

        $resultModel = new ResultModel();
        $resultModel->resCode = $retJson['resCode'];
        if (array_key_exists('resMsg', $retJson)) {
            $resultModel->resMsg = $retJson['resMsg'];
        }

        if (array_key_exists('handlerData', $retJson)) {
            $resultModel->handlerData = $retJson['handlerData'];
        }

        if ($resultModel->resCode == "0000") {
            $rt = $aes->decrypt($resultModel->handlerData);
        } else {
            return $result;
        }
        $splitedSignStr = explode("&sign", $rt);
        if (!RSAUtils::verify($splitedSignStr[0], $splitedSignStr[1], $this->cafintechPubKey)) {
            throw new Exception("RSA签名验证错误，result=\"" . $rt . "\"");
        }
        $resultModel->handlerData = $splitedSignStr[0];
        return $resultModel->__toString();
    }

    public function CheckEmptyString($C_char)
    {
        if (!is_string($C_char)) return false; //判断是否是字符串类型
        if (empty($C_char)) return false; //判断是否已定义字符串
        if ($C_char=='') return false; //判断字符串是否为空
        return true;
    }

    public function postFile($url, $fileName, $fullFilePath)
    {
        $sb = "partnerId=" . $this->partnerId;
        $auth = RSAUtils::encryptByPublicKey($sb, $this->cafintechPubKey);
        //私钥签名
        $sign = RSAUtils::sign($sb, $this->selfPrivateKey);
        $param = array(
            "auth" => $auth,
            "sign" => $sign,
            "version" => CafintechHttpClient::VERSION
        );

        //发送请求
        $result = HttpsUtils::uploadFile($url, $param, $fullFilePath, $fileName, "fileData",  $this->serverCertPath);

        $retJson = json_decode($result, true);
        $resultModel = new ResultModel();
        $resultModel->resCode = $retJson['resCode'];
        $resultModel->resMsg = $retJson['resMsg'];
        $resultModel->handlerData = $retJson['handlerData'];

        if ($resultModel->resCode == "0000" && $this->CheckEmptyString($resultModel->handlerData)) {
            //私钥解密
            $result = base64_decode($resultModel->handlerData);
        } else {
            return $result;
        }

        //验证签名
        $splitedSignStr = explode("&sign=", $result);
        if (!RSAUtils::verify($splitedSignStr[0], $splitedSignStr[1], $this->cafintechPubKey)) {
            throw new Exception("RSA签名验证错误，result=\"" . $result . "\"");
        }
        $resultModel->handlerData = $splitedSignStr[0];
        return $resultModel->__toString();
    }

    public function postPBOCFile($url, $fileName, $fullFilePath)
    {
        return postFile($url, $fileName, $fullFilePath);
    }

}