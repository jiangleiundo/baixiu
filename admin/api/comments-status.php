<?php header('Content-type: application/json');

require_once '../../common.php';

//POST方式也能使用 GET 传参数
if (empty($_GET['id']) || empty($_GET['status'])) {
    exit(json_encode(array(
        'success' => false,
        'message' => '缺少参数id'
    )));
}

$ids = $_GET['id'];

$id_check = explode(',', $ids);
for ($i = 0; $i < count($id_check); $i++) {
    if (!is_numeric($id_check[$i])) { //检测变量是否为数字或数字字符串
        exit(json_encode(array(
            'success' => false,
            'message' => '参数有错误'
        )));
    }
}

$rows = bx_execute(sprintf("update comments set status = '%s' where id in (%s)", $_GET['status'], $_GET['id']));

echo json_encode(array(
    'success'=> $rows > 0
));