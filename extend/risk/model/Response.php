<?php
namespace risk\model;


class Response
{
    public static $STATE_FAIL = "-1001";
    public static $SUCCESS = "0000";
    public static $SUCCESS_MESSAGE = "成功";

    /**
     * 返回码
     */
    public $resCode;
    /**
     * 返回信息
     */
    public $resMsg;
    /**
     * 返回数据
     */
    public $handlerData;
}