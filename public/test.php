<?php
// phpinfo();
// die();
function testDatabase()
{
    $dbms='mysql';     // 数据库类型
    $host='localhost'; // 数据库主机名
    $dbName='mihua_loan';    // 使用的数据库
    $user='root';      // 数据库连接用户名
    $pass='Cj147258';          // 对应的密码
    try {
        $dsn="$dbms:host=$host;dbname=$dbName";
        $pdo = new PDO($dsn, $user, $pass);
        $sql = 'SELECT * from mihua_users';
        $rs = $pdo->query($sql);
        while($row = $rs->fetch()) {
            print_r($row);
        }
        $pdo = null;
    } catch(\Exception $e) {
        echo $e;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .code-highlight {
            background-color: #eba612;
        }
        .code-readonly {
            background-color: #262523;
        }
        .not-active {
            background-color: #aca9a7;
        }
    </style>
</head>
<body>
<form action="#">
    <input class="li-input" autocomplete='off' type="tel" name="telInput" id="telInput" placeholder="请输入您的手机号">
    <buttom id="getCodeBtn">提交</buttom>
</form>
<script src="/static/lib/jquery-3.1.0.min.js"></script>
<script>
    $('#telInput').bind('input propertychange', function (e) {
        console.log(this.value, e);
    });

</script>
</body>
</html>
