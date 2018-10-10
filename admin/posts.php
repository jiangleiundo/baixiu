<?php header('Content-type: text/html; charset=utf-8');
error_reporting(E_ALL ^ E_NOTICE); //关闭掉 NOTICE错误的警告

require_once('../common.php');
bx_is_login();

//筛选===========================================================
$where = '1=1';
$search = '';

$cur_cat_id = $_GET['category']; //当前筛选分类id
$cur_status = $_GET['status']; //当前选中状态

if (isset($cur_cat_id) && $cur_cat_id !== 'all') {
    $where .= ' and posts.category_id =' . $cur_cat_id;
    $search .= '&category=' . $cur_cat_id;
}

if (isset($cur_status) && $cur_status !== 'all') {
    $where .= " and posts.status ='{$cur_status}'";
    $search .= '&status=' . $cur_status;
}

//处理分页========================================================
$size = 20; //分页条数
$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];

//获取总数
$totalCount = bx_fetch_one("select count(1) as count from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
where {$where};");
$totalPage = (int)ceil((int)$totalCount['count'] / $size);

//判断是否非法页数
if ($page < 1) header('Location: /admin/posts.php?page=1' . $search);
if ($page > $totalPage) header('Location: /admin/posts.php?page=' . $totalPage . $search);

$offset = ($page - 1) * $size; //越过几条取数据//越过几条取数据

$visible = 7; //可见页码数
$region = ($visible - 1) / 2;
$begin = $page - $region;
$end = $page + $region;

if ($begin < 1) {
    $begin = 1;
    $end = $begin + $visible - 1;
}

if ($end > $totalPage) {
    $end = $totalPage;
    $begin = $end - $visible + 1;
    if ($begin < 1) {
        $begin = 1;
    }
}//end 分页

//获取所有数据-关联数组查询==============================================
$posts = bx_fetch_all("select 
posts.id,
posts.title,
users.nickname as user_name,
categories.name as category_name, 
posts.created,
posts.status
from posts
inner join categories on category_id = categories.id
inner join users on user_id = users.id
where {$where}
order by posts.created desc
limit {$offset}, {$size}");

// 查询全部的分类数据
$categories = bx_fetch_all('select * from categories;');

//处理格式转换==========================================================
/**
 * 转换状态显示
 * @param $status [状态 英文]
 * @return mixed|string [中文]
 */
function convert_status($status)
{
    $dict = array(
        'published' => '已发布',
        'drafted' => '草稿',
        'trashed' => '回收站'
    );

    return isset($dict[$status]) ? $dict[$status] : '未知状态';
}

/**
 * 日期类型转换
 * @param $created [2018-10-01 08:08:00]
 * @return false|string [2018年10月01日 08:08:00]
 */
function convert_date($created)
{
    $timestamp = strtotime($created); //拿到时间戳
    return date('Y年m月d日<b\r>H:i:s', $timestamp);
}

//以下方法可以使用但是要查询多次，弃用改用关联数组查询节省查询时间
//function get_category($category_id){
//    $category =  bx_fetch_one("select name from categories where id = {$category_id}");
//    return $category['name'];
//
//    //php5.4+可使用下面语句
//    //return bx_fetch_one("select name from categories where id = {$category_id}")['name'];
//}
//
//function get_user($user_id){
//    $user =  bx_fetch_one("select nickname from users where id = {$user_id}");
//    return $user['nickname'];
//}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Posts &laquo; Admin</title>
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
            <h1>所有文章</h1>
            <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
        </div>
        <!-- 有错误信息时展示 -->
        <!-- <div class="alert alert-danger">
          <strong>错误！</strong>发生XXX错误
        </div> -->
        <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
            <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <select name="category" class="form-control input-sm">
                    <option value="all">所有分类</option>
                    <?php foreach ($categories as $item): ?>
                        <option value="<?php echo $item['id']; ?>" <?php echo isset($cur_cat_id) && $cur_cat_id === $item['id'] ? 'selected' : ''; ?>>
                            <?php echo $item['name']; ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <select name="status" class="form-control input-sm">
                    <option value="all">所有状态</option>
                    <option value="drafted"<?php echo isset($_GET['status']) && $_GET['status'] == 'drafted' ? ' selected' : '' ?>>
                        草稿
                    </option>
                    <option value="published"<?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? ' selected' : '' ?>>
                        已发布
                    </option>
                    <option value="trashed"<?php echo isset($_GET['status']) && $_GET['status'] == 'trashed' ? ' selected' : '' ?>>
                        回收站
                    </option>
                </select>
                <button class="btn btn-default btn-sm">筛选</button>
            </form>
            <ul class="pagination pagination-sm pull-right">
                <?php if ($begin > 1): ?>
                    <li><a href="/admin/posts.php?page=1<?php echo $search; ?>">首页</a></li>
                <?php endif ?>
                <li><a href="?page=<?php echo $page > 1 ? $page - 1 : 1; ?>">上一页</a></li>
                <?php for ($i = $begin; $i <= $end; $i++): ?>
                    <li <?php echo $i === $page ? "class='active'" : ''; ?>>
                        <a href="?page=<?php echo $i . $search; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor ?>
                <li><a href="?page=<?php echo $page < $totalPage ? $page + 1 : $totalPage; ?>">下一页</a></li>
                <?php if ($end < $totalPage): ?>
                    <li><a href="/admin/posts.php?page=<?php echo $totalPage . $search; ?>">尾页</a></li>
                <?php endif ?>
            </ul>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>标题</th>
                <th>作者</th>
                <th>分类</th>
                <th class="text-center">发表时间</th>
                <th class="text-center">状态</th>
                <th class="text-center" width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($posts as $item): ?>
                <tr>
                    <td class="text-center"><input type="checkbox"></td>
                    <td><?php echo $item['title']; ?></td>
                    <td><?php echo $item['user_name']; ?></td>
                    <td><?php echo $item['category_name']; ?></td>
                    <!--                    <td>--><?php //echo get_user($item['user_id']); ?><!--</td>-->
                    <!--                    <td>--><?php //echo get_category($item['category_id']); ?><!--</td>-->

                    <td class="text-center"><?php echo convert_date($item['created']); ?></td>
                    <!-- *****  一旦输出的逻辑或转换逻辑过于复杂不建议直接写在混编位置 -->
                    <td class="text-center"><?php echo convert_status($item['status']); ?></td>
                    <td class="text-center">
                        <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
                        <a href="/admin/posts-delete.php?id=<?php echo $item['id'].$search; ?>" class="btn btn-danger btn-xs">删除</a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?php $cur_page = 'posts'; ?>
<?php include('inc/sidebar.php'); ?>

<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script>NProgress.done()</script>
</body>
</html>
