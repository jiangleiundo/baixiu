<?php header('Content-type: text/html; charset=utf-8');
/**
 * Created by J.
 * User: JACK
 * Date: 2018/10/6 0006
 * Time: 上午 9:49
 */

if (empty($_FILES['avatar'])) {
    exit('上传文件不能为空');
}

$avatar = $_FILES['avatar'];
if ($avatar['error'] !== UPLOAD_ERR_OK) {
    exit('文件上传失败！');
}

//校验
if ($avatar['size'] > 10 * 1024 * 1024) {
    exit('文件过大');
}

//移动文件
$ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
$target = '../../static/uploads/img-' . uniqid() . '.' . $ext;

if (!move_uploaded_file($avatar['tmp_name'], $target)) {
    exit('上传失败');
}

echo substr($target, 5);
