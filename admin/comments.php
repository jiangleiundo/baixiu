<?php
require_once ('../common.php');
bx_is_login();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>Comments &laquo; Admin</title>
    <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
    <link rel="stylesheet" href="/static/assets/css/admin.css">
    <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
<script>NProgress.start()</script>

<div class="main">
    <?php include ('inc/navbar.php'); ?>
    <div class="container-fluid">
        <div class="page-title">
            <h1>所有评论</h1>
        </div>
        <!-- 有错误信息时展示 -->
        <!-- <div class="alert alert-danger">
          <strong>错误！</strong>发生XXX错误
        </div> -->
        <div class="page-action">
            <!-- show when multiple checked -->
            <div class="btn-batch" style="display: none">
                <button class="btn btn-info btn-sm">批量批准</button>
                <button class="btn btn-warning btn-sm">批量拒绝</button>
                <button class="btn btn-danger btn-sm">批量删除</button>
            </div>
            <ul class="pagination pagination-sm pull-right"></ul>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>作者</th>
                <th>评论</th>
                <th>评论在</th>
                <th>提交于</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
            </tr>
            </thead>
            <tbody id="t_comments"></tbody>
        </table>
    </div>
</div>
<?php $cur_page='comments'; ?>
<?php include('inc/sidebar.php'); ?>

<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script src="/static/assets/vendors/jsrender/jsrender.min.js"></script><!--jsrender 模板引擎-->
<script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script><!--pagination 模板-->
<script id="comment_temp" type="text/x-jsrender">
{{for comments}}
    <tr {{if status == 'held'}} class="warning" {{else status == 'rejected'}} class="danger" {{/if}}>
        <td class="text-center"><input type="checkbox"></td>
        <td>{{:author}}</td>
        <td>{{:content}}</td>
        <td>{{:posts_title}}</td>
        <td>{{:created}}</td>
        <td>{{if status == 'held'}}待审{{else status == 'rejected'}}拒绝{{else status == 'approved'}}同意 {{/if}}</td>
        <td class="text-right" width="150">
            {{if status == 'held'}}
            <a href="" class="btn btn-info btn-xs">批准</a>
            <a href="" class="btn btn-warning btn-xs">驳回</a>
            {{/if}}
            <a href="" class="btn btn-danger btn-xs">删除</a>
        </td>
    </tr>
{{/for}}
</script>
<script>
    $(document).ajaxStart(function (){
        NProgress.start();
    }).ajaxStop(function (){
        NProgress.done();
    });
    //加载分页
    function loadPage(page){
        $.get('/admin/api/comments.php',{page: page}, function (res){

            //分页
            $(".pagination").twbsPagination({
                totalPages: res.totalPage,
                visiblePages: 5,
                initiateStartPageClick: false,
                first: '首页',
                last: '尾页',
                prev: '前一页',
                next: '后一页',
                onPageClick: function (e, page){
                    //初始化时就会触发-通过设置initiateStartPageClick: false,解决初始化时不触发
                    loadPage(page);
                }
            });

            //jsrender模板引擎
            let html = $("#comment_temp").render({ comments: res.comments});
            $('#t_comments').html(html);
        });
    }

    loadPage(1);
</script>
<script>NProgress.done()</script>
</body>
</html>
