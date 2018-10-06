<?php header('Content-type: text/html; charset=utf-8');
/**
 * Created by J.
 * User: JACK
 * Date: 2018/10/6 0006
 * Time: 上午 9:49
 */

$email = $_GET['email'];
if (empty($email)) {
    exit('<h2>缺少必要参数！</h2>');
}

require_once ('../../config.php');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if(!$conn) {
    exit('<h2>数据库连接失败！</h2>');
}

$query = mysqli_query($conn, "select avatar from users where email = '{$email}' limit 1;");
if(!$query) {
    exit('<h2>数据库查询失败</h2>');
}

$row = mysqli_fetch_assoc($query);
echo $row['avatar'];