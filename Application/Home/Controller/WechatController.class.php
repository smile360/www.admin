<?php
/*+--------------------------------------------------------------------
| @ 果本科技
+----------------------------------------------------------------------
| @ www.applebook.com All Rights Reserved.
+----------------------------------------------------------------------
| @ Author ： xz
+----------------------------------------------------------------------
| @ Date   ： 2016-09-27 10:18
+----------------------------------------------------------------------
| @ wechat 服务器对接
+----------------------------------------------------------------------*/
namespace Home\Controller;
use Think\Controller;
use Common\Normal\Wechat;

define('AppID','wx743d702af3989bc1'); //mp
define('AppSecret','a3b30cdcdafd3f0fb9eebcf2f5cbd8ae');//mp
define("TOKEN", "FENGZAKKA" );//微信平台
// define('openAppID','wxaa4d7c08bbd68c41'); //mp
// define('openAppSecret','9690ce489000f6ad18abe1e87c7be0bb');//mp
class WechatController extends BaseController {

    protected $wechatObj;//微信obj
    protected $openObj;//微信obj
	public function __construct()
    {
        header('Content-Type:text/html;charset=utf-8;');
        parent::__construct();
        $this->wechatObj = new Wechat(array('appid' => AppID, 'appsecret' => AppSecret, 'token' => TOKEN));
        $this->openObj = new Wechat(array('appid' => openAppID, 'appsecret' => openAppSecret, 'token' => TOKEN));
    }

    public function index(){
        $this->display();
    }

    //验证通讯对接
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
 	
    //开放平台 扫码授权 && 授权登录
    public function WechatAuth(){
        $history = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'http://www.zbzixun.com';
        $callback = 'http://www.zbzixun.com/Home/Wechat/Userinfo?type='.$_GET['type'].'&id='.$_GET['id'].'&history='.$history;
        if($_GET['type'] == 'pc'){
            $redirecturl = $this->getOauthRedirect( $callback );
        }else{
            $redirecturl = $this->openObj->getOauthRedirect( $callback );
        }
        header('location:'.$redirecturl);
    }

    /**
     * 开放平台 获取用户的详细信;
     * */
    public function Userinfo()
    {   
        $history = strstr($_GET['history'] , 'login') ? U('User/index') : $_GET['history'];
        $data = array();
        $res = $this->openObj->getOauthAccessToken();
        $refresh_token = $res['refresh_token'];
        $info = $this->openObj->getOauthRefreshToken( $refresh_token );
        $userdata = $this->openObj->getOauthUserinfo( $info['access_token'] , $info['openid'] );
        //检测是否第一次
        $Model = M('users');
        $_user = $Model->where(array('unionid'=>$userdata['unionid']))->find();//unionid开放平台公众号唯一关联
        $data['last_ip'] = get_client_ip(); //获取ip地址
        $data['last_login'] = time();
        $_log['web']= 'pc';
        if( $_user ){
            $Model->where(array('unionid'=>$userdata['unionid']))->setField( $data );
            $_log['info']=json_encode($_user);
            $_log['addtime']=$data['last_login'];
            $_log['flag']= 'old';
            M("user_log")->data( $_log )->add();
            session('user' , $_user);
            if( $_user['mobile']){
                redirect( $history );
            }else{
                redirect(U("User/bind_mobile"));
            }
        }else{
            $data['sex'] = $userdata['sex'] ? $userdata['sex'] : '0';
            $data['nickname'] = $userdata['nickname']; 
            $data['city'] = $userdata['city'];
            $data['province'] = $userdata['province'];
            $data['head_pic'] = (substr($userdata['headimgurl'],0,strlen($userdata['headimgurl'])-1))."96";
            $data['openid'] = $userdata['openid'];
            $data['unionid'] = $userdata['unionid'];
            $data['reg_time'] = time();
            $data['oauth'] = 'wx_pc';
            $UserID = $Model->data( $data )->add();
            $_log['info']=json_encode($data);
            $_log['addtime']=$data['last_login'];
            $_log['flag']= 'new';
            M("user_log")->data( $_log )->add();
            $UserResult = $Model->where(array('user_id'=>$UserID))->find();
            if( $UserResult ){
                session('user' , $UserResult);
                if( $UserResult['mobile'] ){
                    redirect( $history );
                }else{
                    redirect(U("User/bind_mobile"));
                }  
            }else{
                redirect('http://www.zbzixun.com/Home/Wechat/WechatAuth?type=pc&id='.session_id());
            }
        }

    }

    /**
     * oauth 开放平台 授权跳转接口
     * @param string $callback 回调URI
     * @return string
     */
    //开放平台回调是有区别的
    //https://open.weixin.qq.com/connect/qrconnect?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
    public function getOauthRedirect($callback, $state = '', $scope = 'snsapi_login') {
        return 'https://open.weixin.qq.com/connect/qrconnect?appid='.openAppID.'&redirect_uri=' . urlencode( $callback) . '&response_type=code&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
    }

 	//添加菜单栏
	public function CreateMenu()
	{
		$menu = array(
		 	'button' => array(
		 		array('type' => 'view', 'name' => '珠宝官网', 'url' => 'http://www.zbzixun.com/Mobile/Index/index'),
                array('type' => 'view', 'name' => '珠宝商城' , 'url' => 'http://www.zbzixun.com/Mobile/Goods/index.html'),
		 		array('type' => 'view', 'name' => '珠宝培训' , 'url' => 'http://www.zbzixun.com/Mobile/Activity/index.html'),
			)
		);
		$return = $this->wechatObj->createMenu($menu);
        // dump($this->wechatObj);
		dump($return);
	}
    public function test(){
        $res = M('users')->select();
        dump($res);
    }
}
