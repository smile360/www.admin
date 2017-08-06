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
use Think\Model;
class PublicController extends MobileBaseController {

	//找回密码
    public function findPwd(){      
        $this->display();
    }

    //重置密码
    public function resetPwd(){
    	if(IS_POST){
    		if(!session("?phone")){
    			$this->error('请验证手机号',U('Public/findPwd'),3);
    			exit();
    		}
    		$passwordOne = trim(I('post.passwordOne'));
    		$passwordTwe = trim(I('post.passwordTwe'));
    		if( $passwordOne != $passwordTwe ){
    			$this->error("两次密码不一至，请重新填写");
    			exit();
    		}
    		$result = M("Users")->where(array("mobile"=>session("phone")))
    							->setField(array("password"=>encrypt($passwordOne)));
    		if( !$result ){
    			$this->error("重置密码失败，请稍候再试！");
    			exit();
    		}
    		session("phone",null);
    		$this->success('重置密码成功！',U('User/login'),3);
    	}else{
    		$this->display();
    	}
    }

    //联系我们
    public function contact(){
        $this->display();
    }

    //search
    public function search(){
    	if(IS_POST){
	        $keyword = I('search') ? I('search') :'' ;
	        $start = I('start') ? I('start')/2 : 0 ;
	        $limit = "$start,10";
	        $Model = new Model();
	        $SQL = "(SELECT article_id AS ID,title AS TITLE,thumb AS IMG,content AS detail,publish_time AS TIME,'article' AS TYPE 
	        FROM tp_article WHERE title LIKE '%{$keyword}%' OR content LIKE '%{$keyword}%' ORDER BY sort DESC LIMIT $limit)
	UNION
	(SELECT consult_id AS ID,tittle AS TITLE,substring_index(path,',',1) AS IMG,detail AS detail ,createtime AS TIME,'consult' AS TYPE 
	        FROM tp_consult WHERE tittle LIKE '%{$keyword}%' OR detail LIKE '%{$keyword}%' ORDER BY createtime DESC LIMIT $limit)";
	        $result = $Model->query( $SQL );  
	        $data = $this->eachData( $result );
	        if(IS_AJAX){
	            if( $data ){
	                $return['code'] = 200;
	                $return['result'] = $data ;
	            }else{
	                $array['code'] = 400;
	            }
	            $this->ajaxReturn( $return );
	        }
	    }
        $this->data = $data ;
        $this->keyword = $keyword;
        $this->display();
    }
    function eachData( $data){
        $result = false;
        if( $data ){
            foreach ($data as $key => $value) {
                $data[$key]['time'] = date('Y-m-d H:i',$value['time']);
                if($value['type'] == 'article'){
                    $str = preg_replace("/<img\s*src=(\"|\')(.*?)\\1[^>]*>/is",'',htmlspecialchars_decode($value['detail']));
                    $data[$key]['detail'] = mb_substr(preg_replace ('/\<p\>|\<\/p\>|\<p\>|<br\/>|\<\/p\>/','', $str),0,50).'...';
                }else{
                    $data[$key]['detail'] = mb_substr( $value['detail'],0,50).'...';
                }
            }
            $result = $data;
        }
        return $result;
    }
}