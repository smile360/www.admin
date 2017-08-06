<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html>

	<head>
		<meta charset="utf-8" />
		<title>账户设置</title>
		<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/mobile/css/css.css" />
	</head>

	<body>
		<div class="mui-content consult">
			<div id="slider" class="mui-slider mui-fullscreen">
				<div id="sliderSegmentedControl" class="mui-slider-indicator mui-segmented-control-inverted mui-segmented-control">
					<a class="mui-control-item c-line mui-active" href="#item1mobile">
						修改密码
					</a>
					<a class="mui-control-item" href="#item2mobile">
						修改绑定手机
					</a>
				</div>
				<div class="mui-slider-group modify-psd">
					<div id="item1mobile" class="mui-slider-item mui-control-content mui-active">

						<div class="mui-table-view">
							<div class="mui-input-group">
								<div class="mui-input-row">
									<label>原密码</label>
									<input id="old-psd" type="password" placeholder="请输入原密码" />
								</div>
								<div class="mui-input-row">
									<label>新密码</label>
									<input id="new-psd" type="password" placeholder="请输入新密码" />
								</div>
								<div class="mui-input-row">
									<label>确认密码</label>
									<input id="confirm-psd" type="password" placeholder="请再次输入新密码" />
								</div>
							</div>
							<button type="button" id="change-psd" class="mui-btn mui-btn-danger mui-btn-block">确定</button>
						</div>

					</div>
					<div id="item2mobile" class="mui-slider-item mui-control-content">
						<div class="mui-table-view" id="step-1">
							<div class="mui-input-group">
								<div class="mui-input-row">
									<label>手机号</label>
									<input id="tel-1" type="text" placeholder="请输入原手机号" />
								</div>
								<div class="mui-input-row cl">
									<label>验证码</label>
									<input id="code-1" class="code" type="text" placeholder="请输入验证码" />
									<button class="sendcode mui-text-center" onclick="sendcode('tel-1')">获取验证码</button>
								</div>
							</div>
							<button type="button" id="c-change" class="mui-btn mui-btn-danger mui-btn-block">验证旧手机</button>
						</div>
						<div class="mui-table-view" id="step-2" style="display: none;">
							<div class="mui-input-group">
								<div class="mui-input-row">
									<label>新手机号</label>
									<input id="tel-2" type="text" placeholder="请输入新手机号" />
								</div>
								<div class="mui-input-row cl">
									<label>验证码</label>
									<input id="code-2" class="code" type="text" placeholder="请输入验证码" />
									<button class="sendcode mui-text-center" onclick="sendcode('tel-2')">获取验证码</button>
								</div>
							</div>
							<button type="button" id="c-confirm" class="mui-btn mui-btn-danger mui-btn-block">确认修改绑定</button>
						</div>

					</div>
				</div>
			</div>
		</div>
		<script src="/Public/mobile/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript">
			var oldpsd = document.getElementById("old-psd").value;
			var newpsd = document.getElementById("new-psd").value;
			var confirmpsd = document.getElementById("confirm-psd").value;
			document.getElementById("change-psd").addEventListener('tap', function() {
				if(newpsd != confirmpsd) {
					mui.toast('新密码两次输入不一致');
					return false;
				}
				mui.post("<?php echo U('User/accountSet');?>", {
					oldpsd: oldpsd,
					newpsd: newpsd,
					confirmpsd: confirmpsd,
				}, function(data) {
					if(data.code == 200) {
						mui.toast("密码修改成功");
						setTimeout(function() {
							location.href = "<?php echo U('index');?>";
						}, 1000);
					} else {
						mui.toast( data.msg );
						return false;
					}
				});

			})

			function sendcode(obj) {
				var _type = obj == 'tel-1' ? 'resetphone':'bind';
				var phone = document.getElementById(obj);
				var myreg = /^1[3|4|5|7|8]\d{9}$/;
				if(!myreg.test(phone.value)) {
					mui.toast('手机号格式不正确！');
					return false;
				}
				mui.post("<?php echo U('Api/SendCode');?>", {
					phone: phone.value,
					type: _type
				}, function(data) {
					if(data.msg == 200) {
						time();
						mui.toast("短信发送成功");
						return false;
					} else {
						mui.toast( data.msg );
						return false;
					}
				});
			}
			var tel1 = document.getElementById("tel-1");
			var tel2 = document.getElementById("tel-2");
			var code1 = document.getElementById("code-1");
			var code2 = document.getElementById("code-2");
			document.getElementById("c-change").addEventListener('tap', function() {
				if(code1.value == "") {
					mui.toast("验证码不能为空");
					return false;
				}
				mui.post("<?php echo U('Api/codeVerify');?>", {
					phone: tel1.value,
					code: code1.value,
					findpwd :'findpwd',
				}, function(data) {
					if(data.msg == 200) {
						time();
						mui.toast("验证成功");
						setTimeout(function() {
							document.getElementById("step-2").style.display = "block";
							document.getElementById("step-1").style.display = "none";
							return false;
						}, 1000);
					} else {
						mui.toast(data.msg);
						return false;
					}
				});

			});
			document.getElementById("c-confirm").addEventListener('tap', function() {
				if(code2.value == "") {
					mui.toast("验证码不能为空");
					return false;
				}
				mui.post("<?php echo U('Api/codeVerify');?>", {
					phone: tel2.value,
					code: code2.value,
					findpwd :'resetphone',
				}, function(data) {
					if(data.msg == 200) {
						time();
						mui.toast("验证成功");
						setTimeout(function() {
							location.href = "<?php echo U('index');?>";
							return false;
						}, 1000);
					} else {
						mui.toast(data.msg);
						return false;
					}
				});

			});
			var wait = 60;
			function time() {
				if(wait == 0) {
					sendcode.disabled = false;
					sendcode.innerHTML = "获取验证码";
					wait = 60;
				} else {
					sendcode.disabled = true;
					sendcode.innerHTML = wait + "S";
					wait--;
					setTimeout(function() {
						time()
					}, 1000)
				}
			}

		</script>

	</body>

</html>