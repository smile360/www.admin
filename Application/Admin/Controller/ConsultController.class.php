<?php
/*+--------------------------------------------------------------------
| @ 果本科技
+----------------------------------------------------------------------
| @ www.applebook.com All Rights Reserved.
+----------------------------------------------------------------------
| @ Author ： xz
+----------------------------------------------------------------------
| @ Date   ： 2017-1-11 13.00
+----------------------------------------------------------------------
| @ 后台咨询列表模块
+----------------------------------------------------------------------*/
namespace Admin\Controller;
use Think\AjaxPage;
use Think\Model;
use Think\Page;
use Think\Log;

class ConsultController extends BaseController 
{ 
    
    public function __construct(){
        parent::__construct();
    }
    //咨询列表
	public function index(){
        $Activity =  M('Consult as a'); 
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        $keywords = I('keywords','','trim');
        $where ='is_show=0';
        if( $keywords ){
            $where .= " AND a.tittle like '%{$keywords}%'";
            $as = " OR b.nickname like '%{$keywords}%'";
        } 
           
        $data = $Activity->where( $where )
                         ->field("a.*,b.nickname")
                         ->join("tp_users as b ON a.user_id=b.user_id $as")
                         ->order('a.createtime desc')
                         ->page("$p,$size")
                         ->select();
        foreach ($data as $key => $value) {
            // $data[$key]['status'] = $value['status']==='1'?'已解决咨询':'待解决咨询';
            $data[$key]['path'] = explode(',', $value['path']);
        }
        $count = $Activity->where( $where )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出
        $this->assign('keywords',$keywords);
        $this->assign('list',$data);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        $this->display(); 
	}
    /*_________咨询详情____________*/
    public function detail(){
        $id = I('id','','trim');
        if(empty($id))
            $this->error('操作有误，请稍候再试~'); 
        $data = M('Consult a')->where(array("consult_id"=>$id))
                              ->field("a.*,b.nickname,head_pic")
                              ->join("tp_users b ON a.user_id=b.user_id")
                              ->find();
                              // echo M('Consult a')->_sql();exit;
        $data['path'] = explode(',', $data['path']);
        //管理回复相关message
        $comment = M("consult_comment")->where(array("consult_id"=>$id,"is_show"=>'0',"type"=>1))->select();
        $this->comment = $comment;
        $this->data = $data; 
        $this->display();
    }

    //管理员评论回复
    public function adminReply(){
        $admin = M("Admin")->where(array("admin_id"=>session("admin_id")))
                           ->field('user_name,admin_id')
                           ->find();
        $data['parent_name'] = '@'.I('post.nickname','','trim');
        $data['parent_id'] = I('post.parent_id','','trim');
        $data['consult_id'] = I('post.consult_id','','trim');
        $data['comment'] = I('post.content','','trim');
        $data['user_id'] = $admin['admin_id'];
        $data['nickname'] = $admin['user_name'];
        $data['comment_id'] = uniqid();
        $data['createtime'] = time();
        $data['type'] = 1;
        $data['ip_address'] = get_client_ip();
        $result = M('consult_comment')->data( $data )->add();
        if( $result ){
            $this->success("回复成功",U('detail',array('id'=>$data['consult_id'])));
        }else{
            $this->error("回复失败");
        }
    }
    /*_____确认_____*/
    public function confirm(){
        $consult_id = I('id',0,'trim');
        $reslut = M("Consult")->where(array('consult_id'=>$consult_id))->setField(array('status'=>'1'));
        if($reslut)
            exit(json_encode(array('code'=>200)));
        else
            exit(json_encode(array('code'=>400)));
    }

    //活动评论
    public function comment(){
        $Activity =  M('consult_comment a'); 
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        $keywords['username'] = I('nickname','','trim');
        $keywords['content'] = I('content','','trim');
        $where ='a.parent_id = 0';
        if( $keywords['username'] ){
            $where .= " AND a.nickname like '%{$keywords['username']}%' OR a.parent_name like '%{$keywords['username']}%'";
        }
        if( $keywords['content'] ){
            $where .= " AND a.comment like '%{$keywords['content']}%'";
        } 
        $data = $Activity->where( $where )
                         ->field("a.*,b.tittle")
                         ->join("tp_consult b ON a.consult_id=b.consult_id")
                         ->order('a.createtime desc')
                         ->page("$p,$size")
                         ->select();
                         // dump( $data );
        $count = $Activity->where( $where )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出
        // dump($data);
        $this->assign('keywords',$keywords);
        $this->assign('data',$data);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        $this->display();
    }
    //评论详情
    public function comment_detail(){
         if(IS_POST){
            $id = I('get.id');
            $admin = M("Admin")->where(array("admin_id"=>session("admin_id")))->field('user_name,admin_id')->find();
            $add['parent_id'] = I('post.parent_id');
            $add['comment'] = I('post.content');
            $add['activity_id'] = I('post.activity_id');
            $add['use_user_id'] = I('post.use_user_id');
            $add['add_time'] = time();
            $add['nickname'] = $admin['user_name'];
            $add['user_id'] = $admin['admin_id'];
            // $add['parent_name'] = $admin['admin_id'];
            $add['ip_address'] = get_client_ip();
            $row =  M('consult_comment')->add($add);
            if($row){
                $this->success('回复成功');
            }else{
                $this->error('回复失败');
            }
            exit;
        }else{
            $id = I('get.id');
            $res = M('consult_comment as a')->where(array('comment_id'=>$id))
                                            ->field("a.*,b.head_pic")
                                            ->join("tp_users as b ON a.user_id=b.user_id")
                                            ->find();
            if(!$res){
                exit($this->error('不存在该评论'));
            }
            // dump($res);
            $reply = M('consult_comment a')->where(array('a.parent_id'=>$id))
                                           ->field("a.*,b.head_pic")
                                           ->join("tp_users as b ON a.user_id=b.user_id")
                                           ->select(); // 评论回复列表
            $this->assign('comment',$res);
            $this->assign('reply',$reply);
            $this->display();
        }
        
    }
    //
    public function del(){
        $id = I('post.id');
        $row = M('consult')->where(array('consult_id'=>$id))->setField(array('is_show'=>'1'));
        if( $row ){
            exit('200');
        }else{
            exit('400');
        }
    }

    public function commentDel(){
        $id = I('id');
        $row = M('consult_comment')->where(array('comment_id'=>$id))->delete();
        if( $row ){
            $this->success('删除评论成功');
        }else{
            $this->error('删除评论失败');
        }
    }

    public function op(){
        $type = I('post.type');
        $selected_id = I('post.selected');
        if(!in_array($type,array('del','show','hide')) || !$selected_id)
            $this->error('非法操作');
        $where = "comment_id IN ({$selected_id})";
        if($type == 'del'){
            //删除回复
            $where .= " OR parent_id IN ({$selected_id})";
            $row = M('ActivityComment')->where($where)->delete();
        //  exit(M()->getLastSql());
        }
        if($type == 'show'){
            $row = M('ActivityComment')->where($where)->save(array('is_show'=>0));
        }
        if($type == 'hide'){
            $row = M('ActivityComment')->where($where)->save(array('is_show'=>1));
        }
        if(!$row)
            $this->error('操作失败');
        $this->success('操作成功');

    }
}
