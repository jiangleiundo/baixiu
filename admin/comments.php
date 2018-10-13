<?php
require_once('../common.php');
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
    <?php include('inc/navbar.php'); ?>
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
                <a class="btn btn-info btn-sm btn-batch-status" data-status="approved">批量批准</a>
                <a class="btn btn-warning btn-sm btn-batch-status" data-status="rejected">批量拒绝</a>
                <button class="btn btn-danger btn-sm" id="delAllBtn">批量删除</button>
            </div>
            <ul class="pagination pagination-sm pull-right"></ul>
        </div>
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th class="text-center" width="40"><input id="allSel" type="checkbox">全选</th>
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
<?php $cur_page = 'comments'; ?>
<?php include('inc/sidebar.php'); ?>

<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
<script src="/static/assets/vendors/jsrender/jsrender.min.js"></script><!--jsrender 模板引擎-->
<script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script><!--pagination 模板-->
<script id="comment_temp" type="text/x-jsrender">
{{for comments}}
    <tr {{if status == 'held'}} class="warning" {{else status == 'rejected'}} class="danger" {{/if}} data-id="{{:id}}">
        <td class="text-center"><input type="checkbox" data-id="{{:id}}"></td>
        <td>{{:author}}</td>
        <td width='360'>{{:content}}</td>
        <td>{{:posts_title}}</td>
        <td>{{:created}}</td>
        <td>{{if status == 'held'}}待审{{else status == 'rejected'}}拒绝{{else status == 'approved'}}同意{{else status == 'trashed'}}废除 {{/if}}</td>
        <td class="text-right" width="150">
            {{if status == 'held'}}
            <a class="btn btn-info btn-xs btn-approved" data-status="approved">批准</a>
            <a class="btn btn-warning btn-xs btn-rejected" data-status="rejected">驳回</a>
            {{/if}}
            <a class="btn btn-danger btn-xs btn-del">删除</a>
        </td>
    </tr>
{{/for}}
</script>
<script src="/static/assets/js/common.js"></script>
<script>
    $(document).ajaxStart(function () {
        NProgress.start();
    }).ajaxStop(function () {
        NProgress.done();
    });

    //当前页面页数
    let curPage = 1;
    let $t_comments = $('#t_comments');

    /**
     * 加载分页
     * @param page [跳转页]
     */
    function loadPage(page) {
        $.get('/admin/api/comments.php', {page: page}, function (res) {
            if (page > res.totalPage) {
                loadPage(res.totalPage);
                return;
            }

            //解决totalPages不更新的问题(先移除然后重新加入DOM树中)在使用twbsPagination之前做。
            $('.pagination').remove();
            $(".page-action").append('<ul class="pagination pagination-sm pull-right"></ul>');
            //分页
            $('.pagination').twbsPagination({
                totalPages: res.totalPage,
                visiblePages: 5,
                initiateStartPageClick: false,
                startPage: page,
                first: '首页',
                last: '尾页',
                prev: '前一页',
                next: '后一页',
                onPageClick: function (e, page) {
                    //初始化时就会触发-通过设置initiateStartPageClick: false,解决初始化时不触发
                    loadPage(page);
                    curPage = page;
                }
            });

            //jsrender模板引擎
            let html = $("#comment_temp").render({comments: res.comments});
            $t_comments.html(html);

            $('.btn-batch').fadeOut();
            batch();
        });
    }

    loadPage(1);

    //删除单个数据
    $t_comments.on("click", '.btn-del', function () {
        let $tr = $(this).parents('tr');
        let id = $tr.attr('data-id');
        $.getJSON('/admin/api/comments-del.php', {id: id}, function (res) {
            if (res) { //返回true刷新页面
                loadPage(curPage);
            }
        });
    });

    //审核通过
    comments_held('.btn-approved');
    //审核拒绝
    comments_held('.btn-rejected');

    function comments_held(btn) {
        $t_comments.on('click', btn, function () {
            let $tr = $(this).parents('tr');
            let id = $tr.attr('data-id');
            let status = $(this).attr('data-status');

            $.getJSON('/admin/api/comments-status.php', {id: id, status: status}, function (res) {
                if (res.success) {
                    loadPage(curPage);
                }
            });
        });
    }

    //批量操作
    function batch(){
        let selCount = [];
        let $tbodyInput = $('#t_comments input');
        let $batchBtn = $('.btn-batch');
        let allSel = $('#allSel');
        let $delAllBtn = $('#delAllBtn');
        let $batchStatus = $('.btn-batch-status');
        allSel.on("change", function () {
            let flag = $(this).prop('checked');
            $tbodyInput.prop('checked', flag).trigger('change');
        });

        $tbodyInput.on('change', function () {
            let id = $(this).attr('data-id');

            if ($(this).prop('checked')) {
                selCount.indexOf(id) === 0 || selCount.push(id); //如果数组中有id就不添加了
            } else {
                selCount.splice(selCount.indexOf(id), 1);
            }
            selCount.length > 0 ? $batchBtn.fadeIn() : $batchBtn.fadeOut();
            allSel.prop('checked', selCount.length === $tbodyInput.length);
            //批量删除
            $delAllBtn.off('click').on('click', function (){
                $.getJSON('/admin/api/comments-del.php', {id: selCount.join(',')}, function (res) {
                    if (res) { //返回true刷新页面
                        loadPage(curPage);
                    }
                });
            });
            //批量修改状态
            $batchStatus.off('click').on('click', function (){
                let status = $(this).attr('data-status');
                $.getJSON('/admin/api/comments-status.php', {id: selCount.join(','), status: status}, function (res) {
                    if (res.success) {
                        loadPage(curPage);
                    }
                });
            })
        });
    }
</script>
<script>NProgress.done()</script>
</body>
</html>