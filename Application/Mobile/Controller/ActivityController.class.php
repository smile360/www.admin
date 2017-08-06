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
 * $Author: 当燃   2016-05-10
 */ 
namespace Mobile\Controller;

class ActivityController extends MobileBaseController {
    public function index(){
        $start = I('get.start') ? I('get.start'):'0';
        $page = 5;
        $Model = M('Activity');
        $result = $Model->where(array('is_show'=>'1','is_del'=>'1'))
                        ->order('activity_time desc,sort desc')
                        ->limit( "$start,$page" )
                        ->select();
        if( $result ){
            foreach ($result as $key => $value) {
                $result[$key]['start_time'] = date('Y-m-d H:i',$value['start_time'] ); 
                $result[$key]['sign'] = $value['activity_time'] > time() ? '2':'1'; //1为报名结束 2为报名中
            }
        } 
        if(IS_AJAX){
            if( $result ){
                $data['code'] = 200;
                $data['result'] = $result;
                exit(json_encode( $data ));
            }else{
                exit(json_encode(array('code'=>400)));
            }
        }else{
            $this->data = $result;
            $this->display();
        } 
    }

    //循环处理相关数据切换
    function eachData( $result ){
        foreach ($result as $key => $value) {
            $result[$key]['content'] = htmlspecialchars_decode($value['content']);
            $result[$key]['start_time'] = date('Y-m-d H:i',$value['start_time']);
            $result[$key]['sign'] = $value['activity_time']>time() ? '2':'1'; //1为报名结束 2为报名中
        }
        return $result;
    }
    //获取活动评论数量
    function getSign( $id ){
        $result = M('ActivityComment')->where(array('activity_id'=>$id,'is_show'=>'1'))->count();
        return $result;
    }
    //获取活动报名数量
    function getEntry( $id ){
        $where['activity_id'] = $id;
        $where['is_show'] = '0';
        $where['is_status'] = array('NEQ','2');
        $result = M('ActivityEntry')->where( $where )->count();
        return $result;
    }
    
    //活动详情页
    public function detail(){
        $activity_id = trim(I('get.activity_id'));
        $Model = M('Activity as a');
        $user_id = session("user.user_id");
        //update 点击量
        $Model->where(array('activity_id'=>$activity_id))->setInc('click',1);
        $data['Comment'] = $this->getSign( $activity_id );
        $data['Entry'] = $this->getEntry( $activity_id );
        $data['data'] = $Model->where('a.activity_id='.$activity_id)->find();
        //检测是否已报名
        $checkSign = M('ActivityEntry')->where(array('user_id'=>$user_id,'activity_id'=>$activity_id))->field('is_status')->find();
        $data['data']['content'] = htmlspecialchars_decode( $data['data']['content'] );
        //评论
        $commentList = M('ActivityComment as a')->field("a.comment_id,a.comment_id,a.user_id,a.content,a.add_time,a.ip_address,a.is_show,a.parent_id,b.head_pic,b.nickname as username")
                                                ->where("a.activity_id={$activity_id} AND a.parent_id='0' AND a.is_show='1'")
                                                ->join("tp_users as b ON a.user_id=b.user_id")
                                                ->order('a.add_time desc')
                                                ->limit("0,5")
                                                ->select();
        $comment = array();
        foreach ($commentList as $key => $value) {
            $comment[$key] = $value;
            $result = M('ActivityComment as a')->field("a.comment_id,a.comment_id,a.user_id,a.content,a.add_time,a.ip_address,a.is_show,a.parent_id,b.head_pic,b.nickname as username")
                                               ->where("a.activity_id={$activity_id} AND a.parent_id={$value['comment_id']}")
                                               ->join("tp_users as b ON a.user_id=b.user_id")
                                               ->find();
            if(  $result ){
                $comment[$key]['reply'] = $result;
            }else{
                $comment[$key]['reply'] = NULL;  
            }
            
        }
        //点赞
        $like['num'] = M('ActivityLike')->where(array("activity_id"=>$activity_id,"is_like"=>'1'))->count();
        $like['is_like'] = M("ActivityLike")->where(array("activity_id"=>$activity_id,"user_id"=>$user_id))->count();
        $this->assign( 'like',$like );
        $this->assign( 'comment',$comment );
        $this->assign( 'data',$data );
        $this->assign( 'sign',$checkSign );
        $this->display();
    }

    //下拉get评论信息
    public function getComment(){
        if(IS_GET){
            $activity_id = trim(I('get.activity_id'));
            $page = trim(I('get.start')).",5";
            $commentList = M('ActivityComment as a')->field("a.*,b.head_pic")
                                                ->where("a.activity_id={$activity_id} AND a.parent_id='0' AND a.is_show='1'")
                                                ->join("tp_users as b ON a.user_id=b.user_id")
                                                ->order('a.add_time desc')
                                                ->limit( $page )
                                                ->select();
            $comment = array();
            foreach ($commentList as $key => $value) {
                $value['add_time'] = date('Y-m-d H:s',$value['add_time']);
                $comment[$key] = $value;
                $result = M('ActivityComment as a')->field("a.*,b.head_pic")
                                                   ->where("a.activity_id={$activity_id} AND a.parent_id={$value['comment_id']}")
                                                   ->join("tp_users as b ON a.user_id=b.user_id")
                                                   ->find();
                if(  $result ){
                    $result['add_time'] = date('Y-m-d H:s',$result['add_time']);
                    $comment[$key]['reply'] = $result;
                }else{
                    $comment[$key]['reply'] = NULL;  
                }
                
            }
            // dump($comment);exit;
            if( $comment ){
                exit( json_encode( array('code'=>200,'result'=>$comment) ));
            }else{
                exit(json_encode(array('code'=>400))); //非法操作
            }
        }else{
            exit(json_encode(array('code'=>400))); //非法操作
        }
    }

