<?php header('Content-type: text/html; charset=utf-8');
require_once('../common.php');
bx_is_login();

if (!empty($_GET['id'])) {
    $cur_edit = bx_fetch_one('select * from users where id =' . $_GET['id']);
}

function bx_mod_user()
{
    global $cur_edit;

    $email = empty($_POST['email']) ? $cur_edit['email'] : $_POST['email'];
    $slug = empty($_POST['slug']) ? $cur_edit['slug'] : $_POST['slug'];
    $nickname = empty($_POST['nickname']) ? $cur_edit['nickname'] : $_POST['nickname'];
    $password = empty($_POST['password']) ? $cur_edit['password'] : md5($_POST['password']);
    $id = $cur_edit['id'];

    $cur_edit['email'] = $email;
    $cur_edit['slug'] = $slug;
    $cur_edit['nickname'] = $nickname;

    //修改数据
    $rows = bx_execute("update users set email = '{$email}',slug = '{$slug}',nickname = '{$nickname}', password = '{$password}' where id = {$id};");

    $GLOBALS['msg'] = $rows <= 0 ? '修改失败' : '修改成功';
    if (is_numeric($rows)) {
        $GLOBALS['success'] =  $rows > 0;
    }
}

function bx_add_user()
{
    if (empty($_POST['email'])) {
        $GLOBALS['msg'] = '请填写邮箱';
        return;
    }

    if (empty($_POST['slug'])) {
        $GLOBALS['msg'] = '请输入别名';
        return;
    }

    if (empty($_POST['nickname'])) {
        $GLOBALS['msg'] = '请输入昵称';
        return;
    }

    if (empty($_POST['password'])) {
        $GLOBALS['msg'] = '请输入密码';
        return;
    }

    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = md5($_POST['password']);
    $avatar = '/static/uploads/avatar.jpg';
    $bio = '';
    $status = 'activated';

    //添加数据
    $rows = bx_execute("insert into users values(null, '{$slug}','{$email}','{$password}','{$nickname}','{$avatar}','{$bio}','{$status}');");

    $GLOBALS['msg'] = $rows <= 0 ? '添加失败' : '添加成功';
    $GLOBALS['success'] = $rows > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_GET['id'])) {
        bx_add_user();
    } else {
        bx_mod_user();
    }
}

$users = bx_fetch_all('select * from users');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Users &laquo; Admin</title>
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
            <h1>用户</h1>
        </div>
        <!-- 有错误信息时展示 -->
        <?php if (isset($msg)): ?>
            <div class="alert<?php echo isset($success) ? ' alert-success' : ' alert-danger'; ?>">
                <strong>提示！</strong><?php echo $msg; ?>
            </div>
        <?php endif ?>
        <div class="row">
            <div class="col-md-4">
                <?php if (isset($cur_edit)): ?>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $cur_edit['id']; ?>" method="post">
                        <h2>修改<?php echo $cur_edit['nickname']; ?></h2>
                        <div class="form-group">
                            <label for="email">邮箱</label>
                            <input id="email" class="form-control" name="email"
                                   value="<?php echo $cur_edit['email']; ?>"
                                   type="email" placeholder="邮箱">
                        </div>
                        <div class="form-group">
                            <label for="slug">别名</label>
                            <input id="slug" class="form-control" name="slug" value="<?php echo $cur_edit['slug']; ?>"
                                   type="text" placeholder="slug">
                        </div>
                        <div class="form-group">
                            <label for="nickname">昵称</label>
                            <input id="nickname" class="form-control" name="nickname"
                                   value="<?php echo $cur_edit['nickname']; ?>" type="text" placeholder="昵称">
                        </div>
                        <div class="form-group">
                            <label for="password">密码</label>
                            <input id="password" class="form-control" name="password" type="text" placeholder="密码">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">更新</button>
                            <a class="btn btn-default" href="/admin/users.php">取消</a>
                        </div>
                    </form>
                <?php else: ; ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <h2>添加新用户</h2>
                        <div class="form-group">
                            <label for="email">邮箱</label>
                            <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
                        </div>
                        <div class="form-group">
                            <label for="slug">别名</label>
                            <input id="slug" class="form-control" name="slug"
                                   type="text" placeholder="slug">
                        </div>
                        <div class="form-group">
                            <label for="nickname">昵称</label>
                            <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
                        </div>
                        <div class="form-group">
                            <label for="password">密码</label>
                            <input id="password" class="form-control" name="password" type="text" placeholder="密码">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">添加</button>
                        </div>
                    </form>
                <?php endif ?>
            </div>
            <div class="col-md-8">
                <div class="page-action">
                    <!-- show when multiple checked -->
                    <a class="btn btn-danger btn-sm" style="display: none">批量删除</a>
                </div>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="text-center" width="40"><input type="checkbox"></th>
                        <th class="text-center" width="80">头像</th>
                        <th>邮箱</th>
                        <th>别名</th>
                        <th>昵称</th>
                        <th>状态</th>
                        <th class="text-center" width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $item): ?>
                        <tr>
                            <td class="text-center"><input type="checkbox"></td>
                            <td class="text-center"><img class="avatar" src="<?php echo $item['avatar']; ?>"></td>
                            <td><?php echo $item['email']; ?></td>
                            <td><?php echo $item['slug']; ?></td>
                            <td><?php echo $item['nickname']; ?></td>
                            <td><?php echo $item['status'] === 'activated' ? '激活' : '未激活'; ?></td>
                            <td class="text-center">
                                <a href="/admin/users.php?id=<?php echo $item['id']; ?>" class="btn btn-default btn-xs">编辑</a>
                                <a href="/admin/api/user-del.php?id=<?php echo $item['id']; ?>"
                                   class="btn btn-danger btn-xs">删除</a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $cur_page = 'users'; ?>
<?php include('inc/sidebar.php'); ?>

<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script>NProgress.done()</script>
</body>
</html>