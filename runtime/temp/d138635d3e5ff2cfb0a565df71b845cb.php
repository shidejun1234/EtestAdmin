<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"D:\sdj\phpStudy\WWW\ETest3/apps/index\view\question\add.html";i:1556173459;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>添加题目</title>
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/style/question.css">
</head>
<body>

<form class="layui-form" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">题目</label>
        <div class="layui-input-block">
            <textarea name="title" lay-verify="required" placeholder="请输入题目" class="layui-textarea"></textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label">科目</label>
            <div class="layui-input-inline">
                <select name="subject" lay-verify="required" lay-search="">
                    <option value="">直接选择或搜索选择</option>
                    <?php if(is_array($subject) || $subject instanceof \think\Collection): $i = 0; $__LIST__ = $subject;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">题型</label>
        <div class="layui-input-block">
            <input type="radio" name="type" value="选择题" title="选择题" checked="">
            <input type="radio" name="type" value="判断题" title="判断题">
        </div>
    </div>
    <table class="layui-table" lay-skin="line">
        <colgroup>
            <col width="20%">
            <col width="70%">
            <col width="10%">
            <col>
        </colgroup>
        <thead>
        <tr>
            <th>答案</th>
            <th>内容</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <tr class="optionA">
            <td><input type="radio" name="answer" value="A" title="选项A"></td>
            <td>
                <textarea placeholder="请输入内容" lay-verify="required" name="options[op][A]" class="layui-textarea"></textarea>
            </td>
            <td>
                <input type="button" class="layui-btn layui-btn-danger layui-btn-xs" value="删除"
                       onclick="delOption('optionA')">
            </td>
        </tr>
        <tr class="optionB">
            <td><input type="radio" name="answer" value="B" title="选项B"></td>
            <td>
                <textarea placeholder="请输入内容" lay-verify="required" name="options[op][B]" class="layui-textarea"></textarea>
            </td>
            <td>
                <input type="button" class="layui-btn layui-btn-danger layui-btn-xs" value="删除"
                       onclick="delOption('optionB')">
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <div class="layui-form-item" style="margin-left: 30%;">
                    <div class="layui-input-block">
                        <input type="button" class="layui-btn layui-btn-normal" id="add_option" value="添加选项">
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
        </div>
    </div>
</form>

<script src="__PUBLIC__/js/jquery.js"></script>
<script src="__PUBLIC__/layui/layui.js"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    var oIndex = 66;

    function delOption(e) {
        if (e === 'optionA' || e === 'optionB') {
            layer.msg('至少要有两个选项', {icon: 5, anim: 6});
            return;
        }
        var option = e.substring(e.length - 1, e.length).charCodeAt();
        if (option < oIndex) {
            layer.msg('请从最后一个选项开始删除', {icon: 5, anim: 6});
            return;
        }
        $('.' + e).remove();
        oIndex--;
    }

    layui.use('form', function () {
        var form = layui.form
            , layer = layui.layer
        //添加选项
        $('#add_option').on('click', function () {
            oIndex++;
            $('.option' + String.fromCharCode(oIndex - 1)).after('\n' +
                '        <tr class="option' + String.fromCharCode(oIndex) + '">\n' +
                '            <td><input type="radio" name="answer" value="' + String.fromCharCode(oIndex) + '" title="选项' + String.fromCharCode(oIndex) + '"></td>\n' +
                '            <td>\n' +
                '                <textarea placeholder="请输入内容" lay-verify="required" name="options[op][' + String.fromCharCode(oIndex) + ']" class="layui-textarea"></textarea>\n' +
                '            </td>\n' +
                '            <td>\n' +
                '                <input type="button" class="layui-btn layui-btn-danger layui-btn-xs" value="删除" onclick="delOption(\'option' + String.fromCharCode(oIndex) + '\')">\n' +
                '            </td>\n' +
                '        </tr>');
            form.render();
        });

        //监听提交
        form.on('submit(demo1)', function (data) {
            if (!data.field.answer) {
                layer.msg('你还没选择正确答案', {icon: 5, anim: 6});
                return false;
            }
            console.log(data.field)
            $.ajax({
                url: '<?php echo url("Question/add"); ?>',
                type: 'POST',
                data: {
                    data: data.field
                },
                success: function (res) {
                    layer.alert(res, {
                        title: '消息'
                    })
                }
            })
            setTimeout(function () {
                var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                parent.layer.close(index); //再执行关闭
            }, 1000)
            return false;
        });

    });
</script>
</body>
</html>