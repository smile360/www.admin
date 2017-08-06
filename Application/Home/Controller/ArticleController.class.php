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
namespace Home\Controller;
use Home\Logic\ArticleLogic;
use Think\Model;
use Think\Page;
class ArticleController extends BaseController {
    
    public function index(){  

        $article_id = I('article_id',38);
    	$article = D('article')->where("article_id=$article_id")->find();
    	$this->assign('article',$article);
        $this->display();
    }

    //最新动态取30条
    public function news(){
        $keyword = I('seach') ? I('seach'):'';
        $data = M("Article a")->field("a.article_id,a.title,FROM_UNIXTIME(a.add_time,'%Y-%m-%d') publish_time,b.cat_name")
                              ->where("a.is_open=1 and a.title like '%{$keyword}%'")
                              ->join("tp_article_cat b ON a.cat_id=b.cat_id")
                              ->order("a.publish_time desc")
                              ->limit(30)
                              ->select();
                              // dump($data);
        $this->keyword = $keyword;
        $this->assign("list",$data);
        $this->display(); 
    }
    /**
     * 文章内列表页
     */
    public function articleList(){
        //菜单start
        $model = M('articleCat');
        $article = M('article');
        //获取前4个分类
        $one = $model->field("cat_id,cat_name")->where("show_in_nav=1")->order("sort_order")->limit(4)->select();
        //获取排序为56的分类
        $twotwo = $model->field("cat_id,cat_name")->where("show_in_nav=1")->order("sort_order")->limit(4,2)->select();
        $this->assign('one',$one);
        $this->assign('twotwo',$twotwo);
        //菜单end

        $articleCat = D('articleCat'); //分类表 

        //获取文章列表
        $cat_id = $_GET['cat_id'];//获取文章分类
        if(empty($cat_id)){
            $where = 'is_open = 1';
        }else{
            $where = " is_open = 1 and cat_id = $cat_id ";
        }
        // echo $where;die;
        //获取文章总条数
        $total = M('article')->where($where)->count();
        // echo M('article')->getLastSql();die;
        $page = new Page($total,2);
        $pages = $page ->show();
        $this->assign('pages',$pages);
        $limit = $page->firstRow.','.$page->listRows;
        // echo $limit;die;
        $list = M('article')->where($where)->order('add_time')->limit($limit)->select();
        
        if($list){
            foreach ($list as $k => $v) {
                $a_id = $v['article_id'];
                $count = M('article_comment')->where("article_id = $a_id")->count();
                // var_dump($count);die;
                $list[$k]['commentNum'] = $count;
            }
            $this ->assign('info',$list);
        }else{
            echo '该分类还没有添加文章哦~';
            return false;
        }
        $this->display();
    }    
    /**
     * 文章内列表页(阅读)
     */
    public function articleYueList(){
        //获取分类
        $cat = M('article_cat')->field("cat_id")->where("show_in_nav = 1 ")->limit(4)->select();
        // var_dump($cat);die;
        $cat_num = getcates($cat);
        M('article')->where('cat_id ')->select();
    }
    
    
    /**
     * 文章内容页
     */
    public function detail(){
        $articleCat = D('articleCat'); //分类表 
        $article_id = I('article_id');//获取传递过来的文章id
        if(empty( $article_id )){
            $this->error("请稍候再试！");
        }
        $comment = M('article_comment a');//评论表
        $articleCollect = D('articleCollect');//收藏
        if(session('?user')){
            $user_id = session('user.user_id');
            $collect = $articleCollect->getArticleCollect($article_id,$user_id);
            $this->assign("collect",$collect);
            $like = $articleCollect->getArticleLike($article_id,$user_id);
            $this->assign("like",$like);
        }
        $likeCount = $articleCollect->getlikeCount( $article_id );
        $collectCount = $articleCollect->getcollectCount( $article_id );
        $this->assign("collectCount",$collectCount);
        $this->assign("likeCount",$likeCount);
        //评论详情
        $comment_R = $comment ->where("a.article_id=$article_id and a.is_show = 0 ")
                              ->field("a.*,b.nickname,head_pic")
                              ->join("tp_users b ON a.user_id=b.user_id")  
                              ->select();
        $this ->assign('comment',$comment_R);
        //评论统计
        $commentCount = count( $comment_R );
        $this->assign("comcount",$commentCount);
        //文章详情
    	$article = D('article')->where("article_id=$article_id")->find();
        $article['content'] = htmlspecialchars_decode($article['content']);
    	if($article){
            //增加点击次数
            M('article')->where(array('article_id'=>$article_id))->setInc('click',1);
    		$parent = D('article_cat')->where("cat_id=".$article['cat_id'])->find();
    		$this->assign('cat_name',$parent['cat_name']);
    		$this->assign('article',$article);
    	}
        $this->display();
    } 
    
