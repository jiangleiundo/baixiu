<?php

require_once '../../common.php';

//根据客户端传递的id删除数据
if (empty($_GET['id'])) {
    exit('缺少参数id');
}

$ids = $_GET['id'];

$id_check = explode(',', $ids);
for ($i = 0; $i < count($id_check); $i++) {
    if (!is_numeric($id_check[$i])) { //检测变量是否为数字或数字字符串
        exit('参数有错误！');
    }
}

$rows = bx_execute('delete from comments where id in (' . $ids . ');');

header('Content-type: application/json');
echo json_encode($rows > 0);