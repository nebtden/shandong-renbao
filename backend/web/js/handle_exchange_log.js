/**
 * Created by Administrator on 2017/11/2 0002.
 */
$(function(){
    if(typeof(height) == "undefined" || height<=0)height=$(window).height()-20;
    $('.table').bootstrapTable({
        url:listurl ,
        method: 'get', //这里要设置为get，不知道为什么 设置post获取不了
        height: $(window).height()-120,//'auto',
        striped: true,
        showRefresh:true,
        showColumns:true,
        showToggle:true,
        clickToSelect:true,
        dataType: "json",
        pagination: true,
        queryParamsType: "limit",
        singleSelect: false,
        contentType: "application/x-www-form-urlencoded",
        pageSize:10,
        pageNumber:1,
        pageList:[5,10,20,50,100],
        toolbar:"#toolbar",
        showExport:"true",
        //exportDataType:"all",
        // search: true, //不显示 搜索框
        showColumns: true, //不显示下拉框（选择显示的列）
        sidePagination: "server" , //服务端请求
        queryParams: queryParams,
        //minimunCountColumns: 2,
        responseHandler: responseHandler
        /*  columns: [{
         field: 'id',
         title: '标志id'
         }, {
         field: 'name',
         title: '名字'
         },
         {
         field:'price',
         title:'价格'
         },
         {
         field:'price',
         title:'价格'
         }
         ]
         */

    });

    $('#remove').click(function () {
        var ids = getIdSelections();
        if(ids.length<=0)
        {
            alert('没有选择勾选项');
            return;
        }
        $.get(durl,{params:ids},function(s){
            alert('删除成功');
        });
        $('.table').bootstrapTable('remove', {
            field: 'id',
            values: ids
        });

        // $('#remove').prop('disabled', true);
    });

    $('.table').on('sort.bs.table', function (e, name, args) {
        type=name,order=args;
    }).on('search.bs.table', function (args) {
        stext=name;
    }).on('check.bs.table', function (row,e) {
        var ids = getIdSelections();
        console.log(ids);
    }).on('check-all.bs.table', function (e) {
        var ids = getIdSelections();
        console.log(ids);

    }).on('check-some.bs.table', function (e) {
        var ids = getIdSelections();
        console.log(ids);
    }).on('uncheck.bs.table', function (e,name) {
        var ids = getIdSelections();
        console.log(ids);
    }).on('uncheck-all.bs.table', function (e) {
        var ids = getIdSelections();
        console.log(ids);
    }).on('onUncheckSome', function (e) {
        var ids = getIdSelections();
        console.log(ids);
    });//

});


function queryParams(params) {
    var opt1=$('#company').val();
    var opt2=$('#nickname').val();
    var opt3=$('#personname').val();
    var opt4=$('#batch_no').val();
    var opt5=$('#shop_name').val();
    var opt6=$('#status').val();
    return {
        pageSize: params.limit,
        pageNumber: params.pageNumber,
        searchText:stext,//params.searchText,
        limit:params.limit,
        order:order,
        company:opt1,
        nickname:opt2,
        personname:opt3,
        batch_no:opt4,
        shop_name:opt5,
        status:opt6
    };
}

function responseHandler(res) {  //console.log(res);
    //  var resultStr = $.parseJSON(res);
    // alert(resultStr);
    console.log(res);
    if (res.IsOk) {
        // var result = b64.decode(res.ResultValue);
        //var resultStr = $.parseJSON(res);
        return {
            "rows": res.rows,
            "total": res.total
        };

    } else {
        return {
            "rows": [],
            "total": 0
        };
    }

}
$('#saveupt').click(function(){
    var op1=$('#name').val();
    var op2=$('#sort').val();
    var id=$('#id').val();
    $.post(upturl,{op1:op1,op2:op2,id:id},function(s){ console.log(s);
        $('.table').bootstrapTable('refresh');
    });

});

$('#sousuo').click(function(){
    var opt1=$('#company').val();
    var opt2=$('#nickname').val();
    var opt3=$('#personname').val();
    var opt4=$('#batch_no').val();
    var opt5=$('#shop_name').val();
    var opt6=$('#status').val();


    $('.table').bootstrapTable('refresh',{query:{
        company:opt1,
        nickname:opt2,
        personname:opt3,
        batch_no:opt4,
        shop_name:opt5,
        status:opt6


    }},function(row){
        console.log(row);
    });
});

function getIdSelections() {
    return $.map($('.table').bootstrapTable('getSelections'), function (row) {
        return row.id
    });
}
function runningFormatter(value, row, index) {
    return index + 1;
}
function actionFormatter(value, row, index) {
    return [
        '<a class="edit ml10" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-edit"></i>审核</a>&nbsp;&nbsp;',
        //   '<a class="next ml10" href="javascript:void(0)" title="next"><i class="glyphicon glyphicon-eye-open"></i>查看下一级</a>'
    ].join('');

}

window.actionEvents = {
    'click .like': function (e, value, row, index) {
        // alert('You click like icon, row: ' + JSON.stringify(row));
        // console.log(value, row, index);
        if(more) {
            window.location.href = more + 'id=' + row.id;
        }
    },
    'click .edit': function (e, value, row, index) {
        $('#myModalLabel').html('编辑');
        if(imgcon) {
            window.location.href = eurl + '?id=' + row.id;
        }

        $('#username').val(row.username);
        $('#email').val(row.email);
        $('#id').val(row.id);
        $('#name').val(row.name);
        $('#sort').val(row.sort);
        //alert('You click edit icon, row: ' + JSON.stringify(row));
        //  console.log(value, row, index);
    },

    'click .next': function (e, value, row, index) {
        var pid=row.id; //alert(pid);
        $('.table').bootstrapTable('refresh',{query:{card:pid}},function(row){
            console.log(row);
        });

    }
};


