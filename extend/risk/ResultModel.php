<?php
namespace risk;

class ResultModel
{
    /**
     * 请求结果数据，当error不为“0000”时，data为null
     */
    public $handlerData = "";
    /**
     * 返回码,为“0000”时，表示请求成功
     */
    public $resCode;
    /**
     * 提示信息
     */
    public $resMsg;

    public function __toString()
    {
        return "{\"handlerData\":" . $this->handlerData . ",\"resCode\":\"" . $this->resCode . "\",\"resMsg\":\"" . $this->resMsg . "\"}";
    }
}