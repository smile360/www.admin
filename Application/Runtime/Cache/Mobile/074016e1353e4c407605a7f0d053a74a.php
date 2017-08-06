<?php if (!defined('THINK_PATH')) exit(); if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><div class="order_list">
          <h2>
              <a href="javascript:void(0);">
                  <img src="/Template/mobile/new/Static/images/dianpu.png"><span>店铺名称:网站自营</span><strong>
                  <img src="/Template/mobile/new/Static/images/icojiantou1.png"></strong>
              </a>
          </h2>
         	<a href="<?php echo U('/Mobile/User/order_detail',array('id'=>$list['order_id']));?>">
	          <?php if(is_array($list["goods_list"])): $i = 0; $__LIST__ = $list["goods_list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$good): $mod = ($i % 2 );++$i;?><dl>  
		          <dt><img src="<?php echo (goods_thum_images($good["goods_id"],200,200)); ?>"></dt>
		          <dd class="name"><strong><?php echo ($good["goods_name"]); ?></strong>
		          <span><?php echo ($good["spec_key_name"]); ?> </span></dd>
		          <dd class="pice">￥<?php echo ($good['member_goods_price']); ?>元<em>x<?php echo ($good['goods_num']); ?></em></dd>
		          </dl><?php endforeach; endif; else: echo "" ;endif; ?>
          	</a>
          <div class="pic">共<?php echo (count($list["goods_list"])); ?>件商品<span>实付：</span><strong>￥<?php echo ($list['order_amount']); ?>元</strong></div>
          <div class="anniu" style="width:95%">
                <?php if($list["cancel_btn"] == 1): ?><span onClick="cancel_order(<?php echo ($list["order_id"]); ?>)">取消订单</span><?php endif; ?>
                <?php if($list["pay_btn"] == 1): ?><a href="<?php echo U('Mobile/Cart/cart4',array('order_id'=>$list['order_id']));?>">立即付款</a><?php endif; ?>
                <?php if($list["receive_btn"] == 1): ?><a href="<?php echo U('Mobile/User/order_confirm',array('id'=>$list['order_id']));?>">收货确认</a><?php endif; ?>
                <?php if($list["comment_btn"] == 1): ?><a href="<?php echo U('/Mobile/User/comment');?>">评价</a><?php endif; ?>                
                <?php if($list["shipping_btn"] == 1): ?><a href="<?php echo U('User/express',array('order_id'=>$list['order_id']));?>">查看物流</a><?php endif; ?>        
               <!-- <?php if($list["return_btn"] == 1): ?><a href="<?php echo U('Mobile/Article/article',array('article_id'=>'22'));?>" target="_blank">联系客服</a><?php endif; ?> -->
          </div>
       </div><?php endforeach; endif; else: echo "" ;endif; ?>