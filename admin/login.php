<?php
require_once ('../config.php');
session_start();

function login(){
    //校验
    //持久化 (记住填写过的用户名)
    //响应
    if (empty($_POST['email'])) {
        $GLOBALS['msg'] = '请填写邮箱';
        return;
    }
    if (empty($_POST['password'])) {
        $GLOBALS['msg'] = '请填写密码';
        return;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$conn) {
        exit('<h2>数据库链接失败！</h2>');
    }

    $query = mysqli_query($conn, "select * from users where email = '{$email}' limit 1;");
    if (!$query) {
        $GLOBALS['msg'] = '登录失败！';
        return;
    }

    $user = mysqli_fetch_assoc($query);

    if (!$user) {
        $GLOBALS['msg'] = '用户名不存在';
        return;
    }

    if ($user['password'] !== md5($password)) {
        $GLOBALS['msg'] = '邮箱和密码不匹配';
        return;
    }

    //存储一个登录标识
    $_SESSION['cur_user'] = $user;

    header('Location: /admin/');//默认打开index.php
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    login();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Sign in &laquo; Admin</title>
    <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/static/assets/vendors/animate/animate.min.css">
    <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
<div class="login">
    <!-- 可以在form上加上 novalidate 去掉浏览器的自动校验功能 -->
    <form class="login-wrap<?php echo isset($msg)? ' animated shake':''; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off">
        <img class="avatar" src="/static/assets/img/default.png">
        <!-- 有错误信息时展示 -->
        <?php if (isset($msg)): ?>
            <div class="alert alert-danger">
                <strong>错误：</strong><?php echo $msg; ?>
            </div>
        <?php endif ?>

        <div class="form-group">
            <label for="email" class="sr-only">邮箱</label>
            <input id="email" type="email" name="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email'])? $_POST['email']: ''; ?>">
        </div>
        <div class="form-group">
            <label for="password" class="sr-only">密码</label>
            <input id="password" type="password" name="password" class="form-control" placeholder="密码">
        </div>
        <button class="btn btn-primary btn-block">登 录</button>
    </form>
</div>

<script src="/static/assets/vendors/jquery/jquery.min.js"></script>
<script>
    $(function ($){
        //根据邮箱获取用户头像
        let reg = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;

        $("#email").on("blur", function (){
            let val = $(this).val();

            //忽略掉空格和非邮箱格式
            if (!val || !reg.test(val)) return;

            //连接数据库找到对应邮箱相应的头像
            $.get('/admin/api/avatar.php', {email: val}, function (res){
                if(!res) return;
                $(".avatar").fadeOut(function (){
                    $(this).attr('src', res).fadeIn();
                });
            });
        })
    })
</script>
</body>
</html>