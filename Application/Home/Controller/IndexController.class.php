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
use Think\Page;
use Think\Verify;
class IndexController extends BaseController {
    
   
    
    public function index(){
        header("Location: ".U('Mobile/Index/index')); exit;
        // 如果是手机跳转到 手机模块
        if(true == isMobile()){
            header("Location: ".U('Mobile/Index/index')); 
        }
        //轮播
        $bannerList = M('AdPosition a')->field("b.*")
                                       ->where("a.position_name LIKE '%pc%'")
                                       ->join("tp_ad b ON a.position_id=b.pid")
                                       ->order("b.orderby desc")
                                       ->limit(3)
                                       ->select(); 
        //咨询
        $consultList = M("Consult")->field("consult_id,tittle,path,createtime,status")
                                   ->where(array("is_show"=>0))
                                   ->order("createtime desc")
                                   ->limit(12)
                                   ->select();
        foreach ($consultList as $key => $value) {
            $consultList[$key]['status'] = $value['status']==='1'?'已解决咨询':'待解决咨询';
            $consultList[$key]['path'] = explode(',', $value['path']);
        }
        $goods = M('Goods')->field("goods_id,goods_sn,goods_name,shop_price,original_img")
                           ->order('on_time desc')
                           ->limit(12)
                           ->select();
        $this->consult = $consultList ;                           
        $this->assign('goods' , $goods);
        $this->assign('banner' , $bannerList);
        $this->display();
    }
    //循环把分类的数组编程字符串
    function getcates($arr){
        $str = '';
        foreach ($arr as $key => $value) {
            foreach ($value as $k => $v) {
                $str .= $v.',';
            }
        }
        $string  = rtrim($str,',');
        return $string;
    }
    public function articlelist(){
        $this ->display('articlelist');
    }
    /**
     *  公告详情页
     */
    public function notice(){
        $this->display();
    }
    
    // 二维码
    public function qr_code(){        
        // 导入Vendor类库包 Library/Vendor/Zend/Server.class.php
        //http://www.tp-shop.cn/Home/Index/erweima/data/www.99soubao.com
         require_once 'ThinkPHP/Library/Vendor/phpqrcode/phpqrcode.php';
          //import('Vendor.phpqrcode.phpqrcode');
            error_reporting(E_ERROR);            
            $url = urldecode($_GET["data"]);
            \QRcode::png($url);          
    }
    
    // 验证码
    public function verify()
    {
        //验证码类型
        $type = I('get.type') ? I('get.type') : '';
        $fontSize = I('get.fontSize') ? I('get.fontSize') : '40';
        $length = I('get.length') ? I('get.length') : '4';
        
        $config = array(
            'fontSize' => $fontSize,
            'length' => $length,
            'useCurve' => true,
            'useNoise' => false,
        );
        $Verify = new Verify($config);
        $Verify->entry($type);        
    }
    
    // 促销活动页面
    public function promoteList()
    {                          
        $Model = new \Think\Model();
        $goodsList = $Model->query("select * from __PREFIX__goods as g inner join __PREFIX__flash_sale as f on g.goods_id = f.goods_id   where ".time()." > start_time  and ".time()." < end_time");                        
        $brandList = M('brand')->getField("id,name,logo");
        $this->assign('brandList',$brandList);
        $this->assign('goodsList',$goodsList);
        $this->display();
    }
    
    function truncate_tables (){
        $model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
        $tables = $model->query("show tables");
        $table = array('tp_admin','tp_config','tp_region','tp_system_module','tp_admin_role','tp_system_menu');
        foreach($tables as $key => $val)
        {                                    
            if(!in_array($val['tables_in_tpshop'], $table))                             
                echo "truncate table ".$val['tables_in_tpshop'].' ; ';
                echo "<br/>";         
        }                
    }

}