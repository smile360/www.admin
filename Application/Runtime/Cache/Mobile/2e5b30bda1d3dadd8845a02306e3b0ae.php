<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8" />
		<title>咨询列表</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/css.css" />
		<script src="/Public/mobile/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body>
		<div class="mui-content consult">
			<div id="slider" class="mui-slider mui-fullscreen">
				<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control-inverted mui-segmented-control">
					<a class="mui-control-item mui-active" href="#item1mobile">
						全部咨询
					</a>
					<a class="mui-control-item" href="#item2mobile">
						已解决咨询
					</a>
					<a class="mui-control-item" href="#item3mobile">
						待解决咨询
					</a>
				</div>
				<div class="mui-slider-group">
					<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">
						<div id="scroll1" class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="consult-card">
									<?php if(is_array($consult)): $i = 0; $__LIST__ = $consult;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
											<a href="<?php echo U('Consult/detail',array('id'=>$vo['consult_id']));?>">
												<div class="consult-body">
													<div class="title"> <?php echo ($vo["tittle"]); ?>
														<?php if($vo["status"] == 1 ): ?><span class="mui-pull-right al">已解决问题</span>
														<?php else: ?>
															<span class="mui-pull-right wait">待解决问题</span><?php endif; ?>
														
													</div>
													<div class="cont mui-clearfix">
													    <div class="cont-img">
														   <img onerror="onerror=null;src='/Public/mobile/img/c_goods.jpg'" src="<?php echo ($vo['path'][0]); ?>">
														</div>
														<p>
															<?php echo (getSubstr($vo["detail"],0,150,"...")); ?>
														</p>
													</div>
												</div>
												<div class="mui-table">
													<div class="mui-table-cell"><span class="date"><?php echo (date("Y/m/d",$vo["createtime"])); ?></span></div>
													<div class="mui-table-cell"><span class="nick-name"><?php echo ($vo["nickname"]); ?></span></div>
													<div class="mui-table-cell mui-text-right"><span class="zan">赞(<?php echo ($vo["like"]); ?>)</span></div>
													<div class="mui-table-cell mui-text-right"><span class="collect">收藏(<?php echo ($vo["collect"]); ?>)</span></div>
												</div>
											</a>
										</li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div>
						</div>
					</div>
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="consult-card">
									<?php if(is_array($solve)): $i = 0; $__LIST__ = $solve;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
											<a href="<?php echo U('Consult/detail',array('id'=>$vo['consult_id']));?>">
												<div class="consult-body">
													<div class="title"> <?php echo ($vo["tittle"]); ?>
														<?php if($vo["status"] == 1 ): ?><span class="mui-pull-right al">已解决问题</span>
														<?php else: ?>
															<span class="mui-pull-right wait">待解决问题</span><?php endif; ?>
														
													</div>
													<div class="cont mui-clearfix">
													<div class="cont-img">
														<img onerror="onerror=null;src='/Public/mobile/img/c_goods.jpg'" src="<?php echo ($vo['path'][0]); ?>">
													</div>
														<p>
															<?php echo (getSubstr($vo["detail"],0,150,"...")); ?>
														</p>
													</div>
												</div>
												<div class="mui-table">
													<div class="mui-table-cell"><span class="date"><?php echo (date("Y/m/d",$vo["createtime"])); ?></span></div>
													<div class="mui-table-cell"><span class="nick-name"><?php echo ($vo["nickname"]); ?></span></div>
													<div class="mui-table-cell mui-text-right"><span class="zan">赞(<?php echo ($vo["like"]); ?>)</span></div>
													<div class="mui-table-cell mui-text-right"><span class="collect">收藏(<?php echo ($vo["collect"]); ?>)</span></div>
												</div>
											</a>
										</li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div>
						</div>
					</div>
					<div id="item3mobile" class="mui-slider-item mui-control-content">
						<div class="mui-scroll-wrapper">
							<div class="mui-scroll">
								<ul class="consult-card">
									<?php if(is_array($unsolve)): $i = 0; $__LIST__ = $unsolve;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
											<a href="<?php echo U('Consult/detail',array('id'=>$vo['consult_id']));?>">
												<div class="consult-body">
													<div class="title"> <?php echo ($vo["tittle"]); ?>
														<?php if($vo["status"] == 1 ): ?><span class="mui-pull-right al">已解决问题</span>
														<?php else: ?>
															<span class="mui-pull-right wait">待解决问题</span><?php endif; ?>
														
													</div>
													<div class="cont mui-clearfix">
													<div class="cont-img">
														<img onerror="/Public/mobile/img/c_goods.jpg" src="<?php echo ($vo['path'][0]); ?>">
													</div>
														<p>
															<?php echo (getSubstr($vo["detail"],0,150,"...")); ?>
														</p>
													</div>
												</div>
												<div class="mui-table">
													<div class="mui-table-cell"><span class="date"><?php echo (date("Y/m/d",$vo["createtime"])); ?></span></div>
													<div class="mui-table-cell"><span class="nick-name"><?php echo ($vo["nickname"]); ?></span></div>
													<div class="mui-table-cell mui-text-right"><span class="zan">赞(<?php echo ($vo["like"]); ?>)</span></div>
													<div class="mui-table-cell mui-text-right"><span class="collect">收藏(<?php echo ($vo["collect"]); ?>)</span></div>
												</div>
											</a>
										</li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
		<nav class="mui-bar mui-bar-tab" id="bar-tab">
			<a class="mui-tab-item" href="<?php echo U('Index/index');?>">
				<span class="mui-icon"><img src="/Public/mobile/img/bar_1.png"/></span>
				<span class="mui-tab-label">首页</span>
			</a>
			<a class="mui-tab-item mui-active" href="<?php echo U('Consult/index');?>">
				<span class="mui-icon"><img src="/Public/mobile/img/bar_2.png"/></span>
				<span class="mui-tab-label">咨询</span>
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
		<script src="/Public/mobile/js/mui.pullToRefresh.js"></script>
		<script>
			mui.init();
			(function($) {
				var deceleration = mui.os.ios ? 0.003 : 0.0009;
				$('.mui-scroll-wrapper').scroll({
					bounce: false,
					indicators: true, //是否显示滚动条
					deceleration: deceleration
				});
				$.ready(function() {
					//循环初始化所有上拉加载
					$.each(document.querySelectorAll('.mui-slider-group .mui-scroll'), function(index, pullRefreshEl) {
						var count = 0;
						$(pullRefreshEl).pullToRefresh({
							up: {
								callback: function() {
									var self = this;
									isnodata = false;
									setTimeout(function() {									   
										var ul = self.element.querySelector('.consult-card');
										var length = ul.querySelectorAll('li').length;
										mui.ajax("<?php echo U('ajaxConsult');?>", {
											data: {
												start: length,
												type: index,
											},
											dataType: 'json',
											type: 'get',
											timeout: 10000,
											async: true,
											success: function(data) {
												var Html = "";
												if(data.code == 200) {
													mui.each(data.result, function(i, item) {
														if(item.status == '1'){
															var _status ='<span class="mui-pull-right al">已解决问题</span></div>';
														}else{
															var _status ='<span class="mui-pull-right wait">待解决问题</span></div>';
														}														
													    li = document.createElement('li');										
														Html += '<a href="'+"<?php echo U('Consult/detail');?>?id="+item.consult_id+'">';
														Html += '<div class="consult-body">';
														Html += '<div class="title">'+item.tittle+_status;
														Html += '<div class="cont mui-clearfix">';
														Html += '<div class="cont-img">';
														Html += '<img src="'+item['path'][0]+'">';
														Html += '</div>';
														Html += '<p>'+item.detail+'</p>';
														Html += '</div>';
														Html += '</div>';
														Html += '<div class="mui-table">';
														Html += '<div class="mui-table-cell"><span class="date">'+item.createtime+'</span></div>';
														Html += '<div class="mui-table-cell"><span class="nick-name">'+item.nickname+'</span></div>';
														Html += '<div class="mui-table-cell mui-text-right"><span class="zan">赞('+item.like+')</span></div>';
														Html += '<div class="mui-table-cell mui-text-right"><span class="collect">收藏('+item.collect+')</span></div>';
														Html += '</div>';
														Html += '</a>';														
														li.innerHTML =Html
														ul.appendChild(li);
													});
												}else{
													 isnodata = true;
												}
												self.endPullUpToRefresh(isnodata);	
											}
										})										
									}, 1000);
								}
							}
						});
					});
				});
			})(mui);
			mui('.mui-bar-tab').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				mui.openWindow({
					url: targetTab,
					id: targetTab,
				})
			});
		</script>
	</body>

</html>