    //报名
    public function sign(){
        if(IS_AJAX){
            $Model = M('ActivityEntry');
            $data['name'] = I('post.name');
            $data['sex'] = I('post.sex');
            $data['address'] = I('post.city');
            $data['age'] = I('post.age');
            $data['phone'] = I('post.tel');
            $data['email'] = I('post.mail');
            $data['work_name'] = I('post.company');
            $data['activity_id'] = I('post.activity_id');
            $data['add_time'] = time();
            $data['user_id'] = session('user.user_id');
            //检测手机号是否已登记
            $_issign = $Model->where(array("activity_id"=> $data['activity_id'],"phone"=>$data['phone']))->field('is_status')->find();
            if( $_issign['is_status'] == '1' ){
                $this->ajaxReturn(array('msg'=>'此手机号已报名！'));
            }else if($_issign['is_status'] == '0'){
                $this->ajaxReturn(array('msg'=>'您已经报名！'));
            }
            $result = $Model->data( $data )->add();
            if( $result ){
                $this->ajaxReturn(array("msg"=>200));
            }else{
                $this->ajaxReturn(array("msg"=>'报名失败，请稍后重试'));
            }
        }else{
            $activity_id = trim(I('get.activity_id'));
            $this->assign( 'activity_id',$activity_id );
            $this->display();
        }
        
    }

    //评论
    public function comment(){
        if(IS_AJAX){
            $data['activity_id'] = I('post.activity_id');
            $data['content'] = I('post.question');
            $data['user_id'] = session('user.user_id');
            $data['username'] = session('user.nickname');
            $data['add_time'] = time();
            $data['ip_address'] = get_client_ip();
            $result = M('ActivityComment')->data( $data )->add();
            if( $result ){
                exit('200');
            }else{
                exit('400');
            }
        }else{
            $activity_id = trim(I('get.activity_id'));
            $this->assign( 'activity_id',$activity_id );
            $this->display();
        }
        
    }
   /**
    * 商品详情页
    */ 
    public function group(){
        //form表单提交
        C('TOKEN_ON',true);  
        $goodsLogic = new \Home\Logic\GoodsLogic();
        $goods_id = I("get.id",66);
        
        $group_buy_info = M('GroupBuy')->where("goods_id = $goods_id and ".time()." >= start_time and ".time()." <= end_time ")->find(); // 找出这个商品
        if(empty($group_buy_info)) 
        {
            //$this->error("此商品没有团购活动",U('Home/Goods/goodsInfo',array('id'=>$goods_id)));
        }
                    
        $goods = M('Goods')->where("goods_id = $goods_id")->find();
        $goods_images_list = M('GoodsImages')->where("goods_id = $goods_id")->select(); // 商品 图册
                
        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id = $goods_id")->select(); // 查询商品属性表
                        
        $Model = new \Think\Model();        
        // 商品规格 价钱 库存表 找出 所有 规格项id
        $keys = M('SpecGoodsPrice')->where("goods_id = $goods_id")->getField("GROUP_CONCAT(`key` SEPARATOR '_') ");         
        if($keys)
        {
             $specImage =  M('SpecImage')->where("goods_id = $goods_id and src != '' ")->getField("spec_image_id,src");// 规格对应的 图片表， 例如颜色                
             $keys = str_replace('_',',',$keys);             
             $sql  = "SELECT a.name,a.order,b.* FROM __PREFIX__spec AS a INNER JOIN __PREFIX__spec_item AS b ON a.id = b.spec_id WHERE b.id IN($keys) ORDER BY a.order";
             $filter_spec2 = $Model->query($sql);             
             foreach($filter_spec2 as $key => $val)
             {                                  
                 $filter_spec[$val['name']][] = array(
                     'item_id'=> $val['id'],
                     'item'=> $val['item'],
                     'src'=>$specImage[$val['id']],
                     );                 
             }            
        }                
        $spec_goods_price  = M('spec_goods_price')->where("goods_id = $goods_id")->getField("key,price,store_count"); // 规格 对应 价格 库存表
        M('Goods')->where("goods_id=$goods_id")->save(array('click_count'=>$goods['click_count']+1 )); // 统计点击数
        $commentStatistics = $goodsLogic->commentStatistics($goods_id);// 获取某个商品的评论统计           
        $this->assign('group_buy_info',$group_buy_info);
        $this->assign('spec_goods_price', json_encode($spec_goods_price,true)); // 规格 对应 价格 库存表
        $this->assign('commentStatistics',$commentStatistics);
        $this->assign('goods_attribute',$goods_attribute);       
        $this->assign('goods_attr_list',$goods_attr_list);
        $this->assign('filter_spec',$filter_spec);
        $this->assign('goods_images_list',$goods_images_list);
        $this->assign('goods',$goods);
        $this->display();
    } 
    
    
    /**
     * 团购活动列表
     */
    public function group_list()
    {
    	$count =  M('GroupBuy')->where(time()." >= start_time and ".time()." <= end_time ")->count();// 查询满足要求的总记录数
    	$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数  	
    	$show = $Page->show();// 分页显示输出
    	$this->assign('page',$show);// 赋值分页输出
        $list = M('GroupBuy')->where(time()." >= start_time and ".time()." <= end_time ")->limit($Page->firstRow.','.$Page->listRows)->select(); // 找出这个商品        
        $this->assign('list', $list);
        $this->display();
    }
}