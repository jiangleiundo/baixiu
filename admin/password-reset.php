<?php
require_once('../common.php');
$cur_user = bx_is_login();

//修改密码
function mod_password()
{
    global $cur_user;

    $id = $cur_user['id'];
    $old_pw = md5($_POST['pw1']);
    $new_pw = md5($_POST['pw2']);
    $new_pw2 = md5($_POST['pwc']);

    if (empty($_POST['pw1']) || empty($_POST['pw2']) || empty($_POST['pwc'])) {
        $GLOBALS['msg'] = '密码不能为空';
        return;
    }

    if ($new_pw !== $new_pw2) {
        $GLOBALS['msg'] = '两次密码不一致';
        return;
    }

    if ($old_pw !== $cur_user['password']) {
        $GLOBALS['msg'] = '旧密码不正确';
        return;
    }

    $rows = bx_execute("update users set password = '{$new_pw2}' where id = {$id};");

    $GLOBALS['msg'] = $rows <= 0 ? '修改密码失败' : '修改密码成功';
    $GLOBALS['success'] = $rows > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    mod_password();
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Password reset &laquo; Admin</title>
    <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
    <link rel="stylesheet" href="/static/assets/css/admin.css">
    <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
<script>NProgress.start()</script>

<div class="main">
    <?php include('inc/navbar.php'); ?>
    <div class="container-fluid">
        <div class="page-title">
            <h1>修改密码</h1>
        </div>
        <?php if (isset($msg)): ?>
            <div class="alert<?php echo isset($success) ? ' alert-success' : ' alert-danger'; ?>">
                <strong>提示：</strong><?php echo $msg; ?>
            </div>
        <?php endif ?>
        <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="old" class="col-sm-3 control-label">旧密码</label>
                <div class="col-sm-7">
                    <input id="old" class="form-control" type="password" name="pw1" placeholder="旧密码">
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-3 control-label">新密码</label>
                <div class="col-sm-7">
                    <input id="password" class="form-control" type="password" name="pw2" placeholder="新密码">
                </div>
            </div>
            <div class="form-group">
                <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
                <div class="col-sm-7">
                    <input id="confirm" class="form-control" type="password" name="pwc" placeholder="确认新密码">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-7">
                    <button type="submit" class="btn btn-primary">修改密码</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $cur_page = 'password-reset'; ?>
<?php include('inc/sidebar.php'); ?>

<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script>NProgress.done()</script>
</body>
</html>
