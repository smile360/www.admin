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
namespace Mobile\Controller;
use Think\AjaxPage;
use Think\Page;

class ConsultController extends MobileBaseController {

    public function index(){
    	$db = M('consult as a');
        //All
    	$consult = $db->where(array('a.is_show' => '0'))
                      ->field('a.*,b.nickname,count(c.consult_id) as collect')
                      ->join('tp_users b ON a.user_id=b.user_id')
                      ->join("LEFT JOIN tp_consult_collect as c ON c.consult_id=a.consult_id")
                      ->group('a.consult_id')
                      ->order('a.createtime desc')
                      ->limit(5)
                      ->select();
    	foreach ($consult as $key => $value) {
    		$consult[$key]['path'] = explode(',', $value['path']);
    	}
    	$this->consult = $consult;
        //solve
        $solve = $db->where(array('a.is_show' => '0',"a.status"=>'1'))
                      ->field('a.*,b.nickname,count(c.consult_id) as collect')
                      ->join('tp_users b ON a.user_id=b.user_id')
                      ->join("LEFT JOIN tp_consult_collect as c ON c.consult_id=a.consult_id")
                      ->group('a.consult_id')
                      ->order('a.createtime desc')
                      ->limit(5)
                      ->select();
        foreach ($solve as $key => $value) {
            $solve[$key]['path'] = explode(',', $value['path']);
        }
        $this->solve = $solve;
        //unsolve
        $unsolve = $db->where(array('a.is_show' => '0',"a.status"=>'0'))
                      ->field('a.*,b.nickname,count(c.consult_id) as collect')
                      ->join('tp_users b ON a.user_id=b.user_id')
                      ->join("LEFT JOIN tp_consult_collect as c ON c.consult_id=a.consult_id")
                      ->group('a.consult_id')
                      ->order('a.createtime desc')
                      ->limit(5)
                      ->select();
        foreach ($unsolve as $key => $value) {
            $unsolve[$key]['path'] = explode(',', $value['path']);
        }
         
        $this->unsolve = $unsolve;
        $this->display();
    }

    //ajaxconsult
    public function ajaxConsult(){
        $start = I('get.start','');
        $status = I('get.type');
        $db = M('consult as a');
        if( $status == '0' ){
            $db->where(array('a.is_show' => '0'));
        }else if( $status == '1' ){
            $db->where(array('a.is_show' => '0',"a.status"=>'1'));
        }else{
            $db->where(array('a.is_show' => '0',"a.status"=>'0'));
        }
        $db->field('a.*,b.nickname,count(c.consult_id) as collect');
        $db->join('tp_users b ON a.user_id=b.user_id');
        $db->join("LEFT JOIN tp_consult_collect as c ON c.consult_id=a.consult_id");
        $db->group('a.consult_id');
        $db->order('a.createtime desc');
        $db->limit("$start,5");
        $data = $db->select();
        foreach ($data as $key => $value) {
            $data[$key]['path'] = explode(',', $value['path']);
            $data[$key]['detail'] = mb_substr( $value['detail'], 0, 100);
            $data[$key]['createtime'] =  date("Y/m/d",$value['createtime']);
        }
        if( $data ){
            $return['code'] = 200; 
            $return['result'] = $data ; 
            $this->ajaxReturn( $return );
        }else{
            $return['code'] = 400;
            $this->ajaxReturn( $return );
        }
    }

    public function detail(){
        $db = M('consult as a');
        $db->where(array('a.consult_id' => $_GET['id']));
        $db->field('a.*,b.nickname,b.head_pic,count(c.consult_id) as collect');
        $db->join('tp_users as b ON a.user_id=b.user_id');
        $db->join("LEFT JOIN tp_consult_collect as c ON c.consult_id=a.consult_id", 'LEFT');
        $db->group('a.consult_id');

        $consult = $db->find();
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
        $where['consult_id']=$_GET['id'];
        $this->count =  M('consultComment')->where( array("consult_id"=>$where['consult_id'] ,"type"=>0) )->count();
        $this->consult = $consult;
        
        $this->display();
    }

    public function LikeThis(){
        $model['consult_id'] = $_POST['consultId'];
        $model['user_id'] = session('user.user_id');
        // if(is_null( $model['user_id'] )){
        //     exit(json_encode(array('errCode'=>48001,'errMsg'=>'用户未进行登录,不能进行此操作!')));
        // }
        M('consultLike')->add($model);
        $sql = "UPDATE `tp_consult` SET `like`=`like`+1 WHERE (`consult_id`='{$_POST['consultId']}') ";
        $res = M('consult')->execute($sql);
        $num = M('consult')->field("like")->where(array("consult_id"=>$model['consult_id']))->find();
        if($res){
            $return['code'] = 200 ;
            $return['num'] = $num['like'] ;
            $this->ajaxReturn( $return );
        }
    }

    public function collectThis(){
        $model['consult_id'] = $_POST['id'];
        $model['tittle'] = $_POST['tittle'];
        $model['user_id'] = session('user.user_id');
        // if(is_null( $model['user_id'] )){
        //     exit(json_encode(array('errCode'=>48001,'errMsg'=>'用户未进行登录,不能进行此操作!')));
        // }
        $Model = M('consultCollect');
        if($Model->where(array("consult_id"=>$model['consult_id'],"user_id"=>$model['user_id']))->find()){
            $return['act'] = 'del';
            $result = $Model->where(array("consult_id"=>$model['consult_id'],"user_id"=>$model['user_id']))->delete();
        }else{
            $return['act'] = 'add';
            $result = $Model->add($model);
        }
        $num = $Model->where(array("consult_id"=>$model['consult_id']))->count();
        if( $result ){
            $return['code'] = 200 ;
            $return['num'] = $num ;
            $this->ajaxReturn( $return );
        }
    }
}