/**
 * Created by Administrator on 2018/4/18 0018.
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
    var opt1 = $("#coupon_type").val();
    var opt2 = $("#mobile").val();
    var opt3 = $("#coupon_sn").val();
    var opt4 = $("#coupon_status").val();
    var opt5 = $("#coupon_name").val();
    var opt6 = $("#amount").val();
    var opt7 = $("#batch_no").val();
    var opt8 = $("#coupon_id").val();
    var opt9 =  $("#companyid").attr('data-id');
    var opt10 = $("#start_time").val();
    var opt11 = $("#end_time").val();
    var opt12 = $("#s_time").val();
    var opt13 = $("#e_time").val();
    var opt14 = $("#acs_time").val();
    var opt15 = $("#ace_time").val();
    return {
        pageSize: params.limit,
        pageNumber: params.pageNumber,
        searchText:stext,//params.searchText,
        limit:params.limit,
        coupon_type:opt1,
        mobile:opt2,
        coupon_sn:opt3,
        coupon_status:opt4,
        coupon_name:opt5,
        amount:opt6,
        batch_no:opt7,
        coupon_id:opt8,
        companyid:opt9,
        start_time:opt10,
        end_time:opt11,
        s_time:opt12,
        e_time:opt13,
        acs_time:opt14,
        ace_time:opt15
    };
}

function responseHandler(res) {  //console.log(res);
    //  var resultStr = $.parseJSON(res);
    // alert(resultStr);
    //console.log(res);
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
    var opt1 = $("#coupon_type").val();
    var opt2 = $("#mobile").val();
    var opt3 = $("#coupon_sn").val();
    var opt4 = $("#coupon_status").val();
    var opt5 = $("#coupon_name").val();
    var opt6 = $("#amount").val();
    var opt7 = $("#batch_no").val();
    var opt8 = $("#coupon_id").val();
    var opt9 =  $("#companyid").attr('data-id');
    var opt10 = $("#start_time").val();
    var opt11 = $("#end_time").val();
    var opt12 = $("#s_time").val();
    var opt13 = $("#e_time").val();
    var opt14 = $("#acs_time").val();
    var opt15 = $("#ace_time").val();
    $('.table').bootstrapTable('refresh',{query:{
        coupon_type:opt1,
        mobile:opt2,
        coupon_sn:opt3,
        coupon_status:opt4,
        coupon_name:opt5,
        amount:opt6,
        batch_no:opt7,
        coupon_id:opt8,
        companyid:opt9,
        start_time:opt10,
        end_time:opt11,
        s_time:opt12,
        e_time:opt13,
        acs_time:opt14,
        ace_time:opt15
    }},function(row){
        console.log(row);
    });
});

$('#msousuo').click(function(){
    var opt1=$('#keywords').val();
    var opt2=$('#status').val();
    $('.table').bootstrapTable('refresh',{query:{keywords:opt1,status:opt2}},function(row){
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
        '<a class="edit ml10" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-edit"></i>编辑</a>&nbsp;&nbsp;',
    ].join('');

}

window.actionEvents = {
    'click .like': function (e, value, row, index) {
        // alert('You click like icon, row: ' + JSON.stringify(row));
        // console.log(value, row, index);
        if(more) {
            window.location.href = more + '?pid=' + row.id;
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
        $('#card').val(pid);
        $('.table').bootstrapTable('refresh',{query:{pageNumber:1,card:pid}},function(row){

            console.log(row);
        });

    }
};
