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
 * Author: JY
 * Date: 2015-09-23
 */

namespace Home\Controller;
use Think\Log;
use Think\Controller;
import('Vendor.AliDayu.TopSdk');
define('Appkey', '23584113');    //大鱼Appkey 23584113
define('AppSecret', 'adcea9b09daa14505cfc40bdd2910bdb'); //大鱼AppSecret adcea9b09daa14505cfc40bdd2910bdb
class ApiController extends Controller {
    
    /**
    * 发送短信
    * @param  type  定义模板
    * @param  phone 发送号码
    * @return code  相应返回码
    **/
    public function SendCode( $phone , $type=null ){
        if(empty($phone)){
            $return['msg'] = '短信发送失败,请稍后再试';
            $this->ajaxReturn( $return ); 
        }
        if($type != 'reg'){
            $SendModel = 'SMS_36945016';
        }else{
            $SendModel = 'SMS_37040064';
        }
        if( $phone ){ 
            $findUser = $this->DetectionUser( $phone );
            if($type == 'reg' && $findUser){
                $return['msg'] = '此手机号码已经被注册';
                $this->ajaxReturn($return); 
            }else if($type == 'findpwd' && !$findUser){
                $return['msg'] = '此手机号码未注册';
                $this->ajaxReturn($return); 
            }else if( ($type == 'resetphone' || $type == 'bind') && $findUser){
                $return['msg'] = '此手机已绑定用户';
                $this->ajaxReturn($return);
            }
            $data['phone'] = $phone; 
            $data['code'] = rand(1000,9999);
            $data['ip'] =   get_client_ip();
            $data['send_time'] = time();
            $data['disable_time'] = time()+60*10;
            $data['send_model'] = $SendModel;
            $DetectionResult = $this->DetectionSmsSpeed( $data['phone'] , $data['ip']);
            if( $DetectionResult ){
                $result = $this->CodeLogAdd( $data );
                if( $result ){
                    /**验证短信是否发送成功***/
                    $SendStatus = $this->SendMsg( $data['phone'] , $data['code'] , $SendModel );
                    if( $SendStatus['result']['success'] ){
                        M('SendCode')->where(array("phone"=>$data['phone'],'code'=>$data['code']))->setField("send_statua",'1');
                        Log::write(date('Y-m-d H:i:s')."发送短信至 : ".$data['phone']."状态:成功！");//写个log日志
                        $return['msg'] = 200;
                        $this->ajaxReturn( $return );
                    }else{
                        $return['msg'] = '短信发送失败,请稍后再试';
                        $this->ajaxReturn( $return );
                    }
                }else{
                    $return['msg'] = '短信发送失败,请稍后再试';
                    $this->ajaxReturn( $return );
                }
            }else{
                $return['msg'] = '此手机号码当天获取信息次数已超过10次';
                $this->ajaxReturn( $return ); 
            }
        }else{
            $return['msg'] = '短信发送失败,请稍后再试';
            $this->ajaxReturn( $return );
        }

    }

    /** 
    * 短信code添加log
    * @param 1 $data array;
    * @return $result bool;
    **/
    public function CodeLogAdd( $data ){
        if(empty($data)){
            return false;
        }
        $result = M('send_code')->data( $data )->add();
        return $result;
    }
    //匹配验证手机验证码
    public function codeVerify(){
        $phone = trim( I('post.phone'));
        $code = trim( I('post.code'));
        $findpwd = trim( I('post.findpwd'));
        if(empty( $phone ) || empty( $code )){
            exit(json_encode(array("msg"=>"请输入正确手机号或验证码")));
        }
        $Model = D('Users');
        //查下是否存在此用户
        $user = $Model->getUser( $phone );
        if( $findpwd == 'findpwd'){
            if( !$user ){
               exit(json_encode(array("msg"=>"此手机号码未注册"))); 
            }
            session('phone',$phone);
        }else if($findpwd == 'resetphone'){
            if( $user ){
               exit(json_encode(array("msg"=>"此手机已绑定用户")));  
            }
        }
        //校验验证码
        $codeVerify = $Model->codeVerify( $phone , $code );
        if( $codeVerify ){
            if ( time() > $codeVerify['disable_time'] ){
                exit(json_encode(array("msg"=>"验证码已失效!")));
            }else if ( $code != $codeVerify['code'] ){
                exit(json_encode(array("msg"=>"验证码错误!")));
            }else{
                if( $findpwd == 'resetphone'){
                   $resetphone = $Model->where(array("user_id"=>session('user.user_id'),"mobile"=>session('phone')))->setField("mobile",$phone);
                   if( $resetphone ){
                        session('phone',null);
                        exit(json_encode(array("msg"=>"200")));   
                    }
                    exit(json_encode(array("msg"=>"绑定手机修改失败")));
                }else{
                    exit(json_encode(array("msg"=>"200")));
                }
            }
        }
        exit(json_encode(array("msg"=>"请输入正确手机号或验证码")));
    }

