<?php
/*+--------------------------------------------------------------------
| @ 果本科技
+----------------------------------------------------------------------
| @ www.applebook.com All Rights Reserved.
+----------------------------------------------------------------------
| @ Author ： xz
+----------------------------------------------------------------------
| @ Date   ： 2016-12-31 11:44
+----------------------------------------------------------------------
| @ Public 模块
+----------------------------------------------------------------------*/
namespace Home\Controller;
use Think\AjaxPage;
use Think\Page;

class ConsultController extends BaseController {

    public function index(){
    	$db = M('consult as a');
    	/*最新咨询*/
        $keyword = I('seach') ? I('seach') :'' ;
        $where = $keyword ? "is_show=0 AND tittle like '%{$keyword}%'":'is_show=0';
    	$newConsult = $db->where( $where )
                         ->order("createtime desc")
                         ->limit(3)
						 ->select();
    	foreach ($newConsult as $key => $value) {
    		$newConsult[$key]['status'] = $value['status']==='1'?'已解决咨询':'待解决咨询';
    		$newConsult[$key]['path'] = explode(',', $value['path']);
    	}
    	$this->newConsult = $newConsult;

    	/*分页设置*/
    	if(!is_null($_GET['type']) && $_GET['type'] != -1){
            $_like['a.status'] = $_GET['type'];
            $_like['is_show'] = 0;
    		$db->where( $_like );
    	}else{
            $_like['a.tittle'] = array('like',"%{$keyword}%");
            $_like['is_show'] = 0;
            $db->where( $_like );
        }
    	$count = $db->count();
		$Page = new \Think\Page($count,5);

    	$db->limit($Page->firstRow,$Page->listRows);

    	/*tab点击事件*/
		if(!is_null($_GET['type']) && $_GET['type'] != -1){
            $_like['a.status'] = $_GET['type'];
            $_like['is_show'] = 0;
    		$db->where( $_like );
    	}else{
            $_like['a.tittle'] = array('like',"%{$keyword}%");
            $_like['is_show'] = 0;
            $db->where( $_like );
        }
    	$db->field('a.*,b.nickname,count(c.consult_id) as collect');
    	$db->join('tp_users b ON a.user_id=b.user_id');
        $db->join("LEFT JOIN tp_consult_collect as c ON c.consult_id=a.consult_id", 'LEFT');
        $db->order("a.createtime desc");
        $db->group('a.consult_id');

		$consult = $db->select();
    	foreach ($consult as $key => $value) {
    		$consult[$key]['status'] = $value['status']==='1'?'已解决咨询':'待解决咨询';
    		$consult[$key]['path'] = explode(',', $value['path']);
    	}
    	$this->consult = $consult;
    	$this->page = $Page->show();
        $this->keyword = $keyword;
        $this->display();
    }
    public function detail(){
        $db = M('consult as a');
        $db->where(array('a.consult_id' => $_GET['id']));
        $db->field('a.*,b.nickname,b.head_pic,count(c.consult_id) as collect');
        $db->join('tp_users as b ON a.user_id=b.user_id');
        $db->join("LEFT JOIN tp_consult_collect as c ON c.consult_id=a.consult_id", 'LEFT');
        $db->group('a.consult_id');

        $consult = $db->find();

        $consult['status'] = $value['status']==='1'?'已解决':'待解决';
        $consult['path'] = explode(',', $consult['path']);

        $where['consult_id'] = $_GET['id'];
        $where['user_id'] = session('user.user_id');
        if(M('consultLike')->where($where)->find()){
            $this->like = 1;
        }else{
            $this->like = -1;
        }
        if(M('consultCollect')->where($where)->find()){
            $this->collect = 1;
        }else{
            $this->collect = -1;
        }
        
        $this->count = M('consultComment')->where(array('consult_id'=>$_GET['id']))->count();
        $this->consult = $consult;
        $this->display();
    }

    public function LikeThis(){
        $model['consult_id'] = $_POST['consultId'];
        $model['user_id'] = session('user.user_id');
        if(is_null( $model['user_id'] )){
            exit(json_encode(array('errCode'=>48001,'errMsg'=>'用户未进行登录,不能进行此操作!')));
        }
        M('consultLike')->add($model);
        $sql = "UPDATE `tp_consult` SET `like`=`like`+1 WHERE (`consult_id`='{$_POST['consultId']}') ";
        $res = M('consult')->execute($sql);
        echo json_encode($res);
    }

    public function collectThis(){
        $model['consult_id'] = $_POST['consultId'];
        $model['tittle'] = $_POST['tittle'];
        $model['user_id'] = session('user.user_id');
        if(is_null( $model['user_id'] )){
            exit(json_encode(array('errCode'=>48001,'errMsg'=>'用户未进行登录,不能进行此操作!')));
        }
        echo json_encode(M('consultCollect')->add($model));
    }
}