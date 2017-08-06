<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: 当燃      
 * Date: 2015-09-09
 */
namespace Admin\Controller;
use Admin\Logic\ArticleCatLogic;

class ArticleController extends BaseController {

    public function categoryList(){
        $ArticleCat = new ArticleCatLogic(); 
        $cat_list = $ArticleCat->article_cat_list(0, 0, false);
        $type_arr = array('系统默认','系统帮助','系统公告');
        $this->assign('type_arr',$type_arr);
        $this->assign('cat_list',$cat_list);
        $this->display('categoryList');
    }
    
    public function category(){
        $ArticleCat = new ArticleCatLogic();  
 		$act = I('GET.act','add');
        $this->assign('act',$act);
        $cat_id = I('GET.cat_id');
        $cat_info = array();
        if($cat_id){
            $cat_info = D('article_cat')->where('cat_id='.$cat_id)->find();
            $this->assign('cat_info',$cat_info);
        }
        $cats = $ArticleCat->article_cat_list(0,$cat_info['parent_id'],true);
        $this->assign('cat_select',$cats);
        $this->display();
    }
    
    public function articleList(){
        $Article =  M('Article'); 
        $res = $list = array();
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        $where = " 1 = 1 ";
        $keywords = trim(I('keywords'));
        if($keywords){
            $where = "title like '%$keywords%'";
        }
        $cat_id = I('cat_id',0);
        $cat_id && $where.=" and cat_id = $cat_id ";
        $res = $Article->where($where)->order('article_id desc')->page("$p,$size")->select();
        $count = $Article->where($where)->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出

        $ArticleCat = new ArticleCatLogic();
        $cats = $ArticleCat->article_cat_list(0,0,false);
        if($res){
        	foreach ($res as $val){
        		$val['category'] = $cats[$val['cat_id']]['cat_name'];
        		$val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);        		
        		$list[] = $val;
        	}
        }
        $this->assign('cats',$cats);
        $this->assign('cat_id',$cat_id);
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        $this->display('articleList');
    }
    
    public function article(){
        $ArticleCat = new ArticleCatLogic();
 		$act = I('GET.act','add');
        $info = array();
        $info['publish_time'] = time()+3600*24;
        if(I('GET.article_id')){
           $article_id = I('GET.article_id');
           $info = D('article')->where('article_id='.$article_id)->find();
        }
        $cats = $ArticleCat->article_cat_list(0,$info['cat_id']);
        $this->assign('cat_select',$cats);
        $this->assign('act',$act);
        $this->assign('info',$info);
        $this->initEditor();
        $this->display();
    }
    
    /**
     * 文章评论页
     */
    public function comment(){ 
        $Activity =  M('article_comment as a'); 
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        $keywords['username'] = I('nickname','','trim');
        $keywords['content'] = I('content','','trim');
        $where =' a.is_show = 0';
        if( $keywords['content'] ){
            $where .= " AND a.content like '%{$keywords['content']}%'";
        } 
        if( $keywords['username'] ){
            $as = " OR b.nickname like '%{$keywords['username']}%'";
        }
 
        $data = $Activity->where( $where )
                         ->field("a.*,b.nickname")
                         ->join("tp_users as b ON a.user_id=b.user_id $as")
                         ->order('a.add_time desc')
                         ->page("$p,$size")
                         ->select();
        $count = $Activity->where( $where )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出
        $this->assign('keywords',$keywords);
        $this->assign('comment_list',$data);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        $this->display(); 
    }
    /**
     * ajax更新文章排序
     */
    public function changeTableSort(){   
        $id_name = I('id_name'); // 表主键id名
        $id_value = I('id_value'); // 表主键id值
        $field  = I('field'); // 修改哪个字段
        $value  = I('value'); // 修改字段值 
        $Model = M('Article');             
        $res = $Model->where("$id_name = $id_value")->setField(array($field=>$value)); // 根据条件保存修改的数据
        if( $res ){
            $this->ajaxReturn(array('code'=>200));
        }else{
            $reslut = $Model->where(array($id_name=>$id_value))->getField('sort');
            $this->ajaxReturn( $reslut );
        }
    } 
    
    /**
     * 初始化编辑器链接
     * @param $post_id post_id
     */
    private function initEditor()
    {
        $this->assign("URL_upload", U('Admin/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_fileUp", U('Admin/Ueditor/fileUp',array('savepath'=>'article')));
        $this->assign("URL_scrawlUp", U('Admin/Ueditor/scrawlUp',array('savepath'=>'article')));
        $this->assign("URL_getRemoteImage", U('Admin/Ueditor/getRemoteImage',array('savepath'=>'article')));
        $this->assign("URL_imageManager", U('Admin/Ueditor/imageManager',array('savepath'=>'article')));
        $this->assign("URL_imageUp", U('Admin/Ueditor/imageUp',array('savepath'=>'article')));
        $this->assign("URL_getMovie", U('Admin/Ueditor/getMovie',array('savepath'=>'article')));
        $this->assign("URL_Home", "");
    }
    
    
    public function categoryHandle(){
    	$data = I('post.');  
        if($data['act'] == 'add'){           
            $d = D('article_cat')->add($data);
        }
        
        if($data['act'] == 'edit')
        {
        	if ($data['cat_id'] == $data['parent_id']) 
			{
        		$this->error("所选分类的上级分类不能是当前分类",U('Admin/Article/category',array('cat_id'=>$data['cat_id'])));
        	}
        	$ArticleCat = new ArticleCatLogic();
        	$children = array_keys($ArticleCat->article_cat_list($data['cat_id'], 0, false)); // 获得当前分类的所有下级分类
        	if (in_array($data['parent_id'], $children))
        	{
        		$this->error("所选分类的上级分类不能是当前分类的子分类",U('Admin/Article/category',array('cat_id'=>$data['cat_id'])));
        	}
        	$d = D('article_cat')->where("cat_id=$data[cat_id]")->save($data);
        }
        
        if($data['act'] == 'del'){      	
        	$res = D('article_cat')->where('parent_id ='.$data['cat_id'])->select(); 
        	if ($res)
        	{
        		exit(json_encode('还有子分类，不能删除'));
        	}
        	$res = D('article')->where('cat_id ='.$data['cat_id'])->select();       	      	
        	if ($res)
        	{
        		exit(json_encode('非空的分类不允许删除'));
        	}      	
        	$r = D('article_cat')->where('cat_id='.$data['cat_id'])->delete();
        	if($r) exit(json_encode(1));
        }
        if($d){
        	$this->success("操作成功",U('Admin/Article/categoryList'));
        }else{
        	$this->error("操作失败",U('Admin/Article/categoryList'));
        }
    }
    
    public function aticleHandle(){
        $data = I('post.');
        $data['publish_time'] = strtotime($data['publish_time']);
        //$data['content'] = htmlspecialchars(stripslashes($_POST['content']));
        if($data['act'] == 'add'){
            $data['click'] = mt_rand(100,200);
        	$data['add_time'] = time(); 
            $r = D('article')->add($data);
        }
        
        if($data['act'] == 'edit'){
            $r = D('article')->where('article_id='.$data['article_id'])->save($data);
        }
        
        if($data['act'] == 'del'){
        	$r = D('article')->where('article_id='.$data['article_id'])->delete();
        	if($r) exit(json_encode(1));       	
        }
        $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Admin/Article/articleList');
        if($r){
            $this->success("操作成功",U('Article/articleList'));
        }else{
            $this->error("操作失败",$referurl);
        }
    }
    
    
    public function link(){
    	$act = I('GET.act','add');
    	$this->assign('act',$act);
    	$link_id = I('GET.link_id');
    	$link_info = array();
    	if($link_id){
    		$link_info = D('friend_link')->where('link_id='.$link_id)->find();
    		$this->assign('info',$link_info);
    	}
    	$this->display();
    }
    
    public function linkList(){
    	$Ad =  M('friend_link');
        $keywords = I('post.keywords');
        if($keywords){
            $where = "link_name like '%$keywords%' or link_url like '%$keywords%'";
        }else{
            $where = "1=1";
        }
    	$res = $Ad->where( $where )->order('orderby')->page($_GET['p'].',10')->select();
    	if($res){
    		foreach ($res as $val){
    			$val['target'] = $val['target']>0 ? '开启' : '关闭';
    			$list[] = $val;
    		}
    	}
    	$this->assign('list',$list);// 赋值数据集
    	$count = $Ad->where('1=1')->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
    	$show = $Page->show();// 分页显示输出
    	$this->assign('page',$show);// 赋值分页输出
        $this->keywords = $keywords;
    	$this->display();
    }
    
    public function linkHandle(){
        $data = I('post.');
    	if($data['act'] == 'add'){
    		stream_context_set_default(array('http'=>array('timeout' =>2)));send_http_status('311');
    		$r = D('friend_link')->add($data);
    	}
    	if($data['act'] == 'edit'){
    		$r = D('friend_link')->where('link_id='.$data['link_id'])->save($data);
    	}
    	
    	if($data['act'] == 'del'){
    		$r = D('friend_link')->where('link_id='.$data['link_id'])->delete();
    		if($r) exit(json_encode(1));
    	}
    	
    	if($r){
    		$this->success("操作成功",U('Admin/Article/linkList'));
    	}else{
    		$this->error("操作失败",U('Admin/Article/linkList'));
    	}
    }

    //写稿权限页面
    public function writeRole(){
        //读出所有的用户及其他们的写稿权限
        $users = M('Users')->field('user_id,nickname,write_role')->select();
        //读出所有的分类
        $cate = M('article_cat')->field('cat_id,cat_name')->where('show_in_nav = 1')->order('sort_order')->select();
        // show_dump($cate);
        $this->assign('write_role',$users['write_role']); //把该用户的
        $this->assign('users',$users);
        $this->assign('cate',$cate);
        $this->display();
    }
    //修改权限
    public function alterWriteRole(){
        // show_dump($_POST);die;
        //接收post数据
        $user_id = $_POST['user_id']; //用户id
        $cat_id = $_POST['write_role'];//权限 eg:95
        //制作条件和数据
        $where = " user_id = ".$user_id;
        $data['write_role'] = $cat_id;
        $data['cat_name'] = $cat_name;

        $writeRole = M('article_write_role');
        //查询（写稿权限表）是否已经有添加
        $Is_user =  $writeRole->where("user_id = ".$user_id )->find();
        if(!empty($Is_user)){ // 如果已经存在权限就只能做修改（article_write_role）操作（一个用户只有一个写稿的权限）
            $res = $writeRole->where($where)->save($data);
        }else{
            //如果没有权限就添加
            $writeRole->create();
            $res = $writeRole->add();
        }
            //并更新users表的权限字段
            $userRes = M('users')->where($where)->save($data);
        if($res && $userRes){
            $this->success('修改成功', 'writeRole');
        }else{
            $this->error('修改失败，请联系管理员', 'writeRole');
        }

    }
    //热词列表
    public function hotWodsList(){
        $hotwords = M('article_hotwords');
        $res = $hotwords->where("is_open = 1")->select();
        if($res){
            $this->assign('list',$res);
        }
        $this->display();
    }
    //添加热词
    public function hotWodsInsert(){
        // show_dump($_POST);
        $hotwords = M('article_hotwords');
        if($hotwords ->create()){
            $res = $hotwords->add();
            if($res){
                $this->success('添加成功', 'hotWodsList');
            }else{
                $this->error('添加失败，请联系管理员', 'hotWodsList');
            }
        }
        
    }
    //热词编辑
    public function hotwordedit(){
        $hotwords = M('article_hotwords');
        $id = $_GET['id'];
        $res = $hotwords->where(array('id'=>$id))->find();
        $this ->assign('info',$res);
        $this->display();
    }
    //更新热词
    public function hotwordupdata(){
        // show_dump($_POST);
        $hotwords = M('article_hotwords');
        if($hotwords->create()){
            $res = $hotwords->save();
            if($res){
                $this->success('更新成功', 'hotWodsList');
            }else{
                $this->error('更新失败', 'hotWodsList');
            }
        }
    }
    public function del(){
        $id = I('get.id');
        $row = M('article_comment')->where(array('article_comment_id'=>$id))->delete();
        if($row){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

     public function op(){
        $type = I('post.type');
        $selected_id = I('post.selected');
        if(!in_array($type,array('del','show','hide')) || !$selected_id)
            $this->error('非法操作');
        $where = "article_comment_id IN ({$selected_id})";
        if($type == 'del'){
            //删除回复
            $where .= " OR parent_id IN ({$selected_id})";
            $row = M('article_comment')->where($where)->delete();
//            exit(M()->getLastSql());
        }
        if($type == 'show'){
            $row = M('article_comment')->where($where)->save(array('is_show'=>0));
        }
        if($type == 'hide'){
            $row = M('article_comment')->where($where)->save(array('is_show'=>1));
        }
        if(!$row)
            $this->error('操作失败');
        $this->success('操作成功');

    }
    //更新热词
    public function hotworddel(){
        $id = $_GET['id'];
        $hotwords = M('article_hotwords');
        $where = "id = ".$id;
        $data['is_open'] = 0;
        $res = $hotwords->where($where)->save($data);
        if($res){
                $this->success('删除成功' );
            }else{
                $this->error('删除失败');
            }
    }
    public function help_list(){
    	
    }
    
    public function notice_list(){
    	
    }
}