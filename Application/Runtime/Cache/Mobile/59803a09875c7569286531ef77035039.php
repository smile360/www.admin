<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8" />
		<title>枫FENGZAKKA</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/css.css" />
		<script src="/Public/mobile/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body>
		<div class="mui-content mui-scroll-wrapper index" id="pullrefresh">
			<div class="mui-scroll">
				<div class="logo">
					<img src="/Public/mobile/img/logo.png" />
					<a href="<?php echo U('User/consultation');?>" class="mui-pull-right">我要咨询</a>
					<a href="<?php echo U('Consult/index');?>" class="mui-pull-right">咨询结果</a>
				</div>
				<div id="scrollimg" class="scrollimg">
					<div class="bd">
						<ul>
							<?php if(is_array($banner)): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><li>
									<a href="<?php echo ($data['ad_link']); ?>"><img src="<?php echo ($data['ad_code']); ?>" /></a>
								</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<div class="hd">
						<ul></ul>
					</div>
				</div>
				<form class="search" id="searchform" action="<?php echo U('Public/search');?>" method="post">
					<input type="search" name="search" id="search" placeholder="搜索文章、咨询" onfocus="foc();"  onblur="blu();" />
				</form>
				<ul class="menu-list mui-clearfix">
					<?php if(is_array($nav)): $k = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($k == 1 || $k == 2 || $k == 3 ): if(empty($vo["id"])): ?><li class="r-line mar">
									<a href="<?php echo U($vo['action']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li>
							<?php else: ?>
								<li class="r-line mar">
									<a href="<?php echo U($vo['action'],'pid='.$vo['id']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li><?php endif; ?>
						<?php elseif($k == 4): ?>
							<?php if(empty($vo["id"])): ?><li class="mar">
									<a href="<?php echo U($vo['action']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li>
							<?php else: ?>
								<li class="mar">
									<a href="<?php echo U($vo['action'],'pid='.$vo['id']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li><?php endif; ?>
						<?php elseif($k == 5 || $k == 6 || $k == 7 ): ?>
							<?php if(empty($vo["id"])): ?><li class="r-line">
									<a href="<?php echo U($vo['action']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li>
							<?php else: ?>
								<li class="r-line">
									<a href="<?php echo U($vo['action'],'pid='.$vo['id']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li><?php endif; ?>
						<?php else: ?>
							<?php if(empty($vo["id"])): ?><li>
									<a href="<?php echo U($vo['action']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li>
							<?php else: ?>
								<li>
									<a href="<?php echo U($vo['action'],'pid='.$vo['id']); ?>">
										<img src="/Public/mobile/img/icon_<?php echo ($k); ?>.png" />
										<p class="mar"><?php echo ($vo["name"]); ?></p>
									</a>
								</li><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
				</ul>
				<div class="cotegory-title">
					咨询展厅 <span> Consultation results</span>
				</div>
				<ul class="consult-list mui-clearfix">
					<?php if(is_array($consult)): $k = 0; $__LIST__ = $consult;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li>	
							<a href="<?php echo U('Consult/detail',array('id'=>$vo['consult_id']));?>">
								<span>
									<img src="<?php echo ($vo["path"]["0"]); ?>"/>
									<?php if($vo["status"] == 1 ): ?><em class="al">已解决咨询</em>
									<?php else: ?>
										<em class="wait">待解决咨询</em><?php endif; ?>
								</span>
								<p class="mui-ellipsis"><b>[0<?php echo ($k); ?>]</b> <?php echo ($vo["tittle"]); ?> </p>
							</a>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>

			</div>
		</div>
		<nav class="mui-bar mui-bar-tab" id="bar-tab">
			<a class="mui-tab-item mui-active" href="<?php echo U('Index/index');?>">
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
			<a class="mui-tab-item" href="<?php echo U('User/center');?>" style="border-right: 0;">
				<span class="mui-icon"><img src="/Public/mobile/img/bar_4.png"/></span>
				<span class="mui-tab-label">个人</span>
			</a>
		</nav>
		<script src="/Public/mobile/js/TouchSlide.1.1.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
			//上拉加载
			mui.init({
				pullRefresh: {
					container: '#pullrefresh',
					up: {
						contentrefresh: '正在加载...',
						callback: pullupRefresh
					}
				}
			});
			var hasMore = false;

			function pullupRefresh() {
				setTimeout(function() {
					var table = document.body.querySelector('.consult-list');
					var cells = document.body.querySelectorAll('.consult-list li').length;
					mui.ajax("<?php echo U('ajaxConsult');?>", {
						data: {
							start: cells,
							num: 6,
						},
						dataType: 'json',
						type: 'get',
						timeout: 10000,
						async: true,
						success: function(data) {
							var Html = "";
							if(data.code == 200) {
								mui.each(data.result, function(i, item) {
									Html += '<li>';
									Html += '<a href="'+"<?php echo U('Consult/detail');?>?id="+item.consult_id+'">';
									Html += '<span>';
									Html += '<img src="'+item.path[0]+'"/>';
									if(item.status == 1){
										Html += '<em class="al">已解决咨询</em>';
									}else{
										Html += '<em class="wait">待解决咨询</em>';
									}
									Html += '</span>';
									Html += '<p class="mui-ellipsis"><b>[0'+(cells+i+1)+']</b>'+item.tittle+'</p>';
									Html += '</a>';
									Html += '</li>';	
								});
								table.insertAdjacentHTML('beforeend', Html);
							} else {
								hasMore = true;
							}
							mui('#pullrefresh').pullRefresh().endPullupToRefresh(hasMore);
						}
					})
				}, 1500);
			}
                        if(mui("#scrollimg li").length) {
                            TouchSlide({
                                    slideCell: "#scrollimg",
                                    titCell: ".hd ul",
                                    mainCell: ".bd ul",
                                    effect: "leftLoop",
                                    interTime: 4000,
                                    delayTime: 800,
                                    autoPage: true,
                                    autoPlay: true
                            });

                        }
			mui('.consult-list').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				window.location.href = targetTab;
			});
			mui('.mui-clearfix').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				window.location.href = targetTab;
			});
			mui('.logo').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				window.location.href = targetTab;
			});
			mui('.mui-bar-tab').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				mui.openWindow({
					url: targetTab,
					id: targetTab,
				})
			});
			function foc(){
			  document.getElementById("bar-tab").style.display="none";
			}
			function blu(){
			  document.getElementById("bar-tab").style.display="block";
			}
			
			document.getElementById("search").addEventListener('tap',function(){
			   location.href="<?php echo U('Public/search');?>";			
			})
			
		</script>
	</body>

</html>