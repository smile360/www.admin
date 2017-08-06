<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8" />
		<title>咨询详情</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/css.css" />
	</head>

	<body>
		<div class="mui-content consult-detail mui-scroll-wrapper" id="pullrefresh">
			<div class="mui-scroll">
				<ul class="mui-table-view">
					<li class="mui-table-view-cell mui-media line">
						<img class="mui-media-object mui-pull-left" src="<?php echo ((isset($consult['head_pic']) && ($consult['head_pic'] !== ""))?($consult['head_pic']):'/Public/mobile/img/default_head.png'); ?>">
						<div class="mui-media-body">
							<div class="nick"><?php echo ($consult['nickname']); ?></div>
							<div class="name"><?php echo (date("Y-m-d H:i",$consult['createtime'])); ?>
								<?php if($consult["status"] == 1): ?><span class="mui-pull-right al">已解决咨询</span>
								<?php else: ?>
									<span class="mui-pull-right wait">待解决咨询</span><?php endif; ?>
							</div>
						</div>
					</li>
					<div class="require">
						<h5><?php echo ($consult['tittle']); ?></h5>
						<?php if(is_array($consult['path'])): foreach($consult['path'] as $key=>$v): ?><img src="<?php echo ($v); ?>" alt="" width="100%" /><?php endforeach; endif; ?>
						<p><?php echo ($consult['detail']); ?></p>
					</div>

				</ul>

				<div class="mui-table talk mui-text-right">
					<?php if($like == 1): ?><button class="zan zan-act" disabled="disabled" id="zan" cid="<?php echo ($consult["consult_id"]); ?>" style="opacity:1">赞 (<span id="zan-num"><?php echo ($consult['like']); ?></span>)</button>
					<?php else: ?>
						<button class="zan" id="zan" cid="<?php echo ($consult["consult_id"]); ?>">赞 (<span id="zan-num"><?php echo ($consult['like']); ?></span>)</button><?php endif; ?>
					<?php if($collect == 1): ?><button class="collect collect-act" id="collect" cid="<?php echo ($consult["consult_id"]); ?>">收藏 (<span id="collect-num"><?php echo ($consult['collect']); ?></span>)</button>
					<?php else: ?>
						<button class="collect" id="collect" tittle="<?php echo ($consult["tittle"]); ?>" cid="<?php echo ($consult["consult_id"]); ?>">收藏 (<span id="collect-num"><?php echo ($consult['collect']); ?></span>)</button><?php endif; ?>
				</div>

				<div class="mui-table" id="hbshf">
					<div id="Comment4Admin">
						
					</div>

					<div id="hot-leave" class="hot-leave">
						<div class="title">评论(<?php echo ($count); ?>)
							<a class="mui-pull-right common-comments-input">
								<img src="/Public/mobile/img/write.png" />
							</a>
						</div>
						<ul class="mui-table-view hot-leave-list" id="hot-leave-list">
							
						</ul>
					</div>

				</div>
			</div>

		</div>
		<div class="replay-input" id="replay-input">
			<form id="replayFrm" action="<?php echo U('Comment/Handle');?>" method="post">
				<input id="parentName" type="hidden" name="parentName" value="" />
				<input id="consultId" type="hidden" name="consultId" value="<?php echo ($_GET['id']); ?>" />
				<input id="parentId" type="hidden" name="parentId" value="" />
				<input id="type" type="hidden" name="type" value="0" />
				<input type="text" name="comment" id="input-in" placeholder="回复" />
				<button type="button" id="replay-btn">回复</button>
			</form>
		</div>
		<script src="/Public/mobile/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="/Public/mobile/js/jquery-3.0.0.min.js" type="text/javascript" charset="utf-8"></script>
		
		<script type="text/javascript">
			var selfID = "<?php echo ($_SESSION['user']['user_id']); ?>";
			var consult_id = "<?php echo ($consult['user_id']); ?>";

			$.post("<?php echo U('Comment/Comment4Admin');?>",{consultId:"<?php echo ($_GET['id']); ?>"},
					function (data){
						var html = '';
						if(data == 'NULL'){
							/*应该设置无评论样式*/
							html += '<div class="hbs">';
							html += '<div class="content">';
							html += '<p style="text-align: center">暂无专家评论';
							html += '</p></div></div>';
							html += '<div class="reply-hbs"></div>';
							$("#Comment4Admin").html(html);
							return ;
						}
						data = eval("("+data+")");
						for (var i = 0; i < data.length; i++) {
							html += '<div class="hbs">';
							html += '<div class="title">'+data[i].nickname+' 回复：</div>';
							html += '<div class="content">';
							html += '<p>'+data[i].comment+'</p>';
							html += '</div>';
							html += '<div class="mui-text-right">';

							if(selfID == consult_id){
								html += '<a data-id="'+data[i].comment_id+'" data-type="1" href="javascript:void(0)" class="hf" data-name="'+data[i].nickname+'">';
								html += '回复</a>';
							}
							
							html += '</div>';
							html += '</div><div class="reply-hbs">';
							//回复循环
							for (var j=0; j < data[i].reply.length;j++) {
								html += '<div class="head">';
								html += ' '+data[i].reply[j].nickname+' <span>回复</span> ';
								html += ' '+data[i].reply[j].parent_name+' <span>回复</span> ';
								html += '</div>';
								html += '<div class="content">';
								html += '<p>'+data[i].reply[j].comment+'</p>';
								html += '</div>';
								html += '<div class="mui-text-right">';
								if(selfID == consult_id){
									html += '<a data-id="'+data[i].comment_id+'" data-type="1" href="javascript:void(0)" class="hf" data-name="'+data[i].reply[j].nickname+'">';
									html += '回复</a>';
								}
								html += '</div>';
							};
							html += '</div>';
						};

						$("#Comment4Admin").html(html);
					});
										
				var isIOS = (/iphone|ipad/gi).test(navigator.appVersion);
				if (isIOS) {
					$('.replay-input').on('focus', '#input-in', function () {
						$('#replay-input').addClass('pos-rel');
					}).on('focusout', 'input', function () {
						$('#replay-input').removeClass('pos-rel');
					 });
				}					
		</script>

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
			function addZERO(value){
				if(value < 10){
					value = '0'+value.toString();
				}
				return value;
			}

			function formatDate(now) {
				var d = new Date(now*1000);  
				var year = d.getYear()+1900;
				var month = addZERO(d.getMonth()+1); 
				var date = addZERO(d.getDate()); 
				var hour = addZERO(d.getHours()); 
				var minute = addZERO(d.getMinutes()); 
				var second = addZERO(d.getSeconds());

				return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second; 
			}

			function pullupRefresh() {
				$.post("<?php echo U('Comment/Comment4User');?>",{consultId:"<?php echo ($_GET['id']); ?>"},
					function (data){
						var Html = '';
						if(data == 'NULL'){
							return ;
						};
						data = eval("("+data+")");
						for (var i = 0; i < data.length; i++) {
							Html += '<li class="mui-table-view-cell mui-media media-detail mui-active">';
							Html += '<img class="mui-media-object mui-pull-left" src="'+data[i].head_pic+'">';
							Html += '<div class="mui-media-body">';
							Html += '<div class="mui-table">';
							Html += '<div class="mui-table-cell">'+data[i].nickname+'</div>';
							Html += '</div>';
							Html += '<p class="rp">'+data[i].comment+'</p>';
							Html += '<div class="date">'+ formatDate(data[i]['createtime']) +'<a data-id="'+data[i].comment_id+'" data-type="0" data-name="'+data[i].nickname+'" class="mui-pull-right comments-input">回复</a></div>';
							Html += '</div>';
							Html += '<div class="reply-hbs" style="padding:0 0 0 60px;margin-bottom: 0px;">';

							for(var j=0;j<data[i].reply.length;j++) {
								Html += '<div class="head"><img src="'+data[i].reply[j].head_pic+'" /> '+data[i].reply[j].nickname+' <span>回复</span> '+data[i].reply[j].parent_name+'</div>';
								Html += '<div class="content">';
								Html += '<p>'+data[i].reply[j].comment+'</p>';
								Html += '</div>';
								Html += '<div class="date" style="padding-left: 35px;">'+ formatDate(data[i].reply[j]['createtime']) +'<a data-id="'+data[i].comment_id+'" data-type="0" data-name="'+data[i].reply[j].nickname+'" class="mui-pull-right comments-input" style="padding-right: 15px;">回复</a></div>';
							}
							Html += '</div>';

							Html += '</li>';
						};
						$("#hot-leave-list").html(Html);
					});
				hasMore = true;
				mui('#pullrefresh').pullRefresh().endPullupToRefresh(hasMore);
			}
			mui("#hot-leave").on("tap",".common-comments-input",function() {
				document.getElementById("input-in").value = '';
				document.getElementById("replay-input").style.display = "block";
				document.getElementById("input-in").focus();
				document.getElementById("input-in").setAttribute('placeholder', '回复主题');
				$("#parentId").val("");
				$("#type").val("0");
			});
			
			
			//回复
			mui("#hot-leave-list").on('tap', '.comments-input', function() {
				document.getElementById("input-in").value = '';
				document.getElementById("replay-input").style.display = "block";
				document.getElementById("input-in").focus();
				var name = this.getAttribute('data-name');
				document.getElementById("input-in").setAttribute('placeholder', '回复@' + name);
				$("#parentName").val("@"+name);

				var type = $(this).attr("data-type");
				var id = $(this).attr("data-id");
				$("#parentId").val(id);
				$("#type").val(type);
			})			
			mui("#hbshf").on('tap', '.hf', function() {
				document.getElementById("input-in").value = '';
				document.getElementById("replay-input").style.display = "block";
				document.getElementById("input-in").focus();
				var name = this.getAttribute('data-name');
				document.getElementById("input-in").setAttribute('placeholder', '回复@' + name);
				$("#parentName").val("@"+name);

				var type = $(this).attr("data-type");
				var id = $(this).attr("data-id");
				$("#parentId").val(id);
				$("#type").val(type);
			})									
			document.getElementById("replay-btn").addEventListener('tap', function() {
				var rcontent = document.getElementById("input-in").value;

				if(rcontent == "") {
					mui.toast("回复内容不能为空");
					return false;
				}

				/*Form表单提交*/
				$("#replayFrm").submit();
			})

			//赞
			var is_zan = true;
			document.getElementById("zan").addEventListener('tap', function() {
				if(is_zan) {
					mui.post("<?php echo U('LikeThis');?>", {
						consultId: document.getElementById("zan").getAttribute('cid'),
					}, function(data) {
						if(data.code == 200) {
                            document.getElementById('zan').classList.add('zan-act');
							document.getElementById("zan-num").innerText = data.num;
							is_zan = false;
						} else {
							mui.toast("点赞失败，请稍后再试");
							return false;
						}
					});
				}
			})
			//收藏
			// var is_collect = true;
			document.getElementById("collect").addEventListener('tap', function() {
				// if(is_collect) {
					mui.post("<?php echo U('collectThis');?>", {
						id: document.getElementById("collect").getAttribute('cid'),
						tittle: document.getElementById("collect").getAttribute('tittle'),
					}, function(data) {
						if(data.code == 200) {
							if(data.act == 'add'){
								document.getElementById('collect').classList.add('collect-act');
								mui.toast("收藏成功！");
							}else{
								document.getElementById("collect").classList.remove('collect-act');
								mui.toast("取消收藏成功！");
							}
							document.getElementById("collect-num").innerText = data.num;
							// is_collect = false;
						} else {
							mui.toast("点赞失败，请稍后再试");
							return false;
						}
					});
				// }
			})
		</script>
	</body>

</html>