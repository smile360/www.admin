<?php if (!defined('THINK_PATH')) exit();?><title>培训</title>
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
	
	<body>
		<div class="mui-content consult training">
			<div id="pullrefresh" class="mui-scroll-wrapper">
				<div class="mui-scroll">
					<ul class="consult-card">
						<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
								<a href="<?php echo U('detail',array('activity_id'=>$vo['activity_id']));?>">
									<div class="consult-body mui-clearfix">
										<div class="title"><?php echo ($vo["title"]); ?></div>
										<div class="cont mui-clearfix">
											<img src="<?php echo ($vo["activity_logo"]); ?>">
											<p>主讲导师：<?php echo ($vo["teacher"]); ?></p>
											<p>主要内容：<?php echo ($vo["course"]); ?></p>
											<p>上课时间：<?php echo ($vo["course_time"]); ?></p>
										</div>
									</div>
									<div class="mui-table">
										<div class="mui-table-cell"><?php echo ($vo["lunch_time"]); ?>
											<?php if($vo["sign"] == '2'): ?><button class="mui-pull-right">立即报名</button>
											<?php else: ?>
												<button class="mui-pull-right" style="background:#8a8a8a;color:#fff;border-color:#8a8a8a">报名结束</button><?php endif; ?>
											
										</div>
									</div>
								</a>
							</li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
			</div>

		</div>
		<script>
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
					mui.ajax("<?php echo U('index');?>", {
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
									if(item.sign == '2'){
										var _sign = '<button class="mui-pull-right">立即报名</button>';
									}else{
										var _sign = '<button class="mui-pull-right">报名已结束</button>';
									}
									Html += '<li>';
									Html += '<a href="'+"<?php echo U('detail');?>?activity_id="+item.activity_id+'">';
									Html += '<div class="consult-body mui-clearfix">';
									Html += '<div class="title">'+item.title+'</div>';
									Html += '<div class="cont mui-clearfix">';
									Html += '<img src="'+item.activity_logo+'">';
									Html += '<p>主讲导师：'+item.teacher+'</p>';
									Html += '<p>主要内容：'+item.course+'</p>';
									Html += '<p>上课时间：'+item.course_time+'</p>';
									Html += '</div>';
									Html += '</div>';
									Html += '<div class="mui-table">';
									Html += '<div class="mui-table-cell">'+item.lunch_time+_sign+'</div>';
									Html += '</div>';
									Html += '</a>';
									Html += '</li>';
									table.insertAdjacentHTML('beforeend', Html);
								});
							} else {
								hasMore = true;
							}
							mui('#pullrefresh').pullRefresh().endPullupToRefresh(hasMore);
						}
					})
				}, 1500);
			}

			mui('.mui-scroll').on('tap', 'a', function(e) {
				var targetTab = this.getAttribute('href');
				mui.openWindow({
					url: targetTab,
					id: targetTab,
				})
			});
		</script>
	</body>

</html>