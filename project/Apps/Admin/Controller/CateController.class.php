<?php
namespace Admin\Controller;
use Think\Controller;
class CateController extends CommonController{
    //用户的列表
    public function index(){
    	//创建对象
    	$cate = M('cate');

        //获取关键字
        if(!empty($_GET['keyword'])){
            $where = "where name like '%".$_GET['keyword']."%'";
            $w = "name like '%".$_GET['keyword']."%'";
        }else{
            $where = '';
        }

        //获取每页显示的数量
        $num = !empty($_GET['num']) ? $_GET['num'] : 10;
        
    	//统计总数
    	$count = $cate->where($w)->count();
        // var_dump($count);
    	// 实例化分页类
    	$Page = new \Think\Page($count,$num);

    	//获取limit
    	$limit = $Page->firstRow.','.$Page->listRows;
    	// 分页显示输出
    	$pages = $Page->show();
    	// var_dump($pages);
    	//查询
    	// $cates = $cate->limit($limit)->where($where)->select();
        $sql = "select * from yc_cate ".$where." order by concat(path,id) asc limit ".$limit;
        
        $cates = $cate->query($sql);
        //查看sql语句
        // echo $cate->_sql();
        // var_dump($cates);die;
        foreach ($cates as $k => $v) {
            //计算出分隔多少次
            $c = count(explode(',',$v['path']))-2;
            $cates[$k]['name'] = str_repeat('|-----',$c).$v['name'];
        }

    	//分配变量
    	$this->assign('cates',$cates);
    	$this->assign('pages',$pages);

    	//解析模板
    	$this->display(); 	
    }
    //用户的添加
    public function add(){
        //创建表对象
        $cate = M('cate');
        $cates = $cate->select();

        //分配变量
        $this->assign('cates',$cates);

    	//解析模板
    	$this->display();
    }
    //执行插入
    public function insert(){
        // 创建对象
        $cate = M('cate');
        //检测是否为顶级分类
        if($_POST['pid'] == 0){
            $_POST['path'] = '0,';
        }else{
            //按照pid查找分类的path信息
            $info = $cate->where('id = '.$_POST['pid'])->find();
            $_POST['path'] = $info['path'].$info['id'].',';
        }

        //创建数据
        $cate->create();
        //执行添加
        if($cate->add()){
             //添加成功
            $this->success('添加成功',U('Admin/Cate/index'));
        }else{
            //失败
            $this->error('添加失败',U('Admin/Cate/index'));
        }

    }

    //执行删除
    public function delete(){
       // var_dump();
        $id = I('get.id');
        $cate = M('cate');
        $info = $cate->find($id);
        //拼接path
        $path = $info['path'].$info['id'].',';
        // 删除子类信息
       $res =  $cate->where('path like "'.$path.'%" or id='.$id)->delete();
      
       //执行添加
        if($res){
             //添加成功
            $this->success('删除成功',U('Admin/Cate/index'));
        }else{
            //失败
            $this->error('删除失败',U('Admin/Cate/index'));
        }
    }

    //用户修改
    public function edit(){
        $id = I('get.id');
        //查询出所有的分类
        $cate = M('cate');
        $cates = $cate->query('select * from yc_cate where id != '.$id.' order by concat(path,id) asc');
        
        foreach ($cates as $k => $v) {
            //计算出分隔多少次
            $c = count(explode(',',$v['path']))-2;
            $cates[$k]['name'] = str_repeat('|-----',$c).$v['name'];
        }
        //根据id查询要修改的哪个数据
        
        $info = $cate->find($id);

        //分配变量
        $this->assign('info',$info);
        $this->assign('cates',$cates);
        //解析模板
        $this->display();

    }

    //执行修改
    public function update(){

        $cate = M('cate');
       
        $info = $cate->where('id = '.$_POST['pid'])->find();
        $_POST['path'] = $info['path'].$info['id'].',';
        // var_dump($_POST);

        // die;
        $cate->create();
        $res = $cate->save();
          //执行添加
        if($res){
             //添加成功
            $this->success('修改成功',U('Admin/Cate/index'));
        }else{
            //失败
            $this->error('修改失败',U('Admin/Cate/index'));
        }
    }
}