<?php
namespace app\api\controller;

class Index extends Base
{
    public function index()
    {
    	$data = ['id'=>'001','url'=>url('Order/Index/index')];
        // 指定json数据输出
        return json(['data'=>$data,'status'=> 200,'message'=>'操作完成']);
    }
    public function times()
    {
        $t = time();
        echo date('Y-m-d H:i:s',strtotime('+5 day', $t));
    }
    public function login()
    {
        $params = array(
            'id' => 'aaaa',
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => 1356999524,
            "nbf" => 1357000000
        );
        $token = parent::encode($params);
        $decoded = parent::decode($token);
        echo $token . '<br/>';
        var_dump($decoded);
    }
    public function upload()
    {
        $result = array('status' => -1, 'message' => '未知错误','timestamp' => time());
        $file = request()->file('idcard');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            $fileExt = $info->getExtension();
            $filePath = $info->getSaveName();
            $fileName = $info->getFilename();
            $data = array(
                'ext' => $fileExt,
                'path' => $filePath,
                'name' => $fileName
            );
            $result['status'] = 200;
            $result['message'] = 'success';
            $result['data'] = $data;
        }else{
            // 上传失败获取错误信息
            $result['status'] = 301;
            $result['message'] = $file->getError();
        }
        return json($result);
    }
}
