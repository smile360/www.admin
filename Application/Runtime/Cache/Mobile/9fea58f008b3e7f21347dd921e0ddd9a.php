<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8" />
		<title>个人中心</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/css.css" />
		<script src="/Public/mobile/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body>
		<div class="mui-content ucenter">
			<ul class="mui-table-view" style="margin-top: 0;">
				<li class="mui-table-view-cell mui-media">
					<a class="mui-navigate-right" href="<?php echo U('index');?>">
						<img class="mui-media-object mui-pull-left" src="<?php echo ($_SESSION['user']['head_pic']); ?>" onerror="/Public/mobile/img/pl_head.png" />
						<div class="mui-media-body">
							个人资料
						</div>
					</a>
				</li>
			</ul>

			<ul class="mui-table-view modify">
				<li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('User/myconsult');?>"><img src="/Public/mobile/img/u1.png" />我的咨询</a>
				</li>
			</ul>
			<ul class="mui-table-view modify">
				<li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('Cart/cart');?>"><img src="/Public/mobile/img/u2.png" />购物车</a>
				</li>
			</ul>
			<ul class="mui-table-view modify">
				<li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('User/myactivity');?>"><img src="/Public/mobile/img/u3.png" />我的课程</a>
				</li>
				<li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('User/mycollect');?>"><img src="/Public/mobile/img/u4.png" />我的收藏</a>
				</li>
			</ul>
			<ul class="mui-table-view modify">
				<li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('User/mycomment');?>"><img src="/Public/mobile/img/u5.png" />评论消息</a>
				</li>
				<li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('User/mymessages');?>"><img src="/Public/mobile/img/u6.png" />系统消息</a>
				</li>
			</ul>
			<ul class="mui-table-view modify">
				<li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('User/accountSet');?>"><img src="/Public/mobile/img/u7.png" />账户设置</a>
				</li>
				<!-- <li class="mui-table-view-cell">
					<a class="mui-navigate-right" href="<?php echo U('Public/contact');?>"><img src="/Public/mobile/img/u8.png" />关于我们</a>
				</li> -->
			</ul>

		</div>
		<nav class="mui-bar mui-bar-tab" id="bar-tab">
			<a class="mui-tab-item" href="<?php echo U('Index/index');?>">
				<span class="mui-icon"><img src="/Public/mobile/img/bar_1.png"/></span>
				<span class="mui-tab-label">首页</span>
			</a>
			<a class="mui-tab-item" href="<?php echo U('Goods/categoryList');?>">
				<span class="mui-icon"><img src="/Public/mobile/img/bar_2.png"/></span>
				<span class="mui-tab-label">分类</span>
			</a>
			<a class="mui-tab-item" href="<?php echo U('Goods/index');?>">
				<span class="mui-icon"><img src="/Public/mobile/img/bar_3.png"/></span>
				<span class="mui-tab-label">商城</span>
			</a>
			<a class="mui-tab-item mui-active" href="<?php echo U('User/center');?>" style="border-right: 0;">
				<span class="mui-icon"><img src="/Public/mobile/img/bar_4.png"/></span>
				<span class="mui-tab-label">个人</span>
			</a>
		</nav>
		<script type="text/javascript">
			mui('.mui-bar-tab').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				mui.openWindow({
					url: targetTab,
					id: targetTab,
				})
			});
			mui('.ucenter').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				mui.openWindow({
					url: targetTab,
					id: targetTab,
				})
			});
		</script>
	</body>

</html>