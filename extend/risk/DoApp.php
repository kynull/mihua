<?php
namespace risk;

class DoApp extends Config
{
    /**
     * 处理数据
     * @param $data AppForm对象
     * @return Response
     */
    public function send($data)
    {
        $config = parent::getConfig();

        $serverUrl          = $config['SERVER_URL'];   # 联调接口
        $privateKey         = $config['PRIVATE_KEY'];  # 客户私钥，在SAAS系统中申请
        $partnerId          = $config['PARTNER_ID'];   # 客户编码，由诚安提供
        $cafintechPublicKey = $config['PUBLIC_KEY'];   # 诚安聚立服务公钥，由诚安提供
        $serverCertPath     = $config['CERT_PATH'];    # 服务器证书路径，证书由诚安提供
        $connectTimeout     = $config['CONNECT_TIMEOUT'];
        $readTimeout        = $config['READ_TIMEOUT'];

        $appClient = new AppClientImp($privateKey, $partnerId, $serverCertPath, $serverUrl, $cafintechPublicKey, $connectTimeout, $readTimeout);
        $response = $appClient->paydayLoanSync($data);
        return $response;
    }
    public function upload($fileName, $filePath) {

        $config = parent::getConfig();

        $serverUrl          = $config['UPLOAD_URL'];   # 联调接口
        $privateKey         = $config['PRIVATE_KEY'];  # 客户私钥，在SAAS系统中申请
        $partnerId          = $config['PARTNER_ID'];   # 客户编码，由诚安提供
        $cafintechPublicKey = $config['PUBLIC_KEY'];   # 诚安聚立服务公钥，由诚安提供
        $serverCertPath     = $config['CERT_PATH'];    # 服务器证书路径，证书由诚安提供
        $connectTimeout     = $config['CONNECT_TIMEOUT'];
        $readTimeout        = $config['READ_TIMEOUT'];

        $appClient = new AppClientImp($privateKey, $partnerId, $serverCertPath, $serverUrl, $cafintechPublicKey, $connectTimeout, $readTimeout);

        $response = $appClient->uploadFile($serverUrl, $fileName, $filePath);
        # 正常返回结果：{
        #         "handlerData":{
        #             "fileId":"cdb5d294b0ad4d92bb14a9f3c36973cb"
        #          },
        #          "resCode":"0000",    // resCode 为 0000 表示成功，其他情况表示异常
        #          "resMsg":"成功"
        #    }
        #    fileId 为该文件在诚安影像系统的id
        return json_decode($response);
    }
}