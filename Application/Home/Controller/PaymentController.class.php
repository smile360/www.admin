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
namespace Home\Controller;
class PaymentController extends BaseController {
    
    public $payment; //  具体的支付类
    public $pay_code; //  具体的支付code
 
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();                                                  
        // tpshop 订单支付提交
        $pay_radio = $_REQUEST['pay_radio'];
        if(!empty($pay_radio)) 
        {                         
            $pay_radio = parse_url_param($pay_radio);
            $this->pay_code = $pay_radio['pay_code']; // 支付 code
        }
        else // 第三方 支付商返回
        {            
            $_GET = I('get.');            
            //file_put_contents('./a.html',$_GET,FILE_APPEND);    
            $this->pay_code = I('get.pay_code');
            unset($_GET['pay_code']); // 用完之后删除, 以免进入签名判断里面去 导致错误
        }                        
        //获取通知的数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];               
        // 导入具体的支付类文件                
        // include_once  "plugins/payment/{$this->pay_code}/{$this->pay_code}.class.php"; // D:\wamp\www\svn_tpshop\www\plugins\payment\alipay\alipayPayment.class.php                       
        // $code = '\\'.$this->pay_code; // \alipay
        // $this->payment = new $code();
    }
   
    /**
     * tpshop 提交支付方式
     */
    public function getCode(){        
            C('TOKEN_ON',false); // 关闭 TOKEN_ON
            header("Content-type:text/html;charset=utf-8");            
            $order_id = I('order_id'); // 订单id
            // 修改订单的支付方式
            $payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
            M('order')->where("order_id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));
            $order = M('order a')->where("a.order_id=$order_id")
                                 ->join("tp_order_goods b ON a.order_id=b.order_id")
                                 ->find();
            if($order['pay_status'] == 1){
            	$this->error('此订单，已完成支付!');
            }       
            // tpshop 订单支付提交
            // $pay_radio = $_REQUEST['pay_radio'];
            // $config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
            // $code_str = $this->payment->get_code($order,$config_value);
            //整理post数据包
            $RequestPostData = array(
                'out_trade_no'=>$order['order_sn'],
                'body'=>$order['goods_name'],
                'attach'=>'购物商品',
                // 'total_fee'=>$order['total_fee']*100,
                'total_fee'=>1,
                'mch_create_ip'=>getIP(),
                'time_start'=>date("YmdHms"),
                );
            require('plugins/WxPay/payInterface_native/request.php');
            $req = new \Request();
            $_code = $req->submitOrderInfo( $RequestPostData );
            $this->_code = $_code;
            //微信JS支付
           // if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
           //     $code_str = $this->payment->getJSAPI($order,$config_value);
           //     exit($code_str);
           // }
           $this->assign('code_str', $code_str); 
           $this->assign('order_id', $order_id); 
           $this->assign('order_sn', $order['order_sn']); 
           $this->display('payment');  // 分跳转 和不 跳转 
    }
    //二维码轮询订单是否交易成功
    public function payResult(){
        $order_id = trim(I('post.order_id'));
        $order_sn = trim(I('post.order_sn'));
        $result = M('order')->field('order_id,order_sn,pay_status')->where(array('order_id'=>$order_id,'order_sn'=>$order_sn))->find();
        if( $result && $result['pay_status'] =='1' ){
            $this->ajaxReturn(array("code"=>200));
        }
    }

    //查询订单
    public function queryOrder( $order_id=null ){
        if(empty($order_id)){
            $this->error("请求错误，请正确操作",U('Index/index'));
        }
        require('plugins/WxPay/payInterface_native/request.php');
        $req = new \Request();
        $RequestData = array('out_trade_no'=>$order_id);
        $data = $req->queryOrder( $RequestData );
    }

    public function getPay(){
    	C('TOKEN_ON',false); // 关闭 TOKEN_ON
    	header("Content-type:text/html;charset=utf-8"); 
    	$order_id = I('order_id'); // 订单id
    	// 修改充值订单的支付方式
    	$payment_arr = M('Plugin')->where("`type` = 'payment'")->getField("code,name");
    	
    	M('recharge')->where("order_id = $order_id")->save(array('pay_code'=>$this->pay_code,'pay_name'=>$payment_arr[$this->pay_code]));    
    	$order = M('recharge')->where("order_id = $order_id")->find();
    	if($order['pay_status'] == 1){
    		$this->error('此订单，已完成支付!');
    	}
    	$pay_radio = $_REQUEST['pay_radio'];
    	$config_value = parse_url_param($pay_radio); // 类似于 pay_code=alipay&bank_code=CCB-DEBIT 参数
        $order['order_amount'] = $order['account'];
    	$code_str = $this->payment->get_code($order,$config_value);
    	//微信JS支付
    	if($this->pay_code == 'weixin' && $_SESSION['openid'] && strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
    		$code_str = $this->payment->getJSAPI($order,$config_value);
    		exit($code_str);
    	}
    	$this->assign('code_str', $code_str);
    	$this->assign('order_id', $order_id);
    	$this->display('recharge'); //分跳转 和不 跳转
    }
    
    // 服务器点对点 // http://www.zbzixun.com/Home/Payment/notifyUrl       
    public function notifyUrl(){            
        $this->payment->response();            
        exit();
    }

    // 页面跳转 // http://www.tp-shop.cn/index.php/Home/Payment/returnUrl        
    public function returnUrl(){
        $result = $this->payment->respond2(); // $result['order_sn'] = '201512241425288593';
        
        if(stripos($result['order_sn'],'recharge') !== false)
        {
            $order = M('recharge')->where("order_sn = '{$result['order_sn']}'")->find();
            $this->assign('order', $order);
            if($result['status'] == 1)
                $this->display('recharge_success');   
            else
                $this->display('recharge_error');   
            exit();            
        }
                
        $order = M('order')->where("order_sn = '{$result['order_sn']}'")->find();
                    
        $this->assign('order', $order);
        if($result['status'] == 1)
            $this->display('success');   
        else
            $this->display('error');   
    }                
}
