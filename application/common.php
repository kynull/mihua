<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 测试使用函数----打印数组
 * @param $array 传入需要打印的数组
 * @return 无
 * @author Cjky <cjky@qq.com>
 */
function p($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

/**
 * 产生随机字符
 * @param $length 产生字符串的长度(必选)
 * @param $numeric 是否为数字
 * @return string 产生的字符串
 * @author Cjky <cjky@qq.com>
 * @example
 * _random(10);		//随机产生10为字母与数字的组合字符串
 * _random(10,1);	//随机产生10为纯数字字符串
 **/
function _random($length, $numeric = 0) {
    PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
    $seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}
/**
 * 获取字符长度
 * @param $str string 传入字符串
 * @param $charset string 编码
 * @return int 该字符串的长度
 * @author Cjky <cjky@qq.com>
 */
function _getStrLens($str='', $charset='utf-8'){
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']	= "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    return count($match[0]);
}
/**
 * 产生一个订单号，以时间为种子生成
 * @return [string] [16位的订单编号]
 */
function createOrderSn(){
    $year=intval(date('Y')) - 2015;
    $month=date('m')+$year*12;
    $day=date('d');
    $three= substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
    return $month.$day.$three;
}
/**
 * @param $old_time
 * @return string
 */
function time_tran($old_time) {
    $now_time = date("Y-m-d H:i:s", time());

    $now_time = strtotime($now_time);
    $show_time = strtotime($old_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return $old_time;
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 259200) {//3天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return $old_time;
                    }
                }
            }
        }
    }
}
function timediff( $begin_time, $end_time )
{
    if ( $begin_time < $end_time ) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval( $timediff / 86400 );
    $remain = $timediff % 86400;
    $hours = intval( $remain / 3600 );
    $remain = $remain % 3600;
    $mins = intval( $remain / 60 );
    $secs = $remain % 60;
    $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
    return $res;
}

function status($code) {
    $result = '未知状态';
    switch ($code) {
        case 0:
            $result = '未填写';
            break;
        case 1:
            $result = '认证驳回';
            break;
        case 2:
            $result = '待认证';
            break;
        case 3:
            $result = '成功认证';
            break;
        default:
            break;
    }
    return $result;
}
function statusHTML($code) {
    $result = '未知状态';
    switch ($code) {
        case 0:
            $result = '<span style="color:grey;">未填写</span>';
            break;
        case 1:
            $result = '<span style="color:orange;">认证驳回</span>';
            break;
        case 2:
            $result = '<span style="color:red;">待认证</span>';
            break;
        case 3:
            $result = '<span style="color:green;">成功认证</span>';
            break;
        default:
            break;
    }
    return $result;
}
function orderStatus($status, $progress) {
    $result = '未知状态';
    switch ($status) {
        case 0:
            $result = '<span style="color:grey;">未确认</span>';
            break;
        case 1:
            switch ($progress) {
                case 0:
                    $result = '<span style="color:grey;">待审核</span>';
                    break;
                case 1:
                    $result = '<span style="color:orange;">审核失败</span>';
                    break;
                case 10:
                    $result = '<span style="color:green;">放款中</span>';
                    break;
                case 11:
                    $result = '<span style="color:green;">用款中</span>';
                    break;
                case 20:
                    $result = '<span style="color:red;">延期失败</span>';
                    break;
                case 21:
                    $result = '<span style="color:orange;">延期申请中</span>';
                    break;
                case 22:
                    $result = '<span style="color:green;">延期中</span>';
                    break;
                case 30:
                    $result = '<span style="color:red;">逾期中</span>';
                    break;
                case 100:
                    $result = '<span style="color:green;">还款成功</span>';
                    break;
                default:
                    break;
            }
            break;
        case 2:
            $result = '<span style="color:orange;">已取消</span>';
            break;
        default:
            break;
    }
    return $result;
}
function contactsRelations($code) {
    $result = '其他';
    switch ($code) {
        case 'CR01':
            $result = '配偶';
            break;
        case 'CR02':
            $result = '父亲';
            break;
        case 'CR03':
            $result = '母亲';
            break;
        case 'CR04':
            $result = '兄弟';
            break;
        case 'CR05':
            $result = '姐妹';
            break;
        case 'CR06':
            $result = '子女';
            break;
        case 'CR07':
            $result = '同学';
            break;
        case 'CR08':
            $result = '同事';
            break;
        case 'CR09':
            $result = '朋友';
            break;
        case 'CR99':
            $result = '其他';
            break;
        default:
            break;
    }
    return $result;
}

/**
 * 验证手机号码是否正确
 * @param $mobile
 */
function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',$mobile) ? true : false;
}
