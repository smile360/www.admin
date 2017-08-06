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
namespace Mobile\Controller;
use Home\Logic\UsersLogic;
use Think\Controller;
use Common\Normal\Wechat;

define('AppID','wx743d702af3989bc1'); //mp平台
define('AppSecret','a3b30cdcdafd3f0fb9eebcf2f5cbd8ae');//mp平台 
define("TOKEN", "FENGZAKKA" );//mp平台

class MobileBaseController extends Controller {
    public $user = array();
    public $user_id = 0;
    public $session_id;
    public $weixin_config;
    public $cateTrre = array();
    public $wechatObj;//微信obj
    /*
     * 初始化操作
     */
    public function _initialize() {
        header('Content-Type:text/html;charset=utf-8;');
        $this->wechatObj = new Wechat(array('appid' => AppID, 'appsecret' => AppSecret, 'token' => TOKEN));
        $this->session_id = session_id(); // 当前的 session_id       
        define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
        // 判断当前用户是否手机                
        if(isMobile()){
            cookie('is_mobile','1',3600); 
        }else{
            cookie('is_mobile','0',3600);                 
        }
        //微信浏览器
        if( !session('user')){
            // $UserResult = M('users')->where(array('user_id'=>'2694'))->find();
            // session('user' , $UserResult); 
            $this->auth();
        }      
        // $this->public_assign();
    }
    //授权
    public function auth()
    {
        $callback = 'http://www.fengzakka.cn/Mobile/MobileBase/Userinfo';
        $redirecturl = $this->wechatObj->getOauthRedirect( $callback );
        header('location:' . $redirecturl);
    } 
    public function Userinfo()
    {   
        $data = array();
        $res = $this->wechatObj->getOauthAccessToken();
        $refresh_token = $res['refresh_token'];
        $info = $this->wechatObj->getOauthRefreshToken( $refresh_token );
        $userdata = $this->wechatObj->getOauthUserinfo( $info['access_token'] , $info['openid'] );
        //检测是否第一次
        $Model = M('users');
        $getUserResult = $Model->where(array('openid'=>$userdata['openid']))->find();//unionid开放平台公众号唯一关联
        $data['last_ip'] = get_client_ip(); //获取ip地址
        $_log['web']= 'wx';
        if( $getUserResult ){
            $data['last_login'] = time();
            $Model->where(array('openid'=>$userdata['openid']))->data( $data )->save();
            session('user' , $getUserResult);
            $_log['info']=json_encode($getUserResult);
            $_log['addtime']=$data['last_login'];
            $_log['flag']= 'old';
            M("user_log")->data( $_log )->add();
            redirect('http://www.fengzakka.cn');
        }else{
            $data['sex'] = $userdata['sex'] ? $userdata['sex'] : '0';
            $data['nickname'] = $userdata['nickname']; 
            $data['city'] = $userdata['city'];
            $data['province'] = $userdata['province'];
            $data['head_pic'] = (substr($userdata['headimgurl'],0,strlen($userdata['headimgurl'])-1))."96";
            $data['openid'] = $userdata['openid'];
            $data['unionid'] = $userdata['unionid'];
            $data['reg_time'] = time();
            $data['last_login'] = time();
            $data['oauth'] = 'wx_mobile';
            $UserID = $Model->data( $data )->add();
            $_log['info']=json_encode($data);
            $_log['addtime']=$data['last_login'];
            $_log['flag']= 'new';
            M("user_log")->data( $_log )->add();
            $UserResult = $Model->where(array('user_id'=>$UserID))->find();
            if( $UserResult ){
                session('user' , $UserResult);
                redirect('http://www.fengzakka.cn');  
            }else{
                redirect('http://www.fengzakka.cn/Home/Wechat/WechatAuth?type=pc&id='.session_id());
            }
        }

    }
    /**
     * 保存公告变量到 smarty中 比如 导航 
     */   
    public function public_assign()
    {
        
       $tpshop_config = array();
       $tp_config = M('config')->cache(true,TPSHOP_CACHE_TIME)->select();       
       foreach($tp_config as $k => $v)
       {
       	  if($v['name'] == 'hot_keywords'){
       	  	 $tpshop_config['hot_keywords'] = explode('|', $v['value']);
       	  }       	  
          $tpshop_config[$v['inc_type'].'_'.$v['name']] = $v['value'];
       }                        
       
       $goods_category_tree = get_goods_category_tree();    
       $this->cateTrre = $goods_category_tree;
       $this->assign('goods_category_tree', $goods_category_tree);                     
       $brand_list = M('brand')->cache(true,TPSHOP_CACHE_TIME)->field('id,parent_cat_id,logo,is_hot')->where("parent_cat_id>0")->select();              
       $this->assign('brand_list', $brand_list);
       $this->assign('tpshop_config', $tpshop_config);
    }      

