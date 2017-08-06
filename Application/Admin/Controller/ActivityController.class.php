<?php
/*+--------------------------------------------------------------------
| @ 果本科技
+----------------------------------------------------------------------
| @ www.applebook.com All Rights Reserved.
+----------------------------------------------------------------------
| @ Author ： xz
+----------------------------------------------------------------------
| @ Date   ： 2016-11-15 14:44
+----------------------------------------------------------------------
| @ 后台活动模块
+----------------------------------------------------------------------*/
namespace Admin\Controller;
use Think\AjaxPage;
use Think\Model;
use Think\Page;
use Think\Log;

class ActivityController extends BaseController 
{ 
    //活动添加，编辑
	public function activity(){
		if(IS_POST){
    		$data = I('post.');
            $data['activity_time'] = strtotime($data['activity_time']);
    		$data['start_time'] = strtotime($data['start_time']);
            // $data['end_time'] = strtotime($data['end_time']);
    		$data['admin_id'] = session('admin_id');
    		if(empty($data['activity_id'])){
    			$data['add_time'] = time();
    			$r = M('Activity')->add( $data );
    			adminLog("管理员添加新活动 ".$data['title']);
    		}else{
    			$r = M('Activity')->where("activity_id=".$data['activity_id'])->save($data);
    		}
    		if($r){
    			$this->success('课程活动成功',U('Activity/activity_list'));
    			exit;
    		}else{
    			$this->error('课程活动失败',U('Activity/activity',array("activity_id"=>$data['activity_id'])));
    		}
    	}
    	$id = I('get.activity_id');
        $info['activity_time'] = date('Y-m-d H:i:s');
        $info['start_time'] = date('Y-m-d H:i:s');
    	// $info['end_time'] = date('Y-m-d 23:59:59',time()+3600*24*60);
    	if($id > 0){
    		$info = M('Activity')->where("activity_id=$id")->find();
    		$info['start_time'] = date('Y-m-d H:i',$info['start_time']);
    		// $info['end_time'] = date('Y-m-d H:i',$info['end_time']);
    	}
    	$this->assign('info',$info);
    	$this->assign('min_date',date('Y-m-d'));
    	$this->display();
	}
    //活动列表
	public function activity_list(){
        $Article =  M('Activity'); 
        $res = $list = array();
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        $where = "is_del=1";
        $keywords = trim(I('keywords'));
        if($keywords){
            $where = "title like '%$keywords%'";
        }
        $activity_id = I('activity_id',0);
        $activity_id && $where.="and activity_id = $activity_id ";
        $res = $Article->where($where)->order('activity_id desc')->page("$p,$size")->select();
        $count = $Article->where($where)->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出
        if( $res ){
            foreach ($res as $key => $value) {
                $value['activity_time'] = date('Y-m-d ',$value['activity_time']);                
                $value['add_time'] = date('Y-m-d ',$value['add_time']);                
                $value['start_time'] = date('Y-m-d ',$value['start_time']);                
                // $value['end_time'] = date('Y-m-d ',$value['end_time']);                
                $value['content'] = strip_tags(htmlspecialchars_decode( $value['content']));                               
                $list[] = $value;
            }
        }
        $this->assign('keywords',$keywords);
        $this->assign('activity_id',$activity_id);
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
		$this->display();
	}
    //删除活动
    public function activityDel(){
        $data = I('post.');
        if($data['act'] == 'del'){
            $r = D('Activity')->where('activity_id='.$data['activity_id'])->setField('is_del','2');
            if($r) exit(json_encode(1));        
        }
    }
    /**
     * ajax更新活动排序
     */
    public function changeTableSort(){   
        $id_name = I('id_name'); // 表主键id名
        $id_value = I('id_value'); // 表主键id值
        $field  = I('field'); // 修改哪个字段
        $value  = I('value'); // 修改字段值 
        $Model = M('Activity');             
        $res = $Model->where("$id_name = $id_value")->setField(array($field=>$value)); // 根据条件保存修改的数据
        if( $res ){
            $this->ajaxReturn(array('code'=>200));
        }else{
            $reslut = $Model->where(array($id_name=>$id_value))->getField('sort');
            $this->ajaxReturn( $reslut );
        }
    }
    //申请列表
    public function activity_applicant_list(){
        $Article =  M('ActivityEntry as a'); 
        $res = $list = array();
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        $where = "a.is_del=1";
        $keywords = trim(I('keywords'));
        if($keywords){
            $where = "a.name like '%$keywords%' or a.phone like '%$keywords%'";
        }
        $entry_id = I('entry_id',0);
        $entry_id && $where.=" and a.entry_id = $entry_id ";
        $res = $Article->where($where)
                       ->field("a.*,b.title")
                       ->join("tp_activity as b ON a.activity_id=b.activity_id")
                       ->order('a.add_time desc')
                       ->page("$p,$size")
                       ->select();
        $count = $Article->where($where)->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出
        if( $res ){
            foreach ($res as $key => $value) {
                $value['add_time'] = date('Y-m-d',$value['add_time']);                
                $value['sex'] = $value['sex'] == '0' ? '男':'女';                
                $value['is_status'] = $value['is_status'] == '0' ? '申请中': ($value['is_status'] == '1' ? '申请通过':'拒绝');                
                $value['check_time'] = $value['check_time'] ? date('Y-m-d',$value['check_time']) : '';                                         
                $list[] = $value;
            }
        }
        $this->assign('keywords',$keywords);
        $this->assign('list',$list);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        $this->display();
    }
    //审核
    public function checkApplicant(){
        if(IS_POST){
            $is_status = trim(I('post.is_status'));
            $entry_id = trim(I('post.entry_id'));
            $activity_id = trim(I('post.activity_id'));
            $check_time = time();
            $reslut = M('ActivityEntry')->where(array('entry_id'=>$entry_id,'activity_id'=>$activity_id))->setField(array('is_status'=>$is_status,'check_time'=>$check_time,'admin_id'=>session('admin_id')));
            
            if( $reslut ){
                $res['check_time'] = date('Y-m-d',$check_time);
                $res['is_status'] = $is_status == 1 ? '审核通过':'拒绝';
                $res['code'] = 200;
                $this->ajaxReturn( $res );
            }else{
                $this->ajaxReturn(array('code'=>400));
            }
        }else{
            $this->ajaxReturn(array('code'=>500));
        }
    }
    //删除申请
    public function checkDle(){
        $data = I('post.');
        if($data['act'] == 'del'){
            $r = D('ActivityEntry')->where('entry_id='.$data['entry_id'])->setField('is_del','2');
            if($r) exit(json_encode(1));        
        }
    }
    //活动评论
    public function activity_comment(){
        $Activity =  M('ActivityComment as a'); 
        $p = empty($_REQUEST['p']) ? 1 : $_REQUEST['p'];
        $size = empty($_REQUEST['size']) ? 20 : $_REQUEST['size'];
        $keywords['username'] = I('nickname','','trim');
        $keywords['content'] = I('content','','trim');
        $where =' a.parent_id = 0';
        if( $keywords['username'] ){
            $where .= " AND a.username like '%{$keywords['username']}%'";
        }
        if( $keywords['content'] ){
            $where .= " AND a.content like '%{$keywords['content']}%'";
        } 
        $data = $Activity->where( $where )
                         ->field("a.*,b.title")
                         ->join("tp_activity as b ON a.activity_id=b.activity_id")
                         ->order('a.add_time desc')
                         ->page("$p,$size")
                         ->select();
        $count = $Activity->where( $where )->count();// 查询满足要求的总记录数
        $pager = new \Think\Page($count,$size);// 实例化分页类 传入总记录数和每页显示的记录数
        $page = $pager->show();//分页显示输出
        $this->assign('keywords',$keywords);
        $this->assign('list',$data);// 赋值数据集
        $this->assign('page',$page);// 赋值分页输出
        $this->display();
    }
    //评论详情
    public function detail(){
         if(IS_POST){
            $id = I('get.id');
            $admin = M("Admin")->where(array("admin_id"=>session("admin_id")))->field('user_name,admin_id')->find();
            $add['parent_id'] = I('post.parent_id');
            $add['content'] = I('post.content');
            $add['activity_id'] = I('post.activity_id');
            $add['use_user_id'] = I('post.use_user_id');
            $add['add_time'] = time();
            $add['username'] = $admin['user_name'];
            $add['user_id'] = $admin['admin_id'];
            $add['ip_address'] = get_client_ip();
            $row =  M('ActivityComment')->add($add);
            if($row){
                $this->success('回复成功');
            }else{
                $this->error('回复失败');
            }
            exit;
        }else{
            $id = I('get.id');
            $res = M('ActivityComment as a')->where(array('comment_id'=>$id))
                                            ->field("a.*,b.head_pic")
                                            ->join("tp_users as b ON a.user_id=b.user_id")
                                            ->find();
            if(!$res){
                exit($this->error('不存在该评论'));
            }
            // dump($res);
            $reply = M('ActivityComment')->where(array('parent_id'=>$id))->select(); // 评论回复列表
            $this->assign('comment',$res);
            $this->assign('reply',$reply);
            $this->display();
        }
        
    }

    public function del(){
        $id = I('get.id');
        $row = M('ActivityComment')->where(array('comment_id'=>$id))->save(array('is_show'=>0));
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
        $where = "comment_id IN ({$selected_id})";
        if($type == 'del'){
            //删除回复
            $where .= " OR parent_id IN ({$selected_id})";
            $row = M('ActivityComment')->where($where)->delete();
        //  exit(M()->getLastSql());
        }
        if($type == 'show'){
            $row = M('ActivityComment')->where($where)->save(array('is_show'=>1));
        }
        if($type == 'hide'){
            $row = M('ActivityComment')->where($where)->save(array('is_show'=>0));
        }
        if(!$row)
            $this->error('操作失败');
        $this->success('操作成功');

    }
}
