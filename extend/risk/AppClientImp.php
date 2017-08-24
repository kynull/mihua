<?php
namespace risk;

class AppClientImp extends AppClient
{
    public function __construct($selfPrivateKey, $partnerId, $serverCertPath, $openApiUrl, $cafintechPubKey, $connectTimeOut=30000, $readTimeOut=45000)
    {
        parent::__construct($selfPrivateKey, $partnerId, $serverCertPath, $openApiUrl, $cafintechPubKey, $connectTimeOut, $readTimeOut);
    }

    public function AppClientImp($selfPrivateKey, $partnerId, $serverCertPath, $openApiUrl, $cafintechPubKey, $connectTimeOut=30000, $readTimeOut=45000) {
        parent::AppClient($selfPrivateKey, $partnerId, $serverCertPath, $openApiUrl, $cafintechPubKey, $connectTimeOut, $readTimeOut);
    }
    /**
     * 同步调用信审流程 服务端
     * @param $appForm
     * @internal param $appForm
     * @return Response
     */
    public function syncApprove($appForm)
    {
        return $this->httpRequest($appForm, SysParam::$CREDIT_APPLY_SYN);
    }

    /**
     * 异步提交信审流程
     * @param $appForm
     * @return Response
     * @internal param $appForm
     */
    public function asynApprove($appForm)
    {
        return $this->httpRequest($appForm, SysParam::$CREDIT_APPLY_ASYN);
    }

    /**
     * 同步调用信审流程 服务端
     * @param $appForm
     * @return Response
     * @internal param $appForm
     */
    public function paydayLoanSync($appForm)
    {
        return $this->httpRequest($appForm, SysParam::$PAY_DAY_LOAN_CREDIT_APPLY_SYN);
    }

    /**
     * 异步提交信审流程
     * @param $appForm
     * @return Response
     * @internal param $appForm
     */
    public function paydayLoanAsyn($appForm)
    {
        return $this->httpRequest($appForm, SysParam::$PAY_DAY_LOAN_CREDIT_APPLY_ASYN);
    }

    /**
     * 查询审批状态
     *
     * @param $noBus
     * @return Response
     */
    public function approveQuery($noBus)
    {
        $noBus = array(
            "noBus" => $noBus
        );
        $param = array(
            "param" => json_encode($noBus)
        );

        return $this->request($param, SysParam::$CREDIT_SELECT);
    }

    /**
     * 上传文件
     * @param $url
     * @param 文件名 $fileName
     * @param $filePath 文件全路径
     * @return bool|mixed|string
     */
    public function uploadFile($url, $fileName, $filePath)
    {
        return parent::getClient()->postFile($url, $fileName, $filePath);
    }

    /**
     * 处理信审调用请求
     * @param $paramObj
     * @param $productCode
     * @return Response
     */
    private function httpRequest($paramObj, $productCode) {
        // echo "paramString:" . $productCode . ' -> ' . parent::paramToJsonString($paramObj) . "<br/>";
        $param = array(
            "param" => parent::paramToJsonString($paramObj)
        );
        return parent::jsonResultToObject(parent::getClient()->authenReq($param, $productCode));
    }

    /**
     * 通用接口 web
     *
     * @param $param
     * @param $service_id
     * @return Response
     */
    public function request($param, $productCode)
    {
        return parent::jsonResultToObjectCommon(parent::getClient()->authenReq($param, $productCode));
    }
}