<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    //显示
    public function index(){
    	
        //解析模板
        $this->display();
    }
    //验证用户信息
    public function login(){
        // var_dump($_POST);
        $user = M('user');

        $username = I('post.username');
        $password = I('post.password');

        //查询
        $info = $user->where('username = "'.$username.'" and password = "'.$password.'"')->find();
        // echo $user->_sql();
        // var_dump($info);die;
        //检测 如果用户存在
        if(!empty($info)){
            session_start();
            $_SESSION['uid'] = $info['id'];
           session('username',$info['username']);
           // var_dump($_SESSION);die;
           $this->success('登录成功',U('Admin/index/index'),3);
        }else{
            $this->error('登录失败','',3);
        }
    }

    public function testMail(){
        // sendMail('13701383017@139.com','这是一个神奇的网站','您的验证码lamp123');
        sendMail('13701383017@139.com','这是一个神奇的网站','点击以下链接完成注册<a href="http://lamp.cn/Admin/login/verify?uuid=42">点击完成验证</a>');
    }

    public function verify(){
        var_dump($_GET);
        echo '完成注册';
    }
}