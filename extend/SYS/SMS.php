<?php
namespace SYS;

class SMS
{
    private $http = '';		//短信接口
    private $uid = '';				//用户编号
    private $account = '';			//用户账号
    private $pwd = '';				//密码
    public function send($mobile, $message, $time='')
    {
        $result = array('status'=> -1, 'message'=> '未知错误', 'data'=> [], 'time'=> time());
        $params = array(
            'userid'=>$this->uid,					// 用户编号
            'account'=>$this->account,			    // 用户账号
            'password'=>$this->pwd,			//
            'mobile'=>$mobile,				// 号码
            'content'=>'【米花金服】'.$message,			// 内容
            'action'=>'send',			    // 内容
            'time'=>$time,					// 定时发送
        );
        $re= $this->postSMS_XML($this->http, $params);				//POST方式提交
        if ($re['status'] == 200) {
            $value_array = $re['data'];
            if ($value_array['returnstatus'] == 'Success') {
                $result['data'] = array(
                    'id'=> $value_array['taskID'],  // 返回本次任务的序列ID
                    'overage'=> $value_array['remainpoint'],  // 返回余额
                    'counts'=> $value_array['successCounts'], // 当成功后返回提交成功短信数
                );
            }
            $result['status'] = $re['status'];
            $result['message'] = $value_array['message'];

        } else {
            $result['status'] = $re['status'];
            $result['message'] = '获取失败:服务请求错误';
        }
        return $result;
    }
    public function get()
    {
        $result = array('status'=> -1, 'message'=> '未知错误', 'data'=> [], 'time'=> time());
        $params = array(
            'userid'=>$this->uid,					// 用户编号
            'account'=>$this->account,			    // 用户账号
            'password'=>$this->pwd,			        // 密码
            'action'=>'overage',			        // 任务名
        );
        $re = $this->postSMS_XML($this->http, $params);				//POST方式提交
        if ($re['status'] == 200) {
            $value_array = $re['data'];
            if ($value_array['returnstatus'] == 'Sucess') {
                $result['data'] = array(
                    'paytype'=> $value_array['payinfo'],  // 返回支付方式  后付费，预付费
                    'overage'=> $value_array['overage'],  // 返回余额
                    'total'=> $value_array['sendTotal'], // 返回总点数  当支付方式为预付费是返回总充值点数
                );
            }
            $result['status'] = $re['status'];
            $result['message'] = $value_array['returnstatus'];

        } else {
            $result['status'] = $re['status'];
            $result['message'] = '获取失败:服务请求错误';
        }
        return $result;
    }
    public function postSMS_JSON($url, $data)
    {
        $port="";
        $post="";
        $row = parse_url($url);
        $host = $row['host'];
        $port = array_key_exists('port', $row) ? $row['port']:80;
        $file = $row['path'];
        while (list($k,$v) = each($data))	{
            $post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
        }
        $post = substr( $post , 0 , -1 );
        $len = strlen($post);
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n",$receive);
            unset($receive[0]);
            return implode("",$receive);
        }
    }
    public function postSMS_XML($url, $data)
    {
        $post="";
        while (list($k,$v) = each($data))	{
            $post .= rawurlencode($k)."=".rawurlencode($v)."&";	//转URL标准码
        }
        $post = substr( $post , 0 , -1 );

        $len = strlen($post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//                'Content-Type: application/json; charset=utf-8',
//                'Content-Length: ' . $len
//            )
//        );
        $return_xml = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $value_array = json_decode(json_encode(simplexml_load_string($return_xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        return array('status'=> $httpCode, 'data' => $value_array);
    }
}