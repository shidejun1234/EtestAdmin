<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:56:"D:\sdj\phpStudy\WWW\ETest3/apps/index\view\test\lst.html";i:1556154647;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>题目列表</title>
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/style/question.css">
    <style>
        .layui-table-cell{
            text-align: center;
        }
    </style>
</head>
<body>
<!--<div class="layui-btn-group demoTable">-->
<!--    <button class="layui-btn layui-btn-danger layui-btn-sm" lay-event="getCheckData">删除选中</button>-->
<!--</div>-->
<div class="layui-btn-container questionTable">
    <button class="layui-btn layui-btn-danger layui-btn-sm" data-type="delCheck">删除选中</button>
    <button class="layui-btn layui-btn-sm" data-type="addSubject">添加题目</button>
</div>
<table id="question-table" class="question-table" lay-filter="test"></table>

<script type="text/html" id="testBar">
    <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>

<script src="__PUBLIC__/js/jquery.js"></script>
<script src="__PUBLIC__/layui/layui.js"></script>
<script>
    layui.use('table', function () {
        var table = layui.table;

        //第一个实例
        table.render({
            elem: '#question-table'
            , height: 472
            , url: '<?php echo url("Question/getQuestionList"); ?>' //数据接口
            , page: true //开启分页
            , limit: 10
            , cols: [[ //表头
                {type: 'checkbox'}
                , {field: 'id', title: 'ID', sort: true, width: 80}
                , {field: 'title', title: '题目', sort: true}
                , {field: 'type', title: '类型', sort: true}
                , {field: 'name', title: '科目', sort: true}
                , {field: 'options', title: '选项数', templet:function (d) {
                        return JSON.parse(d.options).length;
                    }}
                , {field: 'create_time', title: '创建时间', sort: true}
                , {fixed: 'right', title: '操作', toolbar: '#testBar', width: 120}
            ]]
            , parseData: function (res) { //res 即为原始返回的数据
                var curr = 1;
                if (this.page.curr) {
                    curr = this.page.curr;
                }
                var data = res.data.slice(this.limit * (curr - 1), this.limit * curr);
                return {
                    "code": res.code, //解析接口状态
                    "msg": res.message, //解析提示文本
                    "count": res.count, //解析数据长度
                    "data": data //解析数据列表
                };
            }
        });

        //监听排序事件
        table.on('sort(test)', function (obj) {
            table.reload('question-table', {
                initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
                , where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                    field: obj.field //排序字段
                    , order: obj.type //排序方式
                }
            });
        });

        var active = {
            delCheck: function () { //获取选中数据
                var checkStatus = table.checkStatus('question-table')
                    , data = checkStatus.data, ids = '';
                if (checkStatus.data.length == 0) {
                    layer.msg('请先选择要删除的数据行！', {icon: 2});
                    return;
                }
                layer.confirm('真的删除行么', function (index) {
                    layer.msg('删除中...', {icon: 16, shade: 0.3, time: 5000});
                    data.forEach(function (item) {
                        ids += item.id + ',';
                    })
                    ids = ids.substring(0, ids.length - 1);
                    $.ajax({
                        url: '<?php echo url("Test/del"); ?>',
                        type: 'POST',
                        data: {
                            ids: ids
                        },
                        success: function (res) {
                            if (JSON.parse(res).code === 0) {
                                layer.msg('删除成功！', {icon: 1, time: 2000, shade: 0.2});
                                setTimeout(function () {
                                    location.reload(true);
                                },300)
                            } else {
                                layer.msg('删除失败！', {icon: 2, time: 3000, shade: 0.2});
                            }
                        }
                    });
                    layer.close(index);
                });
            },
            addSubject: function () {
                layer.open({
                    type: 2,
                    title: '添加题目',
                    shadeClose: true,
                    shade: 0.3,
                    area: ['893px', '600px'],
                    content: '<?php echo url("Test/add"); ?>',
                    end: function () {
                        window.location.reload();
                    }
                });
            }
        };

        $('.questionTable .layui-btn').on('click', function () {
            var type = layui.$(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

        //监听行工具事件
        table.on('tool(test)', function (obj) {
            var data = obj.data;
            //console.log(obj)
            if (obj.event === 'del') {
                layer.confirm('真的删除行么', function (index) {
                    $.ajax({
                        url: '<?php echo url("Test/del"); ?>',
                        type: 'POST',
                        data: {
                            ids: data.id
                        },
                        success: function (res) {
                            if (JSON.parse(res).code === 0) {
                                layer.msg('删除成功！', {icon: 1, time: 2000, shade: 0.2});
                                setTimeout(function () {
                                    location.reload(true);
                                },300)
                            } else {
                                layer.msg('删除失败！', {icon: 2, time: 3000, shade: 0.2});
                            }
                        }
                    });
                    layer.close(index);
                });
            } else if (obj.event === 'edit') {
                console.log(data.id)
                layer.open({
                    type: 2,
                    title: '添加题目',
                    shadeClose: true,
                    shade: 0.3,
                    area: ['893px', '600px'],
                    content: '<?php echo url("Test/edit"); ?>?id='+data.id,
                    end: function () {
                        window.location.reload();
                    }
                });
            }
        });

    });
</script>
</body>
</html>