<?php
/**
 * Created by J.
 * User: JACK
 * Date: 2018/10/6 0006
 * Time: 下午 8:07
 */

require_once '../common.php';

//根据客户端传递的id删除数据
if (empty($_GET['id'])) {
    exit('缺少参数id');
}

$ids = $_GET['id'];
//如果接受的是类似'1 or 1 = 1'的数据，会删除数据库所有数据，所以需要判断id是否为数字（sql注入）
//检测变量是否为数字或数字字符串

$id_check = explode(',', $ids);
for ($i = 0; $i < count($id_check); $i++) {
    if (!is_numeric($id_check[$i])) {
        exit('参数有错误！');
    }
}

bx_execute('delete from posts where id in (' . $ids . ');');
header('Location: '.$_SERVER['HTTP_REFERER']); //页面请求来源