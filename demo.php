<?php header('Content-type: text/html; charset=utf-8');
$a = 0;

if (empty($a)) {
    echo '1231321';
}


printf('my name is %s, age %d', 'tom, 18');
$con = sprintf('my name is %s, age %d', 'jerry', '23');
echo $con;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>demo</title>
</head>
<body>
<a id="btn_delete" class="btn btn-danger btn-sm" href="/admin/category-delete.php">批量删除</a>
<script src="static/assets/vendors/jquery/jquery.js"></script>







</body>
</html>