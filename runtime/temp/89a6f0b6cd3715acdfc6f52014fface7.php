<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"D:\sdj\phpStudy\WWW\ETest3/apps/index\view\subject\lst.html";i:1556502695;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>科目列表</title>
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/style/subject.css">
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
<div class="layui-btn-container subjectTable">
    <button class="layui-btn layui-btn-danger layui-btn-sm" data-type="delCheck">删除选中</button>
    <button class="layui-btn layui-btn-sm" data-type="addSubject">添加科目</button>
</div>
<table id="subject-table" class="subject-table" lay-filter="test"></table>

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
            elem: '#subject-table'
            , height: 472
            , url: '<?php echo url("Subject/getSubjectList"); ?>' //数据接口
            , page: true //开启分页
            , limit: 10
            , cols: [[ //表头
                {type: 'checkbox'}
                , {field: 'id', title: 'ID', sort: true, width: 80}
                , {field: 'name', title: '科目', sort: true}
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
            table.reload('subject-table', {
                initSort: obj //记录初始排序，如果不设的话，将无法标记表头的排序状态。
                , where: { //请求参数（注意：这里面的参数可任意定义，并非下面固定的格式）
                    field: obj.field //排序字段
                    , order: obj.type //排序方式
                }
            });
        });

        var active = {
            delCheck: function () { //获取选中数据
                var checkStatus = table.checkStatus('subject-table')
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
                        url: '<?php echo url("Subject/del"); ?>',
                        type: 'POST',
                        data: {
                            ids: ids
                        },
                        success: function (res) {
                            if (JSON.parse(res).code === 0) {
                                layer.msg('删除成功！', {icon: 1, time: 2000, shade: 0.2});
                                table.reload('subject-table');
                                // location.reload(true);
                            } else {
                                layer.msg('删除失败！该科目下存在试题', {icon: 2, time: 3000, shade: 0.2});
                            }
                        }
                    });
                    layer.close(index);
                });
            },
            addSubject: function () {
                layer.prompt({
                    title: '添加科目',
                    formType: 0
                }, function (value, index) {
                    $.ajax({
                        url: '<?php echo url("Subject/add"); ?>',
                        type: 'POST',
                        data: {
                            name: value
                        },
                        success: function (res) {
                            if (JSON.parse(res).code === 0) {
                                layer.msg('添加成功！', {icon: 1, time: 2000, shade: 0.2});
                                table.reload('subject-table');
                                // location.reload(true);
                            } else {
                                layer.msg('添加失败！', {icon: 2, time: 3000, shade: 0.2});
                            }
                        }
                    });
                    layer.close(index);
                })
            }
        };

        $('.subjectTable .layui-btn').on('click', function () {
            var type = layui.$(this).data('type');
            active[type] ? active[type].call(this) : '';
        });

        //监听行工具事件
        table.on('tool(test)', function (obj) {
            var data = obj.data;
            //console.log(obj)
            if (obj.event === 'del') {
                layer.confirm('真的删除行么', function (index) {
                    layer.msg('删除中...', {icon: 16, shade: 0.3, time: 5000});
                    $.ajax({
                        url: '<?php echo url("Subject/del"); ?>',
                        type: 'POST',
                        data: {
                            ids: data.id
                        },
                        success: function (res) {
                            console.log(res);
                            if (JSON.parse(res).code === 0) {
                                layer.msg('删除成功！', {icon: 1, time: 2000, shade: 0.2});
                                table.reload('subject-table');
                                // location.reload(true);
                            } else {
                                layer.msg('删除失败！该科目下存在试题', {icon: 2, time: 3000, shade: 0.2});
                            }
                        }
                    });
                    layer.close(index);
                });
            } else if (obj.event === 'edit') {
                layer.prompt({
                    title: '修改科目',
                    formType: 0
                    , value: data.name
                }, function (value, index) {
                    $.ajax({
                        url: '<?php echo url("Subject/edit"); ?>',
                        type: 'POST',
                        data: {
                            id: data.id,
                            name: value
                        },
                        success: function (res) {
                            if (JSON.parse(res).code === 0) {
                                layer.msg('修改成功！', {icon: 1, time: 2000, shade: 0.2});
                                obj.update({
                                    name: value
                                });
                            } else {
                                layer.msg('删除失败！', {icon: 2, time: 3000, shade: 0.2});
                            }
                        }
                    })
                    layer.close(index);
                });
            }
        });

    });
</script>
</body>
</html>