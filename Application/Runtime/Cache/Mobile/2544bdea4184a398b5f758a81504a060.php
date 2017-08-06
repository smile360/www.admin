<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<title>珠宝咨询网-搜索结果</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
		<link rel="stylesheet" href="/Public/mobile/css/css.css" />
	</head>
	<style>
	.date{
		color:#000 !important;
		float:right;
	}
	</style>
	<body>
		<div class="mui-content consult mui-scroll-wrapper" id="pullrefresh">
			<div class="mui-scroll">
				<form class="search" id="searchform" action="<?php echo U('Public/search');?>" method="post">
					<input type="text" name="search" id="search" placeholder="搜索文章、咨询"  value="<?php echo ($keyword); ?>"/>
				</form>
				<ul class="consult-card">
					<?php if(empty($data)): ?><p style="text-align:center;line-height:50px;background:#fff;margin-top:10px;">暂时无相关信息</p>
					<?php else: ?>
						<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
								<?php if($vo["type"] == 'article' ): ?><a href="<?php echo U('Article/detail',array('article_id'=>$vo['id']));?>">
								<?php else: ?>
									<a href="<?php echo U('Consult/detail',array('id'=>$vo['id']));?>"><?php endif; ?>
									<div class="consult-body">
										<div class="title"><?php echo ($vo["title"]); ?>	<span class="date"><?php echo ($vo["time"]); ?></span></div>
										<div class="cont">
											<img src="<?php echo ((isset($vo["img"]) && ($vo["img"] !== ""))?($vo["img"]):'/Public/mobile/img/c_goods.jpg'); ?>">
											<p>瑰丽、华贵的红宝石是宝石之王，是宝中之宝， 其优点超过所有的宝石。这么珍贵的宝石应该如何 选购呢.有什么样的方法可以挑选到好的红宝石？
											</p>
										</div>
									</div>
								</a>
							</li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
				</ul>
			</div>

		</div>
		<script src="/Public/mobile/js/mui.min.js"></script>
		<script>
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
					var table = document.body.querySelector('.consult-card');
					var cells = document.body.querySelectorAll('.consult-card li').length;
					var _keyword = document.getElementById("search").value;
					mui.ajax("<?php echo U('search');?>", {
						data: {
							start: cells,
							search: _keyword,
						},
						dataType: 'json',
						type: 'get',
						timeout: 10000,
						async: true,
						success: function(data) {
							var Html = "";
							if(data.code == 200) {
								mui.each(data.result, function(i, item) {
									var Url = item.type =='article' ? "<?php echo U('Article/detail');?>?article_id="+item.id : "<?php echo U('Consult/detail');?>?id="+item.id; 
									Html += '<li>';
									Html += '<a href="'+Url+'">';
									Html += '<div class="consult-body">';
									Html += '<div class="title">'+item.title;
									Html += '<span class="date">'+item.time+'</span>';
									Html += '</div>';
									Html += '<div class="cont mui-clearfix">';
									Html += '<img src="'+item.img+'">';
									Html += '<p>'+item.detail+'</p>';
									Html += '</div>';
									Html += '</div>';
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

			mui('.consult-card li').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				mui.openWindow({
					url: targetTab,
					id: targetTab,
				})
			});			
			document.getElementById("search").focus();			
		</script>
	</body>

</html>