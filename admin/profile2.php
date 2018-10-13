<?php header('Content-type: text/html; charset=utf-8');
require_once('../common.php');
$cur_user = bx_is_login();
$cur_user_pic = !empty($cur_user['avatar']) ? $cur_user['avatar'] : '/static/assets/img/default.png';

//修改个人中心
function profile_mod()
{
    global $cur_user, $cur_user_pic;

    $id = $cur_user['id'];
    $avatar = empty($_POST['avatar']) ? $cur_user['avatar'] : $_POST['avatar'];
    $slug = empty($_POST['slug']) ? $cur_user['slug'] : $_POST['slug'];
    $nickname = empty($_POST['nickname']) ? $cur_user['nickname'] : $_POST['nickname'];
    $bio = empty($_POST['bio']) ? $cur_user['bio'] : $_POST['bio'];

    //更新
    $cur_user_pic = $avatar;
    $cur_user['slug'] = $slug;
    $cur_user['nickname'] = $nickname;
    $cur_user['bio'] = $bio;

    $rows = bx_execute("update users set avatar = '{$avatar}',slug = '{$slug}', nickname='{$nickname}', bio='{$bio}' where id = {$id};");

    $GLOBALS['msg'] = $rows <= 0 ? '修改失败' : '修改成功';
    $GLOBALS['success'] = $rows > 0;
}

function mod_password()
{
    global $cur_user;

    $id = $cur_user['id'];
//    $old_pw = md5($_POST['pw1']);
//    $new_pw = md5($_POST['pw2']);
    $new_pw2 = md5($_POST['pwc']);

//    if (empty($old_pw) || empty($new_pw) || empty($new_pw2)) {
//        return;
//    }
//
//    if ($new_pw !== $new_pw2) {
//        $GLOBALS['msg'] = '两次密码不一致';
//        return;
//    }
//
//    if ($old_pw !== $cur_user['password']) {
//        $GLOBALS['msg'] = '旧密码不正确';
//        return;
//    }

    $rows = bx_execute("update users set password = '{$new_pw2}' where id = {$id};");

    $GLOBALS['msg'] = $rows <= 0 ? '修改密码失败' : '修改密码成功';
    $GLOBALS['success'] = $rows > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_GET['mod'] === 'password') {
        mod_password();
    } else {
        profile_mod();
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Dashboard &laquo; Admin</title>
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
            <h1>我的个人资料</h1>
        </div>
        <!-- 有错误信息时展示 -->
        <?php if (isset($msg)): ?>
            <div class="alert<?php echo isset($success) ? ' alert-success' : ' alert-danger'; ?>">
                <strong>提示！</strong><?php echo $msg; ?>
            </div>
        <?php endif ?>
        <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label class="col-sm-3 control-label">头像</label>
                <div class="col-sm-6">
                    <label class="form-image">
                        <input id="avatar" type="file">
                        <img src="<?php echo $cur_user_pic; ?>">
                        <input type="hidden" name="avatar">
                        <i class="mask fa fa-upload"></i>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-sm-3 control-label">邮箱</label>
                <div class="col-sm-6">
                    <input id="email" class="form-control" name="email" type="email"
                           value="<?php echo $cur_user['email']; ?>" placeholder="邮箱"
                           readonly>
                    <p class="help-block">登录邮箱不允许修改</p>
                </div>
            </div>
            <div class="form-group">
                <label for="slug" class="col-sm-3 control-label">别名</label>
                <div class="col-sm-6">
                    <input id="slug" class="form-control" name="slug" type="text"
                           value="<?php echo $cur_user['slug']; ?>" placeholder="slug">
                    <p class="help-block">https://baixiu.io/author/<strong><?php echo $cur_user['slug']; ?></strong></p>
                </div>
            </div>
            <div class="form-group">
                <label for="nickname" class="col-sm-3 control-label">昵称</label>
                <div class="col-sm-6">
                    <input id="nickname" class="form-control" name="nickname" type="text"
                           value="<?php echo $cur_user['nickname']; ?>" placeholder="昵称">
                    <p class="help-block">限制在 2-16 个字符</p>
                </div>
            </div>
            <div class="form-group">
                <label for="bio" class="col-sm-3 control-label">简介</label>
                <div class="col-sm-6">
                    <textarea id="bio" class="form-control" name="bio" placeholder="Bio" cols="30"
                              rows="6"><?php echo $cur_user['bio']; ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a class="btn btn-link" href="password-reset.php">修改密码</a>
<!--                    <a class="btn btn-link" id="mod_password" data-pw="--><?php //echo $cur_user['password']; ?><!--">修改密码</a>-->
                </div>
            </div>
        </form>
    </div>
    <div class="bx-layer" style="display: none;">
        <div class="alert alert-danger bx-layer-alert" style="display: none">
            <strong>提示！</strong><span></span>
        </div>
        <form class="form-horizontal" id="bx_layer_form" action="<?php echo $_SERVER['PHP_SELF'] . '?mod=password'; ?>"
              method="post">
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
            <!--            <div class="form-group">-->
            <!--                <div class="col-sm-offset-3 col-sm-7">-->
            <!--                    <button type="submit" class="btn btn-primary">修改密码</button>-->
            <!--                </div>-->
            <!--            </div>-->
        </form>
    </div>
</div>
<?php $cur_page = 'profile'; ?>
<?php include('inc/sidebar.php'); ?>

<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script src="/static/assets/vendors/layer/layer.js"></script>
<script src="/static/assets/js/md5.js"></script>
<script src="/static/assets/js/common.js"></script>
<script>
    $('#mod_password').on('click', function () {
        layer.open({
            skin: 'layer-skin-bx',
            title: '修改密码',
            btn: '修改密码',
            area: '500px',
            content: $('.bx-layer').html(),
            yes: function (index, layero) {
                let old = layero.find('#old');
                let password = layero.find('#password');
                let confirm = layero.find('#confirm');

                if (check_password(old, password, confirm)) {
                    $('#bx_layer_form').submit();
                    layer.close(index);
                }
            }
        });
    });

    //检查密码
    function check_password(old, password, confirm) {
        let oldPw0 = $('#mod_password').attr('data-pw');
        let oldPw = $(old).val();
        let newPw = $(password).val();
        let newPw2 = $(confirm).val();

        if (!comm.isEmpty(oldPw) || comm.isEmpty(newPw) || comm.isEmpty(newPw2)) {
            comm.tip('密码不能为空', '.bx-layer-alert');
            return false;
        }
        if (newPw !== newPw2) {
            comm.tip('两次密码不一致', '.bx-layer-alert');
            return false;
        }
        if (hex_md5(oldPw) !== oldPw0) {
            comm.tip('旧密码不正确', '.bx-layer-alert');
            return false;
        }

        return true;
    }

    $('#avatar').on('change', function () {
        let self = $(this);
        let files = self.prop('files');
        if (!files.length) return;

        let file = files[0];
        let data = new FormData();
        data.append('avatar', file);

        let xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/api/upload.php');
        xhr.send(data);

        xhr.onload = function () {
            self.siblings('img').attr('src', this.responseText);
            self.siblings('input').val(this.responseText);
        }
    })
</script>
<script>NProgress.done()</script>
</body>
</html>