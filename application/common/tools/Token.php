<?php
/**
 * Created by PhpStorm.
 * User: carey
 * Date: 17/5/31
 * Time: 下午4:49
 */

namespace app\common\tools;
use \Firebase\JWT\JWT;

class Token
{
    private static $key = 'example_key';
    public static function encode($token)
    {
        return JWT::encode($token, static::$key);
    }
    public static function decode($jwtCode)
    {
        return JWT::decode($jwtCode, static::$key, array('HS256'));
    }
}