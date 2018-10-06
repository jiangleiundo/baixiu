<?php
//该页面是镶嵌在其他页面之中的所以路径要根据所镶嵌的页面来定
require_once '../common.php';
$current_user = bx_is_login();

$cur_page = isset($cur_page) ? $cur_page : '';
//设置当前菜单高亮
function setCls($this_page, $current_page)
{
    return ($this_page === $current_page) ? ' class="active"' : '';
}

//带有二级菜单的高亮显示
function setCls2($cur, $cur_page, $type)
{
    $arr = array();
    $cur_cls = null;

    switch ($cur) {
        case 'menu-posts':
            $arr = array('posts', 'post-add', 'categories');
            break;
        case 'menu-settings':
            $arr = array('nav-menus', 'slides', 'settings');
            break;
        default :
            break;
    }

    if ($type === 1) {
        $cur_cls = in_array($cur_page, $arr) ? ' class="active"' : '';
    }
    if ($type === 2) {
        $cur_cls = in_array($cur_page, $arr) ? ' in' : '';
    }
    if ($type === 3) {
        $cur_cls = in_array($cur_page, $arr) ? '' : ' class="collapsed"';
    }

    return $cur_cls;
}
?>

<div class="aside">
    <div class="profile">
        <img class="avatar" src="<?php echo $current_user['avatar']; ?>">
        <h3 class="name"><?php echo $current_user['nickname']; ?></h3>
    </div>
    <ul class="nav">
        <li<?php echo setCls('index', $cur_page); ?>>
            <a href="/admin/index.php"><i class="fa fa-dashboard"></i>仪表盘</a>
        </li>
        <li<?php echo setCls2('menu-posts', $cur_page, 1); ?>>
            <a href="#menu-posts" <?php echo setCls2('menu-posts', $cur_page, 3); ?> data-toggle="collapse">
                <i class="fa fa-thumb-tack"></i>文章<i class="fa fa-angle-right"></i>
            </a>
            <ul id="menu-posts" class="collapse<?php echo setCls2('menu-posts', $cur_page, 2); ?>">
                <li<?php echo setCls('posts', $cur_page); ?>><a href="/admin/posts.php">所有文章</a></li>
                <li<?php echo setCls('post-add', $cur_page); ?>><a href="/admin/post-add.php">写文章</a></li>
                <li<?php echo setCls('categories', $cur_page); ?>><a href="/admin/categories.php">分类目录</a></li>
            </ul>
        </li>
        <li<?php echo setCls('comments', $cur_page); ?>>
            <a href="/admin/comments.php"><i class="fa fa-comments"></i>评论</a>
        </li>
        <li<?php echo setCls('users', $cur_page); ?>>
            <a href="/admin/users.php"><i class="fa fa-users"></i>用户</a>
        </li>
        <li<?php echo setCls2('menu-settings', $cur_page, 1); ?>>
            <a href="#menu-settings" <?php echo setCls2('menu-settings', $cur_page, 3); ?> data-toggle="collapse">
                <i class="fa fa-cogs"></i>设置<i class="fa fa-angle-right"></i>
            </a>
            <ul id="menu-settings" class="collapse<?php echo setCls2('menu-settings', $cur_page, 2); ?>">
                <li<?php echo setCls('nav-menus', $cur_page); ?>><a href="/admin/nav-menus.php">导航菜单</a></li>
                <li<?php echo setCls('slides', $cur_page); ?>><a href="/admin/slides.php">图片轮播</a></li>
                <li<?php echo setCls('settings', $cur_page); ?>><a href="/admin/settings.php">网站设置</a></li>
            </ul>
        </li>
    </ul>
</div>