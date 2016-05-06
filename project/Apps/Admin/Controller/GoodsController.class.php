<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends CommonController {
    //用户的列表
    public function index(){
    	//创建对象
    	// $Goods = M('Goods');

        //获取关键字
        if(!empty($_GET['keyword'])){
            $where = "where title like '%".$_GET['keyword']."%'";
        }else{
            $where = '';
        }

        //获取每页显示的数量
        $num = !empty($_GET['num']) ? $_GET['num'] : 5;
        
    	//统计总数
    	$count = $Goods->where($where)->count();
        // var_dump($count);
    	// 实例化分页类
    	$Page = new \Think\Page($count,$num);

    	//获取limit
    	$limit = $Page->firstRow.','.$Page->listRows;
    	// 分页显示输出
    	$pages = $Page->show();
    	// var_dump($pages);
    	//查询
        $Goods = $Goods->where($where)->limit($limit)->select();
        //查看sql语句
        // echo $Goods->_sql();
        var_dump($Goods);die;
       

    	//分配变量
    	$this->assign('Goods',$Goods);
    	$this->assign('pages',$pages);

    	//解析模板
    	$this->display(); 	
    }
    //用户的添加
    public function add(){
        //创建表对象
        $cate = M('cate');
        $cates = $cate->query('select * from yc_cate order by concat(path,id) asc');
        
        foreach ($cates as $k => $v) {
            //计算出分隔多少次
            $c = count(explode(',',$v['path']))-2;
            $cates[$k]['name'] = str_repeat('|-----',$c).$v['name'];
        }

        //分配变量
        $this->assign('cates',$cates);

    	//解析模板
    	$this->display();
    }
    //执行插入
    public function insert(){
  
        // 创建对象
        // $Goods = M('Goods');
    	$Goods = D('Goods');

        //创建数据
        if(!$Goods->create()){
        	//获取错误信息
        	$info = $Goods->getError();
        	$this->error($info,'',3);
        }
        // var_dump($_POST);
        // die;
        //执行添加
        if($Goods->add()){
             //添加成功
            $this->success('添加成功',U('Admin/Goods/index'));
        }else{
            //失败
            $this->error('添加失败',U('Admin/Goods/index'));
        }

    }

    //执行删除
    public function delete(){
       // var_dump();
        $id = I('get.id');
        $Goods = M('Goods');
        $info = $Goods->find($id);
        //拼接path
        $path = $info['path'].$info['id'].',';
        // 删除子类信息
       $res =  $Goods->where('path like "'.$path.'%" or id='.$id)->delete();
      
       //执行添加
        if($res){
             //添加成功
            $this->success('删除成功',U('Admin/Goods/index'));
        }else{
            //失败
            $this->error('删除失败',U('Admin/Goods/index'));
        }
    }

    //用户修改
    public function edit(){
        $id = I('get.id');
        //查询出所有的分类
        $Goods = M('Goods');
        $Goodss = $Goods->query('select * from yc_Goods where id != '.$id.' order by concat(path,id) asc');
        
        foreach ($Goodss as $k => $v) {
            //计算出分隔多少次
            $c = count(explode(',',$v['path']))-2;
            $Goodss[$k]['name'] = str_repeat('|-----',$c).$v['name'];
        }
        //根据id查询要修改的哪个数据
        
        $info = $Goods->find($id);

        //分配变量
        $this->assign('info',$info);
        $this->assign('Goodss',$Goodss);
        //解析模板
        $this->display();

    }

    //执行修改
    public function update(){

        $Goods = M('Goods');
       
        $info = $Goods->where('id = '.$_POST['pid'])->find();
        $_POST['path'] = $info['path'].$info['id'].',';
        // var_dump($_POST);

        // die;
        $Goods->create();
        $res = $Goods->save();
          //执行添加
        if($res){
             //添加成功
            $this->success('修改成功',U('Admin/Goods/index'));
        }else{
            //失败
            $this->error('修改失败',U('Admin/Goods/index'));
        }
    }
}