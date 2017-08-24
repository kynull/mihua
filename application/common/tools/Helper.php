<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 17/5/30
 * Time: 下午5:28
 */

namespace app\common\tools;


class Helper
{
    public static function uuid() {
        if (function_exists ( 'com_create_guid' )) {
            return com_create_guid ();
        } else {
            mt_srand ( ( double ) microtime () * 10000 ); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
            $charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) ); //根据当前时间（微秒计）生成唯一id.
            $hyphen = chr ( 45 ); // "-"
            $uuid = '' . //chr(123)// "{"
                substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );
            //.chr(125);// "}"
            return $uuid;
        }
    }
    public static function VerifyUserName($key) {
        $strPhone = "/^[1][3,4,5,7,8]{1}[0-9]{9}$/";
        if(preg_match($strPhone,$key)){
            return true;
        }
        return false;
    }
    public static function getOrderStatus($status, $progress)
    {
        $result = '未知状态';
        switch ($status) {
            case 0:
                $result = '待确认';
                break;
            case 1:
                switch ($progress) {
                    case 0:
                        $result = '待审核';
                        break;
                    case 1:
                        $result = '审核未通过';
                        break;
                    case 10:
                        $result = '放款中';
                        break;
                    case 11:
                        $result = '成功借款';
                        break;
                    case 20:
                        $result = '延期失败';
                        break;
                    case 21:
                        $result = '延期申请中';
                        break;
                    case 22:
                        $result = '延期成功';
                        break;
                    case 30:
                        $result = '已逾期';
                        break;
                    case 99:
                        $result = '待还款';
                        break;
                    case 100:
                        $result = '还款成功';
                        break;
                    default:
                        break;
                }
                break;
            case 2:
                $result = '已取消';
                break;
            default:
                break;
        }
        return $result;
    }
}