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

class PublicController extends BaseController {

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

}