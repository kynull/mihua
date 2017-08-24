<?php
return [
    'website' => [
        'name'     => '米花金服'
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'             => APP_PATH  . 'www/view/' .DS. 'dispatch_jump.tpl',
    'dispatch_error_tmpl'               => APP_PATH  . 'www/view/' .DS. 'dispatch_jump.tpl',

    //异常页面模板文件
    'exception_tmpl'                    => APP_PATH . 'www/view' .DS. 'think_exception.tpl',

    'http_exception_template'           =>  [
        // 定义404错误的重定向页面地址
        404 =>  APP_PATH. 'www/view' .DS. '404.html',
        // 还可以定义其它的HTTP status
        401 =>  APP_PATH. 'www/view' .DS. '401.html',
    ],
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__CSS__'    => STATIC_PATH . 'css',
        '__JS__'     => STATIC_PATH . 'js',
        '__IMG__'    => STATIC_PATH . 'images',
        '__LIB__'    => STATIC_PATH . 'lib'
    ],
    // URL伪静态后缀 防止生成的AJAX地址加后缀后访问异常
    'url_html_suffix' => false,
];