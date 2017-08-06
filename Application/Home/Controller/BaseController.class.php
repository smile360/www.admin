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
use Think\Controller;
class BaseController extends Controller {
    public $session_id;
    public $cateTrre = array();
    /*
     * 初始化操作
     */
    public function _initialize() {  
      header("Content-type:text/html;charset=utf-8");
    	$this->session_id = session_id(); // 当前的 session_id
      define('SESSION_ID',$this->session_id); //将当前的session_id保存为常量，供其它方法调用
      // 判断当前用户是否手机                
      if(isMobile())
          cookie('is_mobile','1',3600); 
      else 
          cookie('is_mobile','0',3600);
      $this->public_assign(); 
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
       // if(session('user')){
       //    $this->assign('userInfo',session('user'));
       // }              
       $goods_category_tree = get_goods_category_tree();    
       $this->cateTrre = $goods_category_tree;
       $this->assign('goods_category_tree', $goods_category_tree);                     
       $brand_list = M('brand')->cache(true,TPSHOP_CACHE_TIME)->field('id,parent_cat_id,logo,is_hot')->where("parent_cat_id>0")->select();        
       $this->assign('brand_list', $brand_list);
       $this->assign('tpshop_config', $tpshop_config);
       //导航
       $article_cat = M("article_cat")->where(array("show_in_nav"=>1,"parent_id"=>0))->field("cat_name,cat_id")->select();
       $nav = array(
                array(
                  "action"=>"Index/index",
                  "name"=>"首页"
                  ),
//                array(
//                  "action"=>"Goods/mall",
//                  "name"=>"珠宝商城"
//                  ),
//                array(
//                  "action"=>"Activity/index",
//                  "name"=>"珠宝培训"
//                  ),
//                array(
//                  "action"=>"Consult/index",
//                  "name"=>"咨询结果"
//                  ),
//                array(
//                  "action"=>"Article/news",
//                  "name"=>"最新动态"
//                  ),
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
              "action"=>"Article/homecoming",
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
        $this->assign('nav', $nav);
        //友情链接
        $friend_link = M('friend_link')->where(array("is_show"=>'1'))->order("orderby desc")->select();
        $this->assign('friend_link', $friend_link);         
    }


}