    //文章评论
    public function articleComment(){
        
        $articleC = M('article_comment');
        $data['article_id'] = $_POST['article_id'];
        $data['article_title'] = $_POST['title'];
        $data['parent_id'] = $_POST['cat_id'];
        $data['user_id'] = session('user')['user_id']; //用户id
        $data['content'] = $_POST['comment']; //评论内容
        $data['add_time'] = time(); //当前时间
        $data['ip_address'] = get_client_ip();
        $result = $articleC->data($data)->add();
        // if($result){
        //     echo "<script>alert('评论成功~')</script>";
        //     $_url = $_SERVER['HTTP_REFERER'].'#conment';
        //     header("Location: $_url"); 
        //     exit;
        // }else{
        //     echo "<script>alert('服务器维护，请稍后再试~')</script>";
        // }
        if( $result ){
            $this->success('评论成功！',U('article/detail',array('article_id'=>$data['article_id'])));
        }else{
            $this->error('评论失败,请稍候再试！'); 
        }
    }

    public function college(){
        $cat_id = trim(I('get.cat_id'));
        if(empty( $cat_id )){
            $this->error("请稍候再试！");
        }
        if(!S($cat_id)){
            $list = M("article_cat")->where(array("parent_id"=>$cat_id))
                                    ->field("cat_id,cat_name,parent_id")
                                    ->order("sort_order desc")
                                    ->select();
            S( $cat_id ,$list,10);
        }else{
            $list = S($cat_id);
        }
        $this->assign('cat_id',$cat_id);
        $this->assign('list' , $list);
        $this->display();
    }
    //分类下文章列表
    public function collegeList(){
        $cat_id = trim(I('cat_id'));
        if(empty( $cat_id )){
            $this->error("请稍候再试！");
        }
        if(IS_AJAX){
            $data = M("article")->where(array('cat_id'=>$cat_id))
                                ->field("article_id,title,content")
                                ->order('sort desc')
                                ->find();
            if($data){
                $data['content'] = htmlspecialchars_decode( $data['content']);
                exit(json_encode(array('msg'=>200,'result'=>$data)));
            }else{
                exit(json_encode(array('msg'=>'官方暂未推出，静候佳音')));
            }
        }
        $parent_id = trim(I('parent_id'));
        if(!S($parent_id)){
            $list = M("article_cat")->where(array("parent_id"=>$parent_id))
                                    ->field("cat_id,cat_name,parent_id")
                                    ->order("sort_order desc")
                                    ->select();
            S( $parent_id ,$list,60);
        }else{
            $list = S($parent_id);
        }
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 10 : $_REQUEST['size'];
        $Model = M("article");
        $data = $Model->where(array("cat_id"=>$cat_id,"is_open"=>'1'))
                      ->field("article_id,cat_id,title,content,FROM_UNIXTIME(publish_time,'%Y-%m-%d') publish_time,thumb")
                      ->order('sort desc')
                      ->page("$p,$size")
                      ->select();  
        $count = $Model->where( array("cat_id"=>$cat_id,"is_open"=>'1') )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出

        $this->assign('page' , $page);
        $this->assign('data' , $data);
        $this->assign('cat_id',$cat_id);
        $this->assign('parent_id',$parent_id);
        $this->assign('list' , $list);
        $this->display();
    } 
    public function homecoming(){
        $cat_id = trim(I('get.cat_id'));
        if(empty( $cat_id )){
            $this->error("请稍候再试！");
        }
        if(!S($cat_id)){
            $list = M("article_cat")->where(array("parent_id"=>$cat_id))
                                    ->field("cat_id,cat_name,parent_id")
                                    ->order("sort_order desc")
                                    ->select();
            S( $cat_id ,$list,10);
        }else{
            $list = S($cat_id);
        }
        $this->assign('cat_id',$cat_id);
        $this->assign('list' , $list);
        $this->display();
    }
    //分类下文章列表
    public function homecomingList(){
        $cat_id = trim(I('cat_id'));
        if(empty( $cat_id )){
            $this->error("请稍候再试！");
        }
        if(IS_AJAX){
            $data = M("article")->where(array('cat_id'=>$cat_id))
                                ->field("article_id,title,content")
                                ->order('sort desc')
                                ->find();
            if($data){
                $data['content'] = htmlspecialchars_decode( $data['content']);
                exit(json_encode(array('msg'=>200,'result'=>$data)));
            }else{
                exit(json_encode(array('msg'=>'官方暂未推出，静候佳音')));
            }
        }
        $parent_id = trim(I('parent_id'));
        if(!S($parent_id)){
            $list = M("article_cat")->where(array("parent_id"=>$parent_id))
                                    ->field("cat_id,cat_name,parent_id")
                                    ->order("sort_order desc")
                                    ->select();
            S( $parent_id ,$list,60);
        }else{
            $list = S($parent_id);
        }
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 10 : $_REQUEST['size'];
        $Model = M("article");
        $data = $Model->where(array("cat_id"=>$cat_id,"is_open"=>'1'))
                      ->field("article_id,cat_id,title,content,FROM_UNIXTIME(publish_time,'%Y-%m-%d') publish_time,thumb")
                      ->order('sort desc')
                      ->page("$p,$size")
                      ->select();  
        $count = $Model->where( array("cat_id"=>$cat_id,"is_open"=>'1') )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出

        $this->assign('page' , $page);
        $this->assign('data' , $data);
        $this->assign('cat_id',$cat_id);
        $this->assign('parent_id',$parent_id);
        $this->assign('list' , $list);
        $this->display();
    } 
    //pc端收藏
    public function collect(){
        $c = M('article_collect');
        $data['article_id'] = $_POST['id']; //获取文章id
        $data['user_id'] = session('user')['user_id']; 
        if(!session('?user')){
            $return = array("msg"=>"您还没登录，请先登录！");
            $this->ajaxReturn($return);
        }
        //查询是否已经收藏过
        $where = " article_id = ".$data['article_id']." and user_id = ".$data['user_id'].' ';
        // var_dump($where);
        $cres = $c->where($where)->find();
        if($cres){
            $return = array("msg"=>"您已经收藏过本篇文章！");
            $this->ajaxReturn($return);
            exit;
        }
        $res = $c->add($data);
        if( $res ){
            $count = $c->where(array('article_id'=>$data['article_id']))->count();
            $return = array("code"=>200,"result"=>$count);
            $this->ajaxReturn($return);
        }else{
            $return = array("msg"=>"收藏失败，请稍候再试！");
            $this->ajaxReturn($return);
        }

    }
    //pc端收藏
    public function like(){
        $c = M('article_like');
        $data['article_id'] = $_POST['id']; //获取文章id
        $data['user_id'] = session('user')['user_id']; 
        if(!session('?user')){
            $return = array("msg"=>"您还没登录，请先登录！");
            $this->ajaxReturn($return);
        }
        //查询是否已经收藏过
        $where = " article_id = ".$data['article_id']." and user_id = ".$data['user_id'].' ';
        // var_dump($where);
        $cres = $c->where($where)->find();
        if($cres){
            $return = array("msg"=>"您已经点赞过本篇文章！");
            $this->ajaxReturn($return);
            exit;
        }
        $data['last_time'] = time();
        $res = $c->add($data);
        if( $res ){
            $count = $c->where(array('article_id'=>$data['article_id']))->count();
            $return = array("code"=>200,"result"=>$count);
            $this->ajaxReturn($return);
        }else{
            $return = array("msg"=>"点赞失败，请稍候再试！");
            $this->ajaxReturn($return);
        }

    }
    //专家
    public function expert(){
        $cat_id = trim(I('cat_id'));
        if(empty( $cat_id )){
            $this->error("请稍候再试！");
        }
        $list = M("article_cat")->where(array("parent_id"=>$cat_id))
                                ->field("cat_id,cat_name,parent_id,cat_logo")
                                ->order("sort_order desc")
                                ->select();
        $this->list = $list;
        $this->display(); 
    }

