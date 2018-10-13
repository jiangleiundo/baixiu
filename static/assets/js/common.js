var comm = {
    isEmpty: function (str) {
        var res = false;

        try {
            if (!str || str === "''" || str === "null" || str === "{}"
                || str === "[]" || str === "'[]'" || str === "<null>" || str === "0") {
                res = true;
            }
        }
        catch (e) {
            res = false;
        }

        return res;
    },
    tip: function (msg, tipBox){
        $(tipBox).fadeIn().find('span').html(msg);
        setTimeout(function (){
            $(tipBox).fadeOut();
        }, 9000)
    }
};

function batchAll(bodyInput, delAllBtn, allSel){
    let $tbodyInput = bodyInput;
    let $delAllBtn = delAllBtn;
    let $allSel = allSel;
    let selCount = []; //批量删除id的数组

    //全选
    $allSel.on("change", function () {
        let flag = $(this).prop('checked');
        $tbodyInput.prop('checked', flag).trigger('change'); //jquery批量操作
    });

    //CheckBox被选中时批量删除按钮出现
    $tbodyInput.on('change', function () {
        let id = $(this).data('id');

        if ($(this).prop('checked')) {
            selCount.indexOf(id) === 0 || selCount.push(id); //如果数组中有id就不添加了
        } else {
            selCount.splice(selCount.indexOf(id), 1);
        }

        selCount.length > 0 ? $delAllBtn.fadeIn() : $delAllBtn.fadeOut();

        $allSel.prop('checked', selCount.length === $tbodyInput.length);
        $delAllBtn.prop('search', '?id=' + selCount);
    });
}