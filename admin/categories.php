<?php header('Content-type: text/html; charset=utf-8');
require_once('../common.php');
//判断用户是否登录
bx_is_login();

//有id就取数据
if (!empty($_GET['id'])) {
    $cur_edit = bx_fetch_one('select * from categories where id =' . $_GET['id']);
}

//添加数据
function bx_add_category()
{
    if (empty($_POST['name']) || empty($_POST['slug'])) {
        $GLOBALS['msg'] = '请完整填写表单！';
        return;
    }

    //接受数据并保存
    $name = $_POST['name'];
    $slug = $_POST['slug'];

    //添加数据
    $rows = bx_execute("insert into categories values(null, '{$slug}', '{$name}');");

    $GLOBALS['msg'] = $rows <= 0 ? '添加失败' : '添加成功';
    $GLOBALS['success'] = $rows > 0;
}

//修改分类
function bx_mod_category(){
    global $cur_edit;

    $name = empty($_POST['name'])? $cur_edit['name']: $_POST['name'];
    $slug = empty($_POST['slug'])? $cur_edit['slug']: $_POST['slug'];
    $id = $cur_edit['id'];

    //更新页面数据
    $cur_edit['name'] = $name;
    $cur_edit['slug'] = $slug;

    //修改数据
    $rows = bx_execute("update categories set slug = '{$slug}', name = '{$name}' where id = {$id};");

    $GLOBALS['msg'] = $rows <= 0 ? '修改失败' : '修改成功';
    $GLOBALS['success'] = $rows > 0;
}

//修改添加再现，获取数据在后
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_GET['id'])) {
        bx_add_category();
    } else {
        bx_mod_category();
    }
}

//获取分类信息
$categories = bx_fetch_all('select * from categories');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Categories &laquo; Admin</title>
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
            <h1>分类目录</h1>
        </div>
        <?php if (isset($msg)): ?>
            <div class="alert<?php echo isset($success) ? ' alert-success' : ' alert-danger'; ?>">
                <strong>提示！</strong><?php echo $msg; ?>
            </div>
        <?php endif ?>
        <div class="row">
            <div class="col-md-4">
                <?php if (isset($cur_edit)): ?>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $cur_edit['id']; ?>" method="post">
                        <h2>修改--<?php echo $cur_edit['name']; ?></h2>
                        <div class="form-group">
                            <label for="name">名称</label>
                            <input id="name" class="form-control" name="name" type="text" value="<?php echo $cur_edit['name']; ?>" placeholder="分类名称">
                        </div>
                        <div class="form-group">
                            <label for="slug">别名</label>
                            <input id="slug" class="form-control" name="slug" type="text" value="<?php echo $cur_edit['slug']; ?>" placeholder="slug">
<!--                            <p class="help-block">https://zce.me/category/<strong>slug</strong></p>-->
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">保存</button>
                            <a href="/admin/categories.php" class="btn btn-default">取消</a>
                        </div>
                    </form>
                <?php else: ; ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <h2>添加新分类目录</h2>
                        <div class="form-group">
                            <label for="name">名称</label>
                            <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
                        </div>
                        <div class="form-group">
                            <label for="slug">别名</label>
                            <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
<!--                            <p class="help-block">https://zce.me/category/<strong>slug</strong></p>-->
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
                    <a id="delAll" class="btn btn-danger btn-sm" href="/admin/categories-delete.php"
                       style="display: none;">批量删除</a>
                </div>
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="text-center" width="40"><input id="allSel" type="checkbox"></th>
                        <th>名称</th>
                        <th>Slug（别名）</th>
                        <th class="text-center" width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $item): ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['slug']; ?></td>
                            <td class="text-center">
                                <a href="/admin/categories.php?id=<?php echo $item['id']; ?>"
                                   class="btn btn-info btn-xs">编辑</a>
                                <a href="/admin/categories-delete.php?id=<?php echo $item['id']; ?>"
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
<?php $cur_page = 'categories'; ?>
<?php include('inc/sidebar.php'); ?>

<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script>
    //批量删除功能
    let $tbodyInput = $("tbody input");
    let $delAllBtn = $("#delAll");
    let $allSel = $("#allSel");
    let selCount = []; //批量删除id的数组

    $allSel.on("change", function () {
        let flag = $(this).prop('checked');

        for (let i = 0; i < $tbodyInput.length; i++) {
            let that = $($tbodyInput[i]);

            that.prop('checked', flag);
            if (flag) {
                selCount.push(that.data('id'));
            }
        }

        if (!flag) selCount = [];
        selCount.length > 0 ? $delAllBtn.fadeIn() : $delAllBtn.fadeOut();
    });

    //CheckBox被选中时批量删除按钮出现
    $tbodyInput.on('change', function () {
        let id = $(this).data('id');

        if ($(this).prop('checked')) {
            selCount.push(id);
        } else {
            selCount.splice(selCount.indexOf(id), 1);
        }

        selCount.length > 0 ? $delAllBtn.fadeIn() : $delAllBtn.fadeOut();

        $allSel.prop('checked', selCount.length === $tbodyInput.length);
        $delAllBtn.prop('search', '?id=' + selCount);
    });
</script>
<script>NProgress.done()</script>
</body>
</html>