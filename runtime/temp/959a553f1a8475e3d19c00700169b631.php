<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:57:"D:\sdj\phpStudy\WWW\ETest3/apps/index\view\login\reg.html";i:1556186218;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>易考试后台管理系统</title>
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    <link rel="stylesheet" href="__PUBLIC__/style/admin.css">
    <style>
        .input-code {
            display: flex;
            flex-direction: row;
        }

        .input-code input {
            width: 200px;
        }
    </style>
</head>
<body class="layui-layout-body">
<div class="layui-layout admin-login">
    <form class="layui-form layui-form-pane" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">
                用户名
            </label>
            <div class="layui-input-block">
                <input type="text" name="user" lay-verify="title" autocomplete="off" placeholder="请输入标题"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                密码
            </label>
            <div class="layui-input-block">
                <input type="password" name="pwd" lay-verify="title" autocomplete="off" placeholder="密码"
                       class="layui-input ">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                重复密码
            </label>
            <div class="layui-input-block">
                <input type="password" name="pwd2" lay-verify="title" autocomplete="off" placeholder="密码"
                       class="layui-input ">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">
                验证码
            </label>
            <div class="layui-input-block input-code">
                <input type="text" name="code" lay-verify="title" autocomplete="off" placeholder="验证码"
                       class="layui-input">
                <img src="<?php echo captcha_src(); ?>" alt="" width="100" class="passcode" style="height:38px;cursor:pointer;"
                     onclick="this.src='<?php echo captcha_src(); ?>?'+Math.random();">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="demo1">立即注册</button>
                <input class="layui-btn" style="width: 98px;" value="前往登录" onclick="login()">
            </div>
        </div>
    </form>
</div>
<script src="__PUBLIC__/js/jquery.js"></script>
<script src="__PUBLIC__/layui/layui.js"></script>
<script>
    layui.use('form', function () {
        var form = layui.form
            , layer = layui.layer

        //监听提交
        form.on('submit(demo1)', function (data) {
            if (!data.field.user) {
                layer.msg('用户名不能为空', {icon: 5, anim: 6});
                return false;
            }
            if (!data.field.pwd) {
                layer.msg('密码不能为空', {icon: 5, anim: 6});
                return false;
            }
            if (!data.field.code) {
                layer.msg('验证码不能为空', {icon: 5, anim: 6});
                return false;
            }
            if (data.field.pwd != data.field.pwd2) {
                layer.msg('两次密码不一致', {icon: 5, anim: 6});
                return false;
            }
            var han = /.*[\u4e00-\u9fa5]+.*$/;
            if (han.test(data.field.pwd)) {
                layer.msg('密码不能含有中文', {icon: 5, anim: 6});
                return false;
            }
            ;
            if (data.field.user.length > 20 || data.field.user.length < 3) {
                layer.msg('用户名为3-20位', {icon: 5, anim: 6});
                return false;
            }
            if (data.field.pwd.length > 20 || data.field.pwd.length < 6) {
                layer.msg('密码为6-20位', {icon: 5, anim: 6});
                return false;
            }
            $.ajax({
                url: '<?php echo url("Login/reg"); ?>',
                type: 'POST',
                data: {
                    user: data.field.user,
                    pwd: data.field.pwd,
                    code: data.field.code
                },
                success: function (res) {
                    if (res.code == 1) {
                        layer.alert(res.msg, {
                            title: '消息'
                        })
                        window.location.href='<?php echo url("Login/login"); ?>'
                    } else if (res.code == 0) {
                        layer.msg(res.msg, {icon: 5, anim: 6});
                    }else{
                        layer.msg(res, {icon: 5, anim: 6});
                    }
                }
            })
            return false;
        });

    });

    function login() {
        window.location.href='<?php echo url("Login/login"); ?>';
    }

</script>
</body>
</html>