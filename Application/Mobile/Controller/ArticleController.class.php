<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace Mobile\Controller;
use Home\Logic\CartLogic;
use Think\Controller;
use Think\Page;
use Think\Verify;
class ArticleController extends MobileBaseController {

    //news
    public function news(){
        $start = I('get.start') ? I('get.start') : 0 ; //从第几条开始
        $limit = $start.',10'; //做limit条件
        $data = M("Article a")->field("a.article_id,a.title,b.cat_name")
                              ->where("a.is_open=1")
                              ->join("tp_article_cat b ON a.cat_id=b.cat_id")
                              ->order("a.publish_time desc")
                              ->limit( $limit )
                              // ->limit( "0,10")
                              ->select();
                              // dump($data);
        if(IS_AJAX){
            if( $data ){
                $return['code'] = 200;
                $return['result'] = $data;
            }else{
                $return['code'] =400;
            }
            $this->ajaxReturn( $return ); 
        }
        $this->list = $data;
        $this->display();
    }
    /**
     * 文章内容页
     */
    public function detail(){
    	$article_id = I('article_id');
    	$data = M('article')->where("article_id=$article_id")->find();
        $data['content'] = htmlspecialchars_decode($data['content']);
        $data['publish_time'] = date("Y-m-d H:i",$data['publish_time']);
        $user_id = session('user.user_id');
        $where['article_id'] = $data['article_id'];
        $where['user_id'] = $user_id;
        //点赞量
        $likeNum = M("article_like")->where(array("article_id"=>$data['article_id']))->count();
        if( M("article_like")->where( $where )->count()){
            $this->_islike = 1;
        }else{
            $this->_islike = 0;
        }
        //收藏量
        $collectNum = M("article_collect")->where(array("article_id"=>$data['article_id']))->count();
        if( M("article_collect")->where( $where )->count()){
            $this->_iscollect = 1;
        }else{
            $this->_iscollect = 0;
        }
        //评论
        $comment = M("article_comment as a")->field("a.content,FROM_UNIXTIME(a.add_time,'%Y-%m-%d %H:%i') add_time,b.nickname,head_pic")
                                            ->where("a.article_id=$article_id and is_show='0'")
                                            ->join("tp_users as b ON a.user_id=b.user_id")
                                            ->order("a.add_time desc")
                                            ->limit("0,10")
                                            ->select();
        $this->comment = $comment;
        $this->commentNum = count($comment);
        $this->data = $data;
        $this->likeNum = $likeNum;
        $this->collectNum = $collectNum;
        $this->display();
    }
    //点赞
    public function isLike(){
        $data['article_id'] = trim(I('post.id'));
        $data['user_id'] = session('user.user_id');
        $data['last_time'] = time();
        $result = M("ArticleLike")->data( $data )->add();
        if( $result ){
            $like = M('ArticleLike')->where(array("article_id"=>$data['article_id'],"is_like"=>'1'))->count();
            $this->ajaxReturn(array('code'=>200,'result'=>$like));
        }else{
            $this->ajaxReturn(array('code'=>400));
        }
    }
    //收藏
    public function isCollect(){
        $model['article_id'] = $_POST['id'];
        $model['user_id'] = session('user.user_id');
        $Model = M('article_collect');
        if($Model->where(array("article_id"=>$model['article_id'],"user_id"=>$model['user_id']))->find()){
            $return['act'] = 'del';
            $result = $Model->where(array("article_id"=>$model['article_id'],"user_id"=>$model['user_id']))->delete();
        }else{
            $return['act'] = 'add';
            $result = $Model->add($model);
        }
        $num = $Model->where(array("article_id"=>$model['article_id']))->count();
        if( $result ){
            $return['code'] = 200 ;
            $return['num'] = $num ;
            $this->ajaxReturn( $return );
        }
    }

    //按需加载评论   ajax
    public function commentList(){
        $article_id=I('get.article_id');
        $start = I('get.start'); //从第几条开始
        $limit = $start.',10'; //做limit条件
        $result = M("article_comment as a")->field("a.content,FROM_UNIXTIME(a.add_time,'%Y-%m-%d %H:%i') add_time,b.nickname,head_pic")
                                            ->where("a.article_id=$article_id and is_show='0'")
                                            ->join("tp_users as b ON a.user_id=b.user_id")
                                            ->order("a.add_time desc")
                                            ->limit( $limit )
                                            ->select();
        if($result){
            $data['result'] = $result;
            $data['code'] = 200;
            $this ->ajaxReturn($data);
        }else{
            $data['code'] = 400;
            $this ->ajaxReturn($data);
        }

    }

    
    //点击次数，每点击一次增加一次
    public function clickNum($article_id){
        $article = M('article');
        $num = $article->field('click')->where(" article_id = $article_id ")->find();
        $data['article_id'] = $article_id;
        $data['click'] = $num['click']+1;
        $article->save($data);

    }

