<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html >
<html>
<head>
<meta name="Generator" content="TPSHOP v1.1" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="applicable-device" content="mobile">
<title><?php echo ($tpshop_config['shop_info_store_title']); ?></title>
<meta http-equiv="keywords" content="<?php echo ($tpshop_config['shop_info_store_keyword']); ?>" />
<meta name="description" content="<?php echo ($tpshop_config['shop_info_store_desc']); ?>" />
<meta name="Keywords" content="TPshop触屏版  TPshop 手机版" />
<meta name="Description" content="TPshop触屏版   TPshop商城 "/>
<link rel="stylesheet" href="/Template/mobile/new/Static/css/public.css">
<link rel="stylesheet" href="/Template/mobile/new/Static/css/user.css">
<script type="text/javascript" src="/Template/mobile/new/Static/js/jquery.js"></script>
<script src="/Public/js/global.js"></script>
<script src="/Public/js/mobile_common.js"></script>
<script type="text/javascript" src="/Template/mobile/new/Static/js/modernizr.js"></script>
<script type="text/javascript" src="/Template/mobile/new/Static/js/layer.js" ></script>
</head>


<body>      
<div id="tbh5v0">
<div class="user_com">
<div class="com_top">
	<h2><a href="<?php echo U('Mobile/User/userinfo');?>">设置</a></h2>
	<p class="tuij_cas">
	        <?php echo ($user['nickname']); ?>
            <?php if($first_nickname != ''): ?><br />
            	<span>由( <?php echo ($first_nickname); ?> )推荐</span><?php endif; ?>
    </p>
	<dl>
		<dt>
		<img src='<?php echo ((isset($user[head_pic]) && ($user[head_pic] !== ""))?($user[head_pic]):"/Template/mobile/new/Static/images/user68.jpg"); ?>' />
        
		<dd><?php echo ($level_name); ?></dd>
		</dt>
	</dl>
</div>
<div class="uer_topnav">
	<ul>
		<li><a href="<?php echo U('Mobile/User/order_list');?>" ><span><?php echo ($order_count); ?></span>我的订单</a></li>
		<!-- <li class="bain"><a href="<?php echo U('Mobile/User/collect_list');?>"><span><?php echo ($goods_collect_count); ?></span>我的收藏</a></li> -->
		<!-- <li><a href="<?php echo U('Mobile/User/comment');?>"><span><?php echo ($comment_count); ?></span>我的评价</a></li> -->
	</ul>
</div>
<div class="Wallet">
	<ul>
	<li class="bain1"><strong>￥<?php echo ($user['user_money']); ?>元</strong><span>余额</span></li>
	<li class="bain1"><strong><?php echo ($coupon_count); ?></strong><span>优惠券</span></li>
	<li><strong><?php echo ($user['pay_points']); ?></strong><span>积分</span></li>
	</ul>
	<a href="<?php echo U('Mobile/User/account');?>"><em class="Icon Icon1"></em><dl><dt>我的钱包</dt><dd style="color:#aaaaaa;">查看我的钱包</dd></dl></a>
</div>


<div class="Wallet">
	<a href="<?php echo U('Mobile/User/order_list');?>" ><em class="Icon Icon2"></em><dl class="b"><dt>全部订单</dt><dd>查看订单</dd></dl></a>
	<!-- <a href="<?php echo U('Mobile/User/coupon');?>"><em class="Icon Icon3"></em><dl class="b"><dt>我的优惠券</dt><dd>&nbsp;</dd></dl></a> -->
<!-- 	<a href="<?php echo U('Mobile/User/comment');?>"><em class="Icon Icon4"></em><dl class="b"><dt>我的评价</dt><dd>查看评价</dd></dl></a>
	<a href="<?php echo U('Mobile/User/collect_list');?>"><em class="Icon Icon10"></em><dl class="b"><dt>我的收藏</dt><dd>&nbsp;</dd></dl></a> -->
	<!-- <a href="<?php echo U('Mobile/User/withdrawals');?>"><em class="Icon Icon1"></em><dl><dt>申请提现</dt><dd>&nbsp;</dd></dl></a> -->
</div>
<div class="Wallet">
	<a href="<?php echo U('User/address_list');?>"><em class="Icon Icon5"></em><dl class="b"><dt>地址管理</dt><dd>&nbsp;</dd></dl></a>
	<!-- <a href="<?php echo U('User/points');?>"><em class="Icon Icon6"></em><dl class="b"><dt>积分余额</dt><dd>&nbsp;</dd></dl></a> -->
	<!-- <a href="<?php echo U('User/message_list');?>"><em class="Icon Icon7"></em><dl class="b"><dt>我的留言</dt><dd>&nbsp;</dd></dl></a> -->
	<!-- <a href="<?php echo U('User/return_goods_list');?>"><em class="Icon Icon11"></em><dl class="<?php if($tpshop_config['distribut_switch'] == 1): ?>b<?php endif; ?>"><dt>售后服务</dt><dd>&nbsp;</dd></dl></a> -->
    
    <?php if($tpshop_config['distribut_switch'] == 1): ?><a href="<?php echo U('Distribut/index');?>"><em class="Icon Icon1"></em><dl><dt>我发展收益</dt><dd>&nbsp;</dd></dl></a><?php endif; ?>    
</div>
</div>
</div>
</body>
</html>