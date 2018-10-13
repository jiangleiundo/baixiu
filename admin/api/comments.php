<?php header('Content-type: text/html; charset=utf-8');
//载入封装函数
require_once '../../common.php';

$size = 20;
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
$offset = ($page - 1) * $size;

$sql = sprintf('select 
comments.*,
posts.title as posts_title
from comments 
inner join posts on comments.post_id = posts.id
order by comments.created desc
limit %d, %d', $offset, $size);

$totalCount = bx_fetch_one('select count(1) as count
from comments
inner join posts on comments.post_id = posts.id;');

$totalPage = (int)ceil($totalCount['count'] / $size);

$comments = bx_fetch_all($sql);

//返回一个关联数组
$json = json_encode(array(
    'totalPage' => $totalPage,
    'comments' => $comments
));

header('Content-type: text/json');
echo $json;