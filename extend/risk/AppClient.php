<?php
namespace risk;
use risk\model\Response;
use risk\model\AppResult;

abstract class AppClient
{

    private $client;

    public function __construct($selfPrivateKey, $partnerId, $serverCertPath, $openApiUrl, $cafintechPubKey, $connectTimeOut=30000, $readTimeOut=45000)
    {
        $this->client = new DefaultCafintechClient($openApiUrl, $partnerId, $selfPrivateKey,
            $cafintechPubKey, $serverCertPath, $connectTimeOut, $readTimeOut);
    }
    public function AppClient($selfPrivateKey, $partnerId, $serverCertPath, $openApiUrl, $cafintechPubKey, $connectTimeOut=30000, $readTimeOut=45000)
    {
        $this->client = new DefaultCafintechClient($openApiUrl, $partnerId, $selfPrivateKey,
            $cafintechPubKey, $serverCertPath, $connectTimeOut, $readTimeOut);
    }

    public function getClient()
    {
        return $this->client;
    }

    /**
     * 同步调用信审流程 服务端
     * @param appForm
     * @return
     */
    public abstract function syncApprove($appForm);

    /**
     * 异步提交信审流程
     * @param appForm
     * @return
     */
    public abstract function asynApprove($appForm);

    /**
     * 同步调用paydayloan信审流程 服务端
     * @param appForm
     * @return
     */
    public abstract function paydayLoanSync($appForm);

    /**
     * 异步提交paydayloan信审流程
     * @param appForm
     * @return
     */
    public abstract function paydayLoanAsyn($appForm);

    /**
     * 查询审批状态
     *
     * @return
     * @throws Exception
     */
    public abstract function approveQuery($param);

    /**
     * 上传文件
     * @param url
     * @param fileName 文件名
     * @param is 文件流
     * @return
     * @throws Exception
     */
    public abstract function uploadFile($url, $fileName, $filePath);

    /**
     * 通用接口 web
     *
     * @return
     * @throws Exception
     */
    public abstract function request($param, $productCode);


    /**
     * 将array(map)型参数转换为json格式
     */
    public function paramToJsonString($param) {
        return json_encode($param);
    }

    /**
     * 将返回json数据转换为对象返回
     */
    public function jsonResultToObject($result) {

        $res = new Response();
        if(empty($result)) {
            $res->resCode = Response::$STATE_FAIL;
            $res->resMsg = "请求失败:返回结果为空!";
            return $res;
        } else {
            $temp = json_decode($result, true);
            $res->resCode = $temp['resCode'];
            $res->resMsg = $temp['resMsg'];

            if (array_key_exists('handlerData', $temp)) {
                $handlerData = $temp['handlerData'];

                $appResult = new AppResult();
                $appResult->advice = $handlerData['advice'];
                $appResult->creditLimit = $handlerData['creditLimit'];
                $appResult->creditTerm = $handlerData['creditTerm'];
                $appResult->reasonCode = $handlerData['reasonCode'];

                if (array_key_exists('reason', $handlerData)) {
                    $appResult->reason = $handlerData['reason'];              // 决策原因
                }
                if (array_key_exists('interestCode', $handlerData)) { // 利率码
                    $appResult->interestCode = $handlerData['interestCode'];
                }
                $appResult->amtDownpay = $handlerData['amtDownpay'];       // 首付款
                $appResult->amtMonthrepay = $handlerData['amtMonthrepay']; // 每期还款

                if (array_key_exists('feeRateCode', $handlerData)) {   // 费率码
                    $appResult->feeRateCode = $handlerData['feeRateCode'];
                }
                $appResult->noBus = $handlerData['noBus'];        // 在信申系统唯一的请求id
                $appResult->noBusb = $handlerData['noBusb'];      // 客户的请求id
                $appResult->dataProd = $handlerData['dataProd'];  // 审批明细数据
                $res->handlerData = $appResult;
            }

            return $res;
        }
    }

    public function jsonResultToObjectCommon($result) {
        $res = new Response();
        if(empty($result)) {
            $res->resCode = Response::$STATE_FAIL;
            $res->resMsg = "请求失败:请求返回结果为空!";
            return $res;
        } else {
            $temp = json_decode($result, true);
            $res->resCode = $temp['resCode'];
            $res->resMsg = $temp['resMsg'];
            $res->handlerData = $temp['handlerData'];
            return $res;
        }
    }

}