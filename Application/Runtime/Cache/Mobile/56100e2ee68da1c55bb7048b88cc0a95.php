<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html >
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title>枫FENGZAKKA-所有分类</title>
<meta http-equiv="keywords" content="<?php echo ($tpshop_config['shop_info_store_keyword']); ?>" />
<meta name="description" content="<?php echo ($tpshop_config['shop_info_store_desc']); ?>" />
<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
<link rel="stylesheet" type="text/css" href="/Template/mobile/new/Static/css/public.css"/>
<link rel="stylesheet" type="text/css" href="/Template/mobile/new/Static/css/catalog.css"/>
<link rel="stylesheet" type="text/css" href="/Public/mobile/css/mui.min.css" />
<link rel="stylesheet" type="text/css" href="/Public/mobile/css/css.css" />
<script src="/Public/mobile/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/Template/mobile/new/Static/js/jquery.js"></script>
<script src="/Public/js/global.js"></script>
<style>
.goods_nav{ width:30%; float:right; right:5px; overflow:hidden; position:fixed;margin-top:25px; z-index:9999999}
</style>
</head>
<body>
<!--分类切换--> 
<div class="container">    
  <div class="category-box">
    <div class="category1" style="outline: none;" tabindex="5000">
      <ul class="clearfix" style="padding-bottom:50px;">
      <?php $m = '0'; ?>
	    <?php if(is_array($goods_category_tree)): foreach($goods_category_tree as $k=>$vo): if($vo[level] == 1): ?><li <?php if($m == 0): ?>class='cur' style='margin-top:46px'<?php endif; ?> data-id="<?php echo ($m++); ?>"><?php echo (getSubstr($vo['mobile_name'],0,12)); ?></li><?php endif; endforeach; endif; ?>
      </ul>
    </div>
    <div class="category2" style=" outline: none;" tabindex="5001">
    <?php $j = '0'; ?>
    <?php if(is_array($goods_category_tree)): foreach($goods_category_tree as $kk=>$vo): ?><dl style="margin-top:46px;padding-bottom:50px;<?php if($j == 0): ?>display:block;<?php else: ?>display:none;<?php endif; ?>" data-id="<?php echo ($j++); ?>"> 
        <!-- <span>            
          <?php $pid =400;$ad_position = M("ad_position")->cache(true,TPSHOP_CACHE_TIME)->getField("position_id,position_name,ad_width,ad_height");$result = D("ad")->where("pid=$pid  and enabled = 1 and start_time < 1501999200 and end_time > 1501999200 ")->order("orderby desc")->cache(true,TPSHOP_CACHE_TIME)->limit("1")->select(); if(!in_array($pid,array_keys($ad_position)) && $pid) { M("ad_position")->add(array( "position_id"=>$pid, "position_name"=>CONTROLLER_NAME."页面自动增加广告位 $pid ", "is_open"=>1, "position_desc"=>CONTROLLER_NAME."页面", )); delFile(RUNTIME_PATH); } $c = 1- count($result); if($c > 0 && $_GET[edit_ad]) { for($i = 0; $i < $c; $i++) { $result[] = array( "ad_code" => "/Public/images/not_adv.jpg", "ad_link" => "/index.php?m=Admin&c=Ad&a=ad&pid=$pid", "title" =>"暂无广告图片", "not_adv" => 1, "target" => 0, ); } } foreach($result as $key=>$v): $v[position] = $ad_position[$v[pid]]; if($_GET[edit_ad] && $v[not_adv] == 0 ) { $v[style] = "filter:alpha(opacity=50); -moz-opacity:0.5; -khtml-opacity: 0.5; opacity: 0.5"; $v[ad_link] = "/index.php?m=Admin&c=Ad&a=ad&act=edit&ad_id=$v[ad_id]"; $v[title] = $ad_position[$v[pid]][position_name]."===".$v[ad_name]; $v[target] = 0; } ?><a href="<?php echo ($v["ad_link"]); ?>" target="_blank">
            <em>全部>></em>
                <img src="<?php echo ($v[ad_code]); ?>" title="<?php echo ($v[title]); ?>" style="<?php echo ($v[style]); ?>">
            </a><?php endforeach; ?>               
		</span>	 -->	
        <?php if(is_array($vo["tmenu"])): foreach($vo["tmenu"] as $k2=>$v2): ?><dt><a href="<?php echo U('Mobile/Goods/goodsList',array('id'=>$v2[id]));?>" ><?php echo ($v2['name']); ?></a></dt> 
        <dd> 
	        <div class="fenimg">
		        <?php if(is_array($v2["sub_tmenu"])): foreach($v2["sub_tmenu"] as $key=>$v3): ?><div class="fen">
			        	<a href="<?php echo U('Mobile/Goods/goodsList',array('id'=>$v3[id]));?>"><?php echo ($v3['name']); ?></a> 
			        </div><?php endforeach; endif; ?>
	    	</div>
        </dd><?php endforeach; endif; ?>
      </dl><?php endforeach; endif; ?>
    </div>
  </div>
</div>
<nav class="mui-bar mui-bar-tab" id="bar-tab">
      <a class="mui-tab-item " href="<?php echo U('Index/index');?>">
        <span class="mui-icon"><img src="/Public/mobile/img/bar_1.png"/></span>
        <span class="mui-tab-label">首页</span>
      </a>
      <a class="mui-tab-item mui-active" href="<?php echo U('Goods/categoryList');?>">
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
    <script>
      mui('.mui-bar-tab').on('tap', 'a', function(e) {
        var targetTab = this.getAttribute('href');
        mui.openWindow({
          url: targetTab,
          id: targetTab,
        })
      });
    </script>
<script>

$(function () {
    //滚动条
    $(".category1,.category2").niceScroll({ cursorwidth: 0,cursorborder:0 });

    //图片延迟加载
 	//$(".lazyload").scrollLoading({ container: $(".category2") });
    //$('.category-box').height($(window).height());

    //点击切换2 3级分类
	var array=new Array();
	$('.category1 li').each(function(){ 
		array.push($(this).position().top-0);
	});
	
	$('.category1 li').click(function() {
		var index=$(this).index();
		$('.category1').delay(200).animate({scrollTop:array[index]},300);
		$(this).addClass('cur').siblings().removeClass();
		$('.category2 dl').eq(index).show().siblings().hide();
        $('.category2').scrollTop(0);
	});

});
</script>
<script src="/Template/mobile/new/Static/js/jquery.nicescroll.min.js"></script> 
</body>
</html>