    //评论页面
    public function comment(){
        $this->assign("article_id",$_GET['article_id']);
        $this->assign("title",$_GET['title']);
        $this->display();
    } 
    //插入评论
    public function addComment(){
        $comment = M('article_comment');
        $dat['article_id']=I('post.article_id');
        $dat['article_title']=I('post.title');
        $dat['user_id']=session('user.user_id');
        $dat['add_time']=time();
        $dat['ip_address']=get_client_ip();
        $dat['content']=I('post.question');
        $res = $comment->add($dat);
        if($res){
            $data['status'] = 1;
            $this->ajaxReturn($data);
        }else{
            $data['status'] = 0;
            $this->ajaxReturn($data);
        }
    }
    //文章评论回复ajax
    public function repeatComment(){
        $repeatComment = M('article_comment_repeat');
        $data['article_id'] = $_POST['article_id']; //获取文章id
        $data['comment_id'] = $_POST['comment_id']; //获取评论id
        $data['user_id'] = $_POST['user_id']; //获取被回复人
        $data['replay'] = $_POST['replay']; //获取回复信息
        $data['add_time'] = time();
        $data['person_id'] = session('user')['user_id']; //当前session中的用户
        // var_dump($data);die;
        $res = $repeatComment->add($data);
        if($res){
            $data['code'] = 200;
            $this->ajaxReturn($data);
        }else{
            $data['code'] = 400;
            $this->ajaxReturn($data);
        }
    }

    //循环处理相关数据切换
    function eachData( $result ){
        $array  = array();
        foreach ($result as $key => $value) {
            $array[$key]['content'] = htmlspecialchars_decode($value['content']);
        }
        return $array;
    }  

    //珠宝学院
    public function college(){
        $pid = trim(I('get.pid'));
        if(!IS_AJAX){
            if(!S($pid)){
                $list = M("article_cat")->where(array("parent_id"=>$pid))
                                        ->field("cat_id,cat_name,parent_id")
                                        ->order("sort_order desc")
                                        ->select();
                S( $pid ,$list,10);
            }else{
                $list = S($pid);
            }
        }
        $start = I('get.start') ? I('get.start') : 0;
        $cid = I('get.cid') ? I('get.cid') : ($list[0]['cat_id'] ? $list[0]['cat_id'] : '');
        $name = M("article_cat")->where(array("cat_id"=>$pid))->field('cat_name')->find();
        $cat_name = M("article_cat")->where(array("cat_id"=>$cid))->field("cat_name")->find();
        $data = M("article")->where(array('cat_id'=>$cid,'is_open'=>'1'))
                            ->field("article_id,cat_id,title,content,description,publish_time,thumb")
                            ->order("sort desc")
                            ->limit("$start,5")
                            // ->limit("0,5")
                            ->select();

        if( $data ){
            $return['code'] = 200; 
            foreach ($data as $key => $value) {
                $data[$key]['content'] = mb_substr(htmlspecialchars_decode( $value['content'] ),0,100);
                $data[$key]['publish_time'] = date("Y/m/d", $value['publish_time']);
            }
        }else{
            $return['code'] =400;
        }
        if(IS_AJAX){ 
            $return['result'] = $data;
            $this->ajaxReturn( $return ); 
        }
        $this->data = $data;
        $this->nav = $cat_name ? $cat_name : $name;
        $this->pid = $pid;
        $this->cid = $cid;
        $this->list = $list;
        $this->display();
    }  

