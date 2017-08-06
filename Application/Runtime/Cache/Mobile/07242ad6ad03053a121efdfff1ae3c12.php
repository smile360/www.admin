<?php if (!defined('THINK_PATH')) exit();?><title><?php echo ($data['title']); ?></title>
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
	
		<div class="mui-content mui-scroll-wrapper activity-detail" id="pullrefresh">
			<div class="mui-scroll">
				<div class="title">
					<h5><?php echo ($data['title']); ?></h5>
					<p class="mui-text-center"><span><?php echo ($data["author"]); ?>&nbsp;&nbsp;&nbsp;<?php echo ($data['publish_time']); ?></span></p>
				</div>
				<div class="content">
					<img src="<?php echo ($data["thumb"]); ?>" />
					<p><?php echo ($data["content"]); ?></p>
				</div>
				<div class="copyright mui-text-center">
					文章版权所有，如需转载请与<a href="www.zbzixun.com">珠宝咨询网</a>或作者本人联系
				</div>

				<div class="mui-table zan">
					<div class="mui-table-cell mui-text-center" id="tap-zan">
						<?php if($_islike == 1): ?><i class="iconfont" id="zan-icon" style="font-size: 18px;color:#fc5454">&#xe501;</i>
						<?php else: ?>
							<i class="iconfont" id="zan-icon" style="font-size: 18px;">&#xe501;</i><?php endif; ?>
						&nbsp;&nbsp;点赞(<span id="zan-num"><?php echo ($likeNum); ?></span>)
					</div>
					<div class="mui-table-cell mui-text-center" id="tap-collect">
						<?php if($_iscollect == 1): ?><i class="iconfont" id="collect-icon" cid="<?php echo ($data["article_id"]); ?>" style="font-size: 16px;color:#fc5454">&#xf0003;</i>
						<?php else: ?>
							<i class="iconfont" id="collect-icon" cid="<?php echo ($data["article_id"]); ?>" style="font-size: 16px;">&#xf0003;</i><?php endif; ?>
						&nbsp;&nbsp;收藏(<span id="collect-num"><?php echo ($collectNum); ?></span>)
					</div>
					<div class="mui-table-cell mui-text-center" id="tap-comment">
						<i class="iconfont" style="font-size: 16px;vertical-align: middle;">&#xe629;</i>
						&nbsp;&nbsp;评论(<?php echo ($commentNum); ?>)
					</div>
				</div>
				<ul class="mui-table-view comments">
					<?php if(is_array($comment)): $i = 0; $__LIST__ = $comment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="mui-table-view-cell mui-media">
							<img class="mui-media-object mui-pull-left" src="<?php echo ((isset($vo["head_pic"]) && ($vo["head_pic"] !== ""))?($vo["head_pic"]):'/Public/mobile/img/pl_head.png'); ?>">
							<div class="mui-media-body">
								<div class="username"><?php echo ($vo["nickname"]); ?></div>
								<p class="comments-c"><?php echo ($vo["content"]); ?></p>
								<div class="mui-table">
									<div class="mui-table-cell"><?php echo ($vo["add_time"]); ?></div>
									<!-- <div class="mui-table-cell mui-text-right"><span>回复</span></div> -->
								</div>
							</div>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
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
					var table = document.body.querySelector('.comments');
					var cells = document.body.querySelectorAll('.mui-media').length;
					mui.ajax("<?php echo U('commentList');?>", {
						data: {
							start: cells,
							article_id:<?php echo ($data['article_id']); ?>,
						},
						dataType: 'json',
						type: 'get',
						timeout: 10000,
						async: true,
						success: function(data) {
							if(data.code ==200) {
								var Html = "";
								mui.each(data.result, function(i, item) {
									var _pic = item.head_pic ? item.head_pic : "/Public/mobile/img/pl_head";
									Html += '<li class="mui-table-view-cell mui-media">';
									Html += '<img class="mui-media-object mui-pull-left" src="'+_pic+'">';
									Html += '<div class="mui-media-body">';
									Html += '<div class="username">'+item.nickname+'</div>';
									Html += '<p class="comments-c">'+item.content+'</p>';
									Html += '<div class="mui-table">';
									Html += '<div class="mui-table-cell">'+item.add_time+'</div>';
									Html += '</div>';
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

			//赞
			var is_zan = (<?php echo ($_islike); ?> == 0 ) ? true : false;
			document.getElementById("tap-zan").addEventListener('tap', function() {
				if(is_zan){
					mui.post("<?php echo U('isLike');?>", {
						id: <?php echo ($data['article_id']); ?>,
					}, function(data) {
						if(data.code == 200) {
							document.getElementById("zan-icon").innerHTML = "&#xe501;"
							document.getElementById("zan-icon").style.color = "#fc5454";
							document.getElementById("zan-num").innerText = data.result;
							is_zan = false;
						} else {
							mui.toast("收藏失败失败，请稍后再试");
							return false;
						}
					});
				}else{
					mui.toast("您已点赞过！");
				}	
			})
			//收藏
			document.getElementById("tap-collect").addEventListener('tap', function() {
					mui.post("<?php echo U('isCollect');?>", {
						id: document.getElementById("collect-icon").getAttribute('cid'),
					}, function(data) {
						if(data.code == 200) {
							if(data.act == 'add'){
								document.getElementById("collect-icon").innerHTML = "&#xf0003;"
								document.getElementById("collect-icon").style.color = "#fc5454";
								mui.toast("收藏成功！");
							}else{
								document.getElementById("collect-icon").innerHTML = "&#xf0003;"
								document.getElementById("collect-icon").style.color = "#ccc";
								mui.toast("取消收藏成功！");
							}
							document.getElementById("collect-num").innerText = data.num;
							document.getElementById("collect-num").innerText = data.num;
						} else {
							mui.toast("收藏失败失败，请稍后再试");
							return false;
						}
					});

				})
				//评论
			document.getElementById("tap-comment").addEventListener('tap', function() {
				location.href = "<?php echo U('comment',array('article_id'=>$data['article_id'],'title'=>$data['title']));?>"
			})
		</script>
	</body>

</html>