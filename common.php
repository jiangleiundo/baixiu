<?php
/*
 * common function
 * */
require_once 'config.php';
session_start();

/**
 * 获取当前登录用户信息，如果没有获取到则自动跳转到登录页面
 * @return mixed [返回当前用户信息]
 */
function bx_is_login () {
    if (empty($_SESSION['cur_user'])) {
        // 没有当前登录用户信息，意味着没有登录
        header('Location: /admin/login.php');
        exit();
    }
    return $_SESSION['cur_user'];
}

/**
 * 链接数据库，返回查询结果
 * @param $sql [查询语句]
 * @return array|bool [返回查询结果和数据库联结]
 */
function bx_query($sql){
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$conn) {
        exit('<h2>数据库链接失败</h2>');
    }

    $query = mysqli_query($conn, $sql);
    if(!$query) {
        return false;
    }

    $arr = array();
    $arr[] = $query;
    $arr[] = $conn;

    return $arr;
}

/**
 * 通过数据库查询获取数据(多条)
 * @param $sql [查询语句]
 * @return array|bool [查询结果/索引数组]
 */
function bx_fetch_all($sql){

    list($query, $conn) = bx_query($sql);

    $res = array();
    while ($row = mysqli_fetch_assoc($query)){
        $res[] = $row;
    }

    //断开数据库并释放查询结果集(默认也会自动断开)
    mysqli_free_result($query);
    mysqli_close($conn);

    return $res;
}

/**
 * 通过数据库查询获取数据(1条)
 * @param $sql [查询语句]
 * @return mixed|null [查询结果/关联数组中的第一个]
 */
function bx_fetch_one($sql){
    $res = bx_fetch_all($sql);
    return isset($res[0])? $res[0]: null;
}

/**
 * 执行增删改语句
 * @param $sql [查询语句]
 * @return int [受影响行数]
 */
function bx_execute($sql){
    list($query, $conn) = bx_query($sql);

    //增删改查询后获取受影响行
    $affected_row = mysqli_affected_rows($conn);

    //断开数据库链接
    mysqli_close($conn);
    
    return $affected_row;
}