    //expert专家栏
    public function expert(){
        $pid = I('get.pid');
        if(!IS_AJAX){
	        if(!S($pid)){
	            $list = M("article_cat")->where(array("parent_id"=>$pid))
	                                    ->field("cat_id,cat_name,parent_id,cat_logo")
	                                    ->order("sort_order desc")
	                                    ->select();
	            S( $pid ,$list,10);
	        }else{
	            $list = S($pid);
	        }
    	}
        $start = I('get.start') ? I('get.start') : 0;
        $cid = I('get.cid') ? I('get.cid') : ($list[0]['cat_id'] ? $list[0]['cat_id'] : '');
        $navList = M("article_cat")->where(array("parent_id"=>$cid))
        						   ->field("cat_id,cat_name")
        						   ->order("sort_order desc")
        						   ->select();
        $ntroduce = array();//简介
        $article = array();//文章
        foreach ($navList as $key => $value) {
        	if( strpos($value['cat_name'],'简介') ){
        		$ntroduce = $value;
        	}else{
        		$article = $value;
        	}
        }
        //简介
        $Model = M("article");
       	$_info = $Model->where(array("cat_id"=>$ntroduce['cat_id']))->field("content")->find();
       	$_info['content'] = preg_replace ('/\<p\>\<\/p\>|\<p\><br\/>\<\/p\>/','', htmlspecialchars_decode($_info['content']));
       	//文章
       	$_alist = $Model->where(array("cat_id"=>$article['cat_id']))
       					->field("article_id,title,content,publish_time")
       					->order("sort desc")
       					->limit("$start,5")
       					// ->limit("$start,1")
       					->select();
       	foreach ($_alist as $key => $value) {
       		$_alist[$key]['content'] = mb_substr(preg_replace('/\<p\>|\<\/p\>/','',htmlspecialchars_decode( $value['content'] )),0,60).'...';
       		$_alist[$key]['publish_time'] = date("Y-m-d", $value['publish_time']);
       	}
       	if(IS_AJAX){ 
            if( $_info['content'] || $_alist){
                $return['code'] = 200;
                $return['id'] = $article['cat_id'];
                $return['expert'] = $_info;
                $return['article'] = $_alist;
            }else{
                $return['code'] =400;
            }
            $this->ajaxReturn( $return ); 
        }
        $this->id = $article['cat_id'];
  		$this->alist = $_alist;
  		$this->info = $_info['content'];
        $this->list = $list;
        $this->display();
    }
    //滚动加载文章
    public function ajaxArticle(){
    	if(IS_AJAX){
    		$start = I('get.start');
	    	$cat_id = I('get.cat_id');
	    	$_alist =  M("article")->where(array("cat_id"=>$cat_id))
	       					->field("article_id,title,content,publish_time")
	       					->order("sort desc")
	       					->limit("$start,5")
	       					->select();
	       	if( $_alist ){
	       		foreach ($_alist as $key => $value) {
		       		$_alist[$key]['content'] = mb_substr(preg_replace('/\<p\>|\<\/p\>/','',htmlspecialchars_decode( $value['content'] )),0,60).'...';
		       		$_alist[$key]['publish_time'] = date("Y-m-d", $value['publish_time']);
		       	}
		       	$return['status'] = 1;
				$return['result'] = $_alist;
	        }else{
	        	$return['status'] = 400;
	        }
	        $this->ajaxReturn( $return ); 
    	}
    	
    }
    //baike
    public function baike(){
        $pid = trim(I('get.pid'));
        if(!IS_AJAX){
            if(!S($pid)){
                $list = M("article_cat")->where(array("parent_id"=>$pid))
                                        ->field("cat_id,cat_name,parent_id")
                                        ->order("sort_order desc")
                                        ->select();
                S( $pid ,$list,10);
            }else{
                $list = S($pid);
            }
        }
        $start = I('get.start') ? I('get.start') : 0;
        $cid = I('get.cid') ? I('get.cid') : ($list[0]['cat_id'] ? $list[0]['cat_id'] : '');
        $name = M("article_cat")->where(array("cat_id"=>$pid))->field('cat_name')->find();
        $cat_name = M("article_cat")->where(array("cat_id"=>$cid))->field("cat_name")->find();
        $data = M("article")->where(array('cat_id'=>$cid,'is_open'=>'1'))
                            ->field("article_id,cat_id,title")
                            ->order("sort desc")
                            ->limit("$start,10")
                            // ->limit("0,5")
                            ->select();
        if(IS_AJAX){ 
            if( $data ){
                $return['code'] = 200;
                $return['result'] = $data;
            }else{
                $return['code'] =400;
            }
            $this->ajaxReturn( $return ); 
        }
        $this->data = $data;
        $this->nav = $cat_name ? $cat_name : $name;
        $this->pid = $pid;
        $this->cid = $cid;
        $this->list = $list;
        $this->display();
    }
    //standard
    public function standard(){
        $pid = trim(I('get.pid'));
        if(!IS_AJAX){
            if(!S($pid)){
                $list = M("article_cat")->where(array("parent_id"=>$pid))
                                        ->field("cat_id,cat_name,parent_id")
                                        ->order("sort_order desc")
                                        ->select();
                S( $pid ,$list,10);
            }else{
                $list = S($pid);
            }
        }
        $start = I('get.start') ? I('get.start') : 0;
        $cid = I('get.cid') ? I('get.cid') : ($list[0]['cat_id'] ? $list[0]['cat_id'] : '');
        $name = M("article_cat")->where(array("cat_id"=>$pid))->field('cat_name')->find();
        $cat_name = M("article_cat")->where(array("cat_id"=>$cid))->field("cat_name")->find();
        $data = M("article")->where(array('cat_id'=>$cid,'is_open'=>'1'))
                            ->field("article_id,cat_id,title")
                            ->order("sort desc")
                            ->limit("$start,10")
                            // ->limit("0,5")
                            ->select();
        if(IS_AJAX){ 
            if( $data ){
                $return['code'] = 200;
                $return['result'] = $data;
            }else{
                $return['code'] =400;
            }
            $this->ajaxReturn( $return ); 
        }
        $this->data = $data;
        $this->nav = $cat_name ? $cat_name : $name;
        $this->pid = $pid;
        $this->cid = $cid;
        $this->list = $list;
        $this->display();
    }
}