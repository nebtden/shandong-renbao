<?php
use yii\helpers\Url;
$this->title='bootstrap-table 操作动态数据';
?>
<div class="tableTile">后台表格修改后台表格修改后台表格修改后台表格修改后台表格修改后台表格修改</div>
<div id="toolbar" class="btn-group">
    <form class="form-inline">
    <button type="button" class="btn btn-default">
        <i class="glyphicon glyphicon-plus"></i>
    </button>
    <button type="button" class="btn btn-default">
        <i class="glyphicon glyphicon-heart"></i>
    </button>
    <button type="button" class="btn btn-default" id="remove">
        <i class="glyphicon glyphicon-trash" ></i>
    </button>
        <div class="form-group"><input type="text" id="uname"   class="form-control" placeholder="用户名"></div>
        <div class="form-group"> <select id="sec"   placeholder="用户ID"  class="form-control" >
             <option value="">请选择</option>
            <option value="1">1</option>
            <option value="2">2</option>
        </select></div>
        <button type="button" class="btn btn-info" id="sousuo"><span class="glyphicon glyphicon-search"></span> 搜索</button>

</form>


</div>
<table class="table table-bordered">
    <thead>
    <tr>
        <th data-field="state" data-checkbox="true"></th>
        <th data-formatter="runningFormatter" data-sortable="true">序号</th>
        <th data-field="id" data-align="center" data-sortable="true">Item ID</th>
        <th data-field="username" data-align="center" data-sortable="true">用户名</th>
        <th data-field="updated_at" data-align="center" data-sortable="true">创建时间</th>
        <th data-field="action" data-align="center" data-formatter="actionFormatter" data-events="actionEvents">Action</th>
    </tr>
    </thead>
</table>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">编辑</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">用户名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="username" placeholder="用户名">
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">邮箱地址</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="email" placeholder="邮箱地址">
                        </div>
                         <input type="hidden" id="uid" name="uid">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="saveupt" data-dismiss="modal">保存</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var type='id',stext,order='desc',ids='',listurl='<?php echo Url::to(['admin/test']); ?>',durl="<?php echo Url::to(['admin/del']); ?>",upturl='<?php echo Url::to(['admin/uptdata']);?>';
    $(function(){
        $('.table').bootstrapTable({
            url:listurl ,
            method: 'get', //这里要设置为get，不知道为什么 设置post获取不了
            height: $(window).height()-130,//'auto',
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
            pageSize: 1,
            pageNumber:1,
            pageList:[1,10,20,50,100],
            toolbar:"#toolbar",
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

        return {
            pageSize: params.limit,
            pageNumber: params.pageNumber,
            searchText:stext,//params.searchText,
            limit:params.limit,
            type:type,
            order:order,
            ids:ids
        };
    }

    function responseHandler(res) {
        //  var resultStr = $.parseJSON(res);
        // alert(resultStr);
         // console.log(res);
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

    $('#sousuo').click(function(){
        var opt1=$('#uname').val();
        var opt2=$('#sec').val();
        $('.table').bootstrapTable('refresh',{query:{uname:opt1,id:opt2}},function(row){
            console.log(row);
        });
    });

    $('#saveupt').click(function(){
        var op1=$('#username').val();
        var op2=$('#email').val();
        var uid=$('#uid').val();
        $.post(upturl,{op1:op1,op2:op2,uid:uid},function(s){
            $('.table').bootstrapTable('refresh');
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
            '<a class="like" href="javascript:void(0)" title="Like"><i class="glyphicon glyphicon-heart"></i></a>',
            '<a class="edit ml10" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-edit"></i></a>',
            '<a class="remove ml10" href="javascript:void(0)" title="Remove"><i class="glyphicon glyphicon-remove"></i></a>'
        ].join('');

    }

    window.actionEvents = {
        'click .like': function (e, value, row, index) {
           // alert('You click like icon, row: ' + JSON.stringify(row));
            console.log(value, row, index);
        },
        'click .edit': function (e, value, row, index) {
             $('#username').val(row.username);
             $('#email').val(row.email);
             $('#uid').val(row.id);
            //alert('You click edit icon, row: ' + JSON.stringify(row));
          //  console.log(value, row, index);
        },
        'click .remove': function (e, value, row, index) {
            $('.table').bootstrapTable('remove', {
                field: 'id',
                values:[row.id]
            });
            $.get(durl,{params:[row.id]},function(s){
                alert('删除成功');
            });


            //alert('You click remove icon, row: ' + JSON.stringify(row));
            console.log(value, row, index);
        }
    };
</script>

