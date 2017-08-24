<?php
namespace risk;

interface CafintechHttpClient
{
    const VERSION = "1.2";

    public function authenReq($param, $productCode);
    public function postFile($url,$fileName,$is);
    public function postPBOCFile($url,$fileName,$is);
}