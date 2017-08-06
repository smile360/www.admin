<?php
/**
 * User Model
 * Author: xz
 * Date: 2016-12-29
 */

namespace Home\Model;

use Think\Model;

/**
 * @package Home\Model
 */
class UsersModel extends Model
{
   /**
    * date : 2016/11/25 14:28
    * auth : XZ
    * @param $username 
    * @param $password
    * @param $email
    * @return $array
    **/
    public function regverify( $username , $phone , $password , $email ){
        $is_validated = 0 ;
        if( $phone ){
            $is_validated = 1;
            $map['mobile_validated'] = 1;
            $map['nickname'] = $phone;
            $map['mobile'] = $phone;
            $map['account'] = $username;
            $map['email'] = $email; //邮箱注册
        }
        if($is_validated != 1)
            return array('status'=>-1,'msg'=>'请用手机号或邮箱注册');

        if(!$username || !$password)
            return array('status'=>-1,'msg'=>'请输入用户名或密码');
        //验证是否存在用户名
        $Account = M('users')->where(array("account"=>$username))->count();
        $mobile = M('users')->where(array("mobile"=>$phone))->count();
        if($Account || $mobile )
            return array('status'=>-1,'msg'=>'账号已存在');
        $map['password'] = encrypt( $password );
        $map['reg_time'] = time();
        $map['token'] = md5(time().mt_rand(1,99999));
        $user_id = M('users')->add($map);
        if(!$user_id)        
            return array('status'=>-1,'msg'=>'注册失败'); 
        $user = M('users')->where("user_id = {$user_id}")->find();
        return array('status'=>1,'msg'=>'注册成功','result'=>$user); 
    }

    /**
     * 查询用户校验列表
     */
    public function codeVerify( $phone , $code ) {                
        if(empty($code)){
            return false;
        }      
        $AuthCode = M("SendCode");   
        $list = $AuthCode->where( array("phone"=>$phone,"code"=>$code) )->order('code_id desc')->find();
        return $list;
    }

    //核实手机号用户
    public function getUser( $phone ){
        if(empty( $phone )){
            return false;
        }
        $result = M('users')->where(array("mobile"=>$phone))->count();
        return $result;
    }

    //绑定手机号
    public function bindMobile( $phone , $user_id ){
        if(empty($phone) || empty($user_id)){
            return false;
        }
        $result = M('users')->where(array('user_id'=>$user_id))->setField(array('mobile'=>$phone,'mobile_validated'=>'1'));
        return $result;
    }
}