    //专家相关信息
    public function expertInfo(){
        $param = trim(I('id'));
        $OneCatList = M("article_cat")->where(array("parent_id"=>$param))
                                      ->field("cat_id,cat_name,parent_id")
                                      ->order("sort_order desc")
                                      ->select();
        $info = array();
        $article = array();
        foreach ($OneCatList as $key => $value) {
            $res = strpos($value['cat_name'] , '个人简介');
            if( $res === false )
                $article = $value;
            else
                $info = $value;
        }
        $Model = M("article");
        $intro = $Model->where(array("cat_id"=>$info['cat_id']))
                       ->field("content")
                       ->find();
        $articleList =  $Model->where(array("cat_id"=>$article['cat_id']))
                   			  ->field("article_id,cat_id,title,content,publish_time")
                   			  ->limit(30)
                   			  ->select();
        foreach ($articleList as $key => $value) {
            $articleList[$key]['content']= preg_replace ('/\<p\>\<\/p\>|\<p\><br\/>\<\/p\>/','',mb_substr( htmlspecialchars_decode($value['content']),0,130)).'...';
            $articleList[$key]['publish_time' ]= date('Y-m-d',$value['publish_time']);
        }
        $data['experience'] =  htmlspecialchars_decode( $intro['content'] ) ;
        $data['art'] =  $articleList ;
        $this->ajaxReturn( $data );
    }
    //百科
    public function baike(){
        $parent_id = trim(I('cat_id'));
        if(empty( $parent_id )){
            $this->error("请稍候再试！");
        }
        $list = M("article_cat")->where(array("parent_id"=>$parent_id))
                                ->field("cat_id,cat_name,parent_id,cat_logo")
                                ->order("sort_order desc")
                                ->select();
        $this->parent_id = $parent_id;
        if( $list )
            $id = I('get.id') ? I('get.id'):$list[0]['cat_id'];
        else
            $id = I('get.id');
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 10 : $_REQUEST['size'];
        $Model = M("article");
        $data = $Model->where(array("cat_id"=>$id,"is_open"=>'1'))
                      ->field("article_id,cat_id,title,content,FROM_UNIXTIME(publish_time,'%Y-%m-%d') publish_time,thumb")
                      ->order('sort desc')
                      ->page("$p,$size")
                      ->select();  
        $count = $Model->where( array("cat_id"=>$id,"is_open"=>'1') )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出

        $this->id = $id;
        $this->data = $data;
        $this->page = $page;
        $this->list = $list;
        $this->display();
    }
    //国家标准
    public function standard(){
        $parent_id = trim(I('cat_id'));
        $list = M("article_cat")->where(array("parent_id"=>$parent_id))
                                ->field("cat_id,cat_name,parent_id,cat_logo")
                                ->order("sort_order desc")
                                ->select();
        if( $list )
            $id = I('get.id') ? I('get.id'):$list[0]['cat_id'];
        else
            $id = I('get.id');
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 10 : $_REQUEST['size'];
        $Model = M("article");
        $data = $Model->where(array("cat_id"=>$id,"is_open"=>'1'))
                      ->field("article_id,cat_id,title")
                      ->order('sort desc')
                      ->page("$p,$size")
                      ->select();  
        $count = $Model->where( array("cat_id"=>$id,"is_open"=>'1') )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出

        $this->id = $id;
        $this->data = $data;
        $this->page = $page;
        $this->parent_id = $parent_id;
        $this->list = $list;
        $this->display();
    }
}