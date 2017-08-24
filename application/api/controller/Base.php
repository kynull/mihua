<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 17/5/26
 * Time: 下午4:40
 */

namespace app\api\controller;
use app\common\controller\CommonController;
use app\common\tools\Token;

class Base extends CommonController
{
    public function encode($token)
    {
        return Token::encode($token);
    }
    public function decode($jwtCode)
    {
        return Token::decode($jwtCode);
    }
}