    // 网页授权登录获取 OpendId
    public function GetOpenid()
    {
        if($_SESSION['openid'])
            return $_SESSION['openid'];
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            //$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
            $baseUrl = urlencode($this->get_url());
            $url = $this->__CreateOauthUrlForCode($baseUrl); // 获取 code地址
            Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面
            exit();
        } else {
            // 上面跳转, 这里跳了回来
            //获取code码，以获取openid
            $code = $_GET['code'];
            $data = $this->getOpenidFromMp($code);
            $data2 = $this->GetUserInfo($data['access_token'],$data['openid']);
            $data['nickname'] = $data2['nickname'];
            $data['sex'] = $data2['sex'];
            $data['headimgurl'] = $data2['headimgurl']; 
            $data['subscribe'] = $data2['subscribe'];                         
            $_SESSION['openid'] = $data['openid'];
            return $data;
        }
    }

    /**
     * 获取当前的url 地址
     * @return type
     */
    private function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }    
    
    /**
     *
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     *
     * @return openid
     */
    public function GetOpenidFromMp($code)
    {
         //通过code换取网页授权access_token  和 openid
        $url = $this->__CreateOauthUrlForOpenid($code);       
        $ch = curl_init();//初始化curl        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);         
        $res = curl_exec($ch);//运行curl，结果以jason形式返回            
        $data = json_decode($res,true);//取出openid access_token                
        curl_close($ch);
                
        return $data;
    }
    
    
        /**
     *
     * 通过access_token openid 从工作平台获取UserInfo      
     * @return openid
     */
    public function GetUserInfo($access_token,$openid)
    {         
        // 获取用户 信息
        $url = $this->__CreateOauthUrlForUserinfo($access_token,$openid);
        $ch = curl_init();//初始化curl        
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);//设置超时
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);         
        $res = curl_exec($ch);//运行curl，结果以jason形式返回            
        $data = json_decode($res,true);//取出openid access_token                
        curl_close($ch);
        
        // 获取看看用户是否关注了 你的微信公众号， 再来判断是否提示用户 关注
        $access_token2 = $this->get_access_token();
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token2&openid=$openid";
        $subscribe_info = httpRequest($url,'GET');
        $subscribe_info = json_decode($subscribe_info,true);
        $data['subscribe'] = $subscribe_info['subscribe'];        
        
        return $data;
    }
    
    
    public function get_access_token(){
        //判断是否过了缓存期
        $wechat = M('wx_user')->find();
        $expire_time = $wechat['web_expires'];
        if($expire_time > time()){
           return $wechat['web_access_token'];
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$wechat[appid]}&secret={$wechat[appsecret]}";
        $return = httpRequest($url,'GET');
        $return = json_decode($return,1);
        $web_expires = time() + 7000; // 提前200秒过期
        M('wx_user')->where(array('id'=>$wechat['id']))->save(array('web_access_token'=>$return['access_token'],'web_expires'=>$web_expires));
        return $return['access_token'];
    }    

    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->weixin_config['appid'];
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
//        $urlObj["scope"] = "snsapi_base";
        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }

    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     *
     * @return 请求的url
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = $this->weixin_config['appid'];
        $urlObj["secret"] = $this->weixin_config['appsecret'];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }

    /**
     *
     * 构造获取拉取用户信息(需scope为 snsapi_userinfo)的url地址     
     * @return 请求的url
     */
    private function __CreateOauthUrlForUserinfo($access_token,$openid)
    {
        $urlObj["access_token"] = $access_token;
        $urlObj["openid"] = $openid;
        $urlObj["lang"] = 'zh_CN';        
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/userinfo?".$bizString;                    
    }    
    
    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    //nav
    public function _nav()
    {
        
       $article_cat = M("article_cat")->where(array("show_in_nav"=>1,"parent_id"=>0))->field("cat_name,cat_id")->select();
       $nav = array(
                array(
                  "action"=>"Article/news",
                  "name"=>"最新动态"
                  ),
              );
        $catNav = array();
        foreach ($article_cat as $key => $value) {
          if( strstr ($value['cat_name'] , '珠宝学院')){
            $catNav = array(
              "action"=>"Article/college",
              "name"=>$value['cat_name'],
              "id"=>$value['cat_id'],
              );
          }elseif(strstr  ($value['cat_name'] ,'胡博士同学会' )){
            $catNav = array(
              "action"=>"Article/college",
              "name"=>$value['cat_name'],
              "id"=>$value['cat_id'],
              );
          }elseif( strstr  ($value['cat_name'] , '专家专栏' )){
            $catNav = array(
              "action"=>"Article/expert",
              "name"=>$value['cat_name'],
              "id"=>$value['cat_id'],
              );
          }elseif( strstr  ($value['cat_name'] , '珠宝百科' )){
            $catNav = array(
              "action"=>"Article/baike",
              "name"=>$value['cat_name'],
              "id"=>$value['cat_id'],
              );
          }elseif( strstr  ($value['cat_name'] , '国家标准' )){
            $catNav = array(
              "action"=>"Article/standard",
              "name"=>$value['cat_name'],
              "id"=>$value['cat_id'],
              );
          }
          array_push( $nav ,$catNav );
        }
        $this->nav = $nav;
    }  

}