    /** 
    * 检测手机号是否已注册
    * @param 1 $phone string;
    * @return $phone string;
    **/
    public function DetectionUser( $phone ){
        if(empty($phone)){
            return false;
        }
        $result = M('Users')->where(array('mobile'=>$phone))->field('mobile')->find();
        return $result;
    }

    public function SendMsg( $phone , $code , $SendModel ){
        if(empty($phone) || empty($code)){
            return false;
        }
        if( $SendModel == 'SMS_37040064' ){
            $setSmsParam = "{\"code\":\"{$code}\",\"product\":\"【深职院】\"}";
        }else{
            $setSmsParam = "{\"name\":\"【深职院】\",\"code\":\"{$code}\"}";
        }
        $c = new \TopClient;
        $c->appkey = Appkey;
        $c->secretKey = AppSecret;
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("验证码");
        $req->setSmsParam( $setSmsParam );
        $req->setRecNum( $phone );
        $req->setSmsTemplateCode( $SendModel );
        $resp = $c->execute($req);
        $success = $this->toArray($resp);
        return $success;
    }
    //短信结果objSimpleXMLElement转成数组
    public static function toArray($simplexml_obj, $array_tags=array(), $strip_white=1)
    {    
        if( $simplexml_obj )
        {
            if( count($simplexml_obj)==0 )
                return $strip_white?trim((string)$simplexml_obj):(string)$simplexml_obj;
       
            $attr = array();
            foreach ($simplexml_obj as $k=>$val) {
                if( !empty($array_tags) && in_array($k, $array_tags) ) {
                    $attr[] = self::toArray($val, $array_tags, $strip_white);
                }else{
                    $attr[$k] = self::toArray($val, $array_tags, $strip_white);
                }
            }
            return $attr;
        }
           
        return false;
    }
    /** 
    * 检测短信发送频率
    * @param 1 $mobile string;
    * @param 2 $ip string;
    * @return $result bool;
    **/
    public function DetectionSmsSpeed( $mobile , $ip ){
        if(empty($mobile) || empty($ip)){
            return false;
        }
        $Model = M('SendCode');
        $now = time();
        //60秒限制
        $sixWhere = "phone=$mobile AND send_time > $now-60 AND send_statua='1'";
        $lastMinuteCount = $Model->where( $sixWhere )->order('code_id desc')->count();

        //单用户24小时不超过5条
        $userWhere = "phone=$mobile AND send_time > $now-60*60*24 AND send_statua='1'";
        $userCount = $Model->where($userWhere)->order('code_id desc')->count();

        //单IP触发24小时不超过20条
        $ipWhere = "ip='$ip' AND send_time > $now-60*60*24 AND send_statua='1'";
        $ipCount = $Model->where( $ipWhere )->order('code_id desc')->count();

        if($lastMinuteCount>0 || $userCount>=10 || $ipCount>=20){
            return false;
        }
        return true;        
    }
    

     /*
     * 获取地区
     */
    public function getRegion(){
        $parent_id = I('get.parent_id');
        $selected = I('get.selected',0);        
        $data = M('region')->where("parent_id=$parent_id")->select();
        $html = '';
        if($data){
            foreach($data as $h){
                if($h['id'] == $selected){
                    $html .= "<option value='{$h['id']}' selected>{$h['name']}</option>";
                }
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        echo $html;
    }
    

    public function getTwon(){
        $parent_id = I('get.parent_id');
        $data = M('region')->where("parent_id=$parent_id")->select();
        $html = '';
        if($data){
            foreach($data as $h){
                $html .= "<option value='{$h['id']}'>{$h['name']}</option>";
            }
        }
        if(empty($html)){
            echo '0';
        }else{
            echo $html;
        }
    }
    
    /*
     * 获取地区
     */
    public function get_category(){
        $parent_id = I('get.parent_id'); // 商品分类 父id  
            $list = M('goods_category')->where("parent_id = $parent_id")->select();
        
        foreach($list as $k => $v)
            $html .= "<option value='{$v['id']}'>{$v['name']}</option>";        
        exit($html);
    }  
    
    /**
     * 检测手机号是否已经存在
     */
    public function issetMobile()
    {
      $mobile = I("mobile",'0');  
      $users = M('users')->where("mobile = '$mobile'")->find();
      if($users)
          exit ('1');
      else 
          exit ('0');      
    }

    public function issetMobileOrEmail()
    {
        $mobile = I("mobile",'0');
        $user_where['email'] = $mobile;
        $user_where['mobile'] = $mobile;
        $user_where['_logic'] = 'OR';
        $users = M('users')->where($user_where)->find();
        if($users)
            exit ('1');
        else
            exit ('0');
    }
}