<?php

namespace Mobile\Controller;

class IndexController extends MobileBaseController {

    public function __construct() {
        // echo 222;die;
        parent::__construct();
        //   $mobile = M('users')->field("mobile")->where(array("user_id"=>session('user.user_id')))->find();
        // if( !$mobile ){
        //     header('location:http://www.fengzakka.cn/Mobile/User/bind_mobile' );
        //   } 
    }

    public function index() {
        //nav
        $article_cat = M("article_cat")->where(array("show_in_nav" => 1, "parent_id" => 0))->field("cat_name,cat_id")->select();
        // var_dump($article_cat);die;
        $nav = array(
            array(
                "action" => "Article/news",
                "name" => "最新动态"
            ),
            array(
                "action" => "Activity/index",
                "name" => "手工培训"
            ),
        );
        $catNav = array();
        foreach ($article_cat as $key => $value) {
            if (strstr($value['cat_name'], '珠宝学院')) {
                $catNav = array(
                    "action" => "Article/college",
                    "name" => $value['cat_name'],
                    "id" => $value['cat_id'],
                );
            } elseif (strstr($value['cat_name'], '胡博士同学会')) {
                $catNav = array(
                    "action" => "Article/college",
                    "name" => $value['cat_name'],
                    "id" => $value['cat_id'],
                );
            } elseif (strstr($value['cat_name'], '专栏')) {
                $catNav = array(
                    "action" => "Article/expert",
                    "name" => $value['cat_name'],
                    "id" => $value['cat_id'],
                );
            } elseif (strstr($value['cat_name'], '珠宝百科')) {
                $catNav = array(
                    "action" => "Article/baike",
                    "name" => $value['cat_name'],
                    "id" => $value['cat_id'],
                );
            } elseif (strstr($value['cat_name'], '国家标准')) {
                $catNav = array(
                    "action" => "Article/standard",
                    "name" => $value['cat_name'],
                    "id" => $value['cat_id'],
                );
            }
            array_push($nav, $catNav);
        }
        array_push($nav, array("action" => "Goods/index", "name" => "商城"));
        
        $this->nav = array_filter($nav);
//         dump( $this->nav );exit;
        //轮播
        $bannerList = M('AdPosition a')->field("b.*")
        ->where("a.position_name LIKE '%mobile%'")
        ->join("tp_ad b ON a.position_id=b.pid")
        ->order("b.orderby desc")
        ->limit(3)
        ->select();
        $this->banner = $bannerList;
        //拉取咨询列表
        $newConsult = M('consult')->where(array('is_show' => 0))
        ->group('consult_id')
        ->order('createtime desc')
        ->limit(6)
        ->select();
        foreach ($newConsult as $key => $value) {
            $newConsult[$key]['path'] = explode(',', $value['path']);
        }
        $this->consult = $newConsult;
        $this->display();
    }

    //index主页面的ajax按需加载
    public function ajaxConsult() {
        $start = I('get.start');
        $num = I('get.num');
        $newConsult = M('consult')->where(array('is_show' => 0))
        ->group('consult_id')
        ->order('createtime desc')
        ->limit("$start,$num")
        ->select();
        if ($newConsult) {
            foreach ($newConsult as $key => $value) {
                $newConsult[$key]['path'] = explode(',', $value['path']);
            }
            exit(json_encode(array('code' => 200, 'result' => $newConsult)));
        } else {
            exit(json_encode(array('code' => 400)));
        }
    }

    /**
     * 联系我们
     */
    public function contact() {
        $config = tpCache('shop_info');
        $this->assign('info', $config);
        $this->display();
    }

    /**
     * 分类列表显示
     */
    public function categoryList() {
        $this->display();
    }

    /**
     * 模板列表
     */
    public function mobanlist() {
        $arr = glob("D:/wamp/www/svn_tpshop/mobile--html/*.html");
        foreach ($arr as $key => $val) {
            $html = end(explode('/', $val));
            echo "<a href='http://www.php.com/svn_tpshop/mobile--html/{$html}' target='_blank'>{$html}</a> <br/>";
        }
    }

    /**
     * 商品列表页
     */
    public function goodsList() {
        $goodsLogic = new \Home\Logic\GoodsLogic(); // 前台商品操作逻辑类
        $id = I('get.id', 0); // 当前分类id
        $lists = getCatGrandson($id);
        $this->assign('lists', $lists);
        $this->display();
    }

    public function ajaxGetMore() {
        $p = I('p', 1);
        $favourite_goods = M('goods')->where("is_recommend=1 and is_on_sale=1")->order('goods_id DESC')->page($p, 10)->cache(true, TPSHOP_CACHE_TIME)->select(); //首页推荐商品
        $this->assign('favourite_goods', $favourite_goods);
        $this->display();
    }

    public function phpinfo() {
        phpinfo();
    }
}
