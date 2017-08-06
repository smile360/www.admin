<?php if (!defined('THINK_PATH')) exit();?><title>最新动态</title>
<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8" />
		<title>珠宝咨询网</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/css.css" />
		<script src="/Public/mobile/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
	</head>
	
		<div class="mui-content consult">

			<div id="pullrefresh" class="mui-scroll-wrapper">
				<div class="mui-scroll">
					<ul class="mui-table-view modify" style="margin-top: 0;">
						<?php if(empty($list)): ?><p>暂时无相关最新动态</p>
						<?php else: ?>
							<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><li class="mui-table-view-cell">
									<a class="mui-navigate-right" href="<?php echo U('detail',array('article_id'=>$data['article_id']));?>"><span>[ <?php echo ($data["cat_name"]); ?> ]</span><?php echo ($data["title"]); ?></a>
								</li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
					</ul>

				</div>
			</div>

		</div>
		<script type="text/javascript">
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
					var table = document.body.querySelector('.modify');
					var cells = document.body.querySelectorAll('.modify li').length;
					mui.ajax("<?php echo U('news');?>", {
						data: {
							start: cells,
						},
						dataType: 'json',
						type: 'get',
						timeout: 10000,
						async: true,
						success: function(data) {
							var Html = "";
							if(data.code == 200) {
								mui.each(data.result, function(i, item) {
									Html += '<li class="mui-table-view-cell">';
									Html += '<a class="mui-navigate-right" href="'+"<?php echo U('detail');?>?article_id="+item.article_id+'"><span>[ '+item.cat_name+' ]</span>'+item.title+'</a>';
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
			mui('.modify').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				mui.openWindow({
					url: targetTab,
					id: targetTab,
				})
			});
			
			
		</script>
	</body>

</html>