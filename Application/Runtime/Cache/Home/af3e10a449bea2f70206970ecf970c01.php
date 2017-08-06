<?php if (!defined('THINK_PATH')) exit();?>
<!--------头部开始-------------->
<!DOCTYPE html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>深圳珠宝咨询网</title>
		<link rel="stylesheet" type="text/css" href="/Public/home/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="/Public/home/css/css.css" />
		<link rel="stylesheet" href="/Template/pc/default/Static/css/index.css" type="text/css">
		<link rel="stylesheet" href="/Template/pc/default/Static/css/page.css" type="text/css">
        <link rel="stylesheet" href="/Template/pc/default/Static/css/jqzoom.css" type="text/css">
        <link rel="stylesheet" href="/Template/pc/default/Static/css/location.css" type="text/css">
		<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
		<script src="/Public/js/global.js"></script>
		<script src="/Public/js/pc_common.js"></script>
		<script src="/Template/pc/default/Static/js/jquery.jqzoom.js"></script>
	</head>

	<body>
		<div class="top">
			<div class="container">
				<div class="pull-right">
					<!-- if 登陆
					<a href="p_info.html" class="color66">zhangsan</a>	
					<a href="" class="color99">退出</a>-->
					<?php if(empty($_SESSION['user'])): ?><a class="color66 text-center" href="<?php echo U('User/login');?>">登陆</a>
						<a class="color66 text-center reg" href="<?php echo U('User/reg');?>">注册</a>
					<?php else: ?>
						<a href="<?php echo U('Home/Cart/cart');?>" >购物车</a>
						<a href="<?php echo U('User/index');?>" class="color66"><?php echo ($_SESSION['user']['nickname']); ?></a>	
						<a href="<?php echo U('User/logout');?>" class="color99">退出</a><?php endif; ?>
						
				</div>
			</div>
		</div>

		<div class="logo">
			<div class="container">
				<div class="pull-left">
					<a href="/"><img src="/Public/home/img/logo.png" alt="logo" /></a>
				</div>
				<form class="search pull-right" action="<?php echo U('Article/search');?>" method="post" onsubmit="return checkseach()">
					<button type="submit" class="pull-right submit"><span class="glyphicon glyphicon-search"></span></button>
					<input type="text" name="seach" id="seach" class="pull-right input" placeholder="请输入搜索关键字">
				</form>
				<a href="<?php echo U('User/consult_result');?>" class="btn btn-default pull-right">咨询结果</a>
				<a href="<?php echo U('User/consultation');?>" class="btn btn-default pull-right">我要咨询</a>
			</div>
		</div>

		<div class="menu">
			<div class="container">
				<ul class="menu-list">
					<?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(empty($vo["id"])): ?><li>
								<a href="<?php echo U($vo['action']); ?>"><?php echo ($vo["name"]); ?></a>
							</li>
						<?php else: ?>
							<li>
								<a href="<?php echo U($vo['action'],'cat_id='.$vo['id']); ?>"><?php echo ($vo["name"]); ?></a>
							</li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
		</div>
<!--------头部结束-------------->
<div class="layout">
    <div class="breadcrumb-area">
        <a href="/">首页</a> >>
        <?php if(is_array($navigate_goods)): foreach($navigate_goods as $k=>$v): ?><a  href="<?php echo U('Goods/goodsList',array('id'=>$k));?>"><?php echo ($v); ?></a> >><?php endforeach; endif; ?>
        <span><?php echo ($goods["goods_name"]); ?></span>
    </div>
    <div class="layout pa-to-10">
        <!--商品图片轮播-->
        <div class="left-area">
            <div class="left-area-tb">
                <div class="pro-gallery-img">
                    <div class="jqzoom"> <img id="zoomimg" src="<?php echo (goods_thum_images($goods["goods_id"],400,400)); ?>" jqimg="<?php echo (goods_thum_images($goods["goods_id"],800,800)); ?>" id="bigImage" width="480px" height="480px" alt=""/> </div>
                </div>
                <!-- 修改的部分-start -->
                <div class="pro-gallery-area">
                    <div class="pro-gallery-nav">
                        <a href="javascript:;" class="pro-gallery-back next-left disabled"></a>
                        <div class="pro-gallery-thumbs">
                            <ul class="small-pic" id="pro-gallerys" style="left: 0px;">
                                <?php if(is_array($goods_images_list)): foreach($goods_images_list as $k=>$v): ?><li class="small-pic-li <?php if($k == 0): ?>current<?php endif; ?>">
                                    <a href="javascript:void(0);">
                                        <img src="<?php echo (get_sub_images($v,$v[goods_id],60,60)); ?>" data-img="<?php echo (get_sub_images($v,$v[goods_id],400,400)); ?>" data-big="<?php echo (get_sub_images($v,$v[goods_id],800,800)); ?>">
                                    </a>
                                    </li><?php endforeach; endif; ?>
                            </ul>
                        </div>
                        <a href="javascript:;" class="pro-gallery-forward next-right"></a>
                    </div>
                </div>
                <!-- 修改的部分-end -->
            </div>
        </div>
        <!--商品图片轮播 end-->

        <div class="right-area-num">
            <div class="right-area">
                <div class="right-area-le30">
                    <h1><?php echo ($goods["goods_name"]); ?></h1>
                    <div class="cpxq-explain">
                        <?php if($flash_sale['description'] != ''): echo ($flash_sale['description']); ?>
                            <?php else: ?>
                            <?php echo ($goods["goods_remark"]); endif; ?>
                    </div>
                </div>
            </div>
            <div class="right-area ma-to--1">

                <!--商品促销 start-->
                <?php if($goods['prom_type'] == 1): ?><div class="bef_fo2" style="font-size:14px; background-color:#F60; color:#FFFFFF; line-height:30px; position:absolute; width:100%">
                        <p style="background-color:#f72862">
                            <span style="font-size:20px; padding:0px 16px 0px 26px; vertical-align:middle; ">抢购价￥<?php echo ($goods['flash_sale']['price']); ?></span>
                            <img class="clock_w" src="/Template/pc/default/Static/images/lz.png"/><span id="surplus_text">300天 01时 01分 01秒</span> 后结束，请快购买
                        </p>
                    </div>
                    <script>
                        // 倒计时
                        function GetRTime2(){
                            //var text = GetRTime('2016/02/27 17:34:00');
                            var text = GetRTime('<?php echo (date("Y/m/d H:i:s",$goods['flash_sale']['end_time'])); ?>');
                            $("#surplus_text").text(text);
                        }
                        setInterval(GetRTime2,1000);
                    </script><?php endif; ?>
                <!--商品促销 end-->
                <div class="right-area-le30 pa-3-0-0-30">

                    <div class="pro-promotions-area">
                        <table class="promotions-tab" width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="60px" align="right">价&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格：</td>
                                <td>
                                    <p class="co-red fo-si-24 ma-le-6">
                                        ￥<span class="co-red fo-si-24" id="goods_price"><?php echo ($goods["shop_price"]); ?></span>
                                    </p>
                                </td>
                            </tr>
                            <?php if(($goods['shop_price'] >= ($goods['exchange_integral']/$point_rate)) AND $goods['exchange_integral'] > 0): ?><tr>
                                    <td width="60px" align="right">促销信息：</td>
                                    <td>
                                        <p class="co-red fo-si-24 ma-le-6">
                                            ￥<span class="co-red fo-si-24"><?php echo ($goods['shop_price']-$goods['exchange_integral']/$point_rate); ?>+<?php echo ($goods['exchange_integral']); ?>积分</span>
                                        </p>
                                    </td>
                                </tr><?php endif; ?>
                            <tr>
                                <td width="60px" align="right">商品编号：</td>
                                <td>
                                    <p class=" ma-le-6"><?php echo ($goods["goods_sn"]); ?> <a onClick="collect_goods(<?php echo ($goods["goods_id"]); ?>);" style="color:#00F;">收藏</a></p>
                                    <!--------用户评价-end---------------->
                                </td>
                            </tr>
                            <tr>
                                <td width="60px" align="right">商品评分：</td>
                                <td>
                                    <p class="ma-le-6">
                                        <span class="pf-comment"><i class="score"></i></span>
                                        &nbsp;<a href=""><span>(共&nbsp;<?php echo ($goods["comment_count"]); ?>&nbsp;条评论)</span></a></p>
                                </td>
                            </tr>

                            <tr>
                                <td width="60px" align="right">运&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;费：</td>
                                <td><p class="ma-le-6">满￥<?php echo ($freight_free); ?>免运费</p></td>
                            </tr>
                            <tr>
                                <td width="60px" align="right">服&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;务：</td>
                                <td><p class="ma-le-6">由深圳珠宝咨询网负责发货，并提供售后服务</p></td>
                                <td>
                                    <!-- JiaThis Button BEGIN 分享商品链接-->
                                    <div class="jiathis_style">
                                        <a href="http://www.jiathis.com/share" class="jiathis jiathis_txt" target="_blank"><img src="http://v3.jiathis.com/code_mini/images/btn/v1/jiathis1.gif" border="0" /></a>
                                    </div>
                                    <script>
                                        var jiathis_config = {
                                            url:"http://<?php echo ($_SERVER[HTTP_HOST]); ?>/Goods/goodsInfo?id=<?php echo ($_GET[id]); ?>",
                                            pic:"http://<?php echo ($_SERVER[HTTP_HOST]); echo (goods_thum_images($goods[goods_id],400,400)); ?>",
                                        }
                                        var is_distribut = getCookie('is_distribut');
                                        var user_id = getCookie('user_id');
                                        // 如果已经登录了, 并且是分销商
                                        if(parseInt(is_distribut) == 1 && parseInt(user_id) > 0)
                                        {
                                            jiathis_config.url = jiathis_config.url + "&first_leader="+user_id;
                                        }
                                        //alert(jiathis_config.url);
                                    </script>
                                    <script type="text/javascript" src="http://v3.jiathis.com/code_mini/jia.js" charset="utf-8"></script>
                                    <!-- JiaThis Button END 分享商品链接-->
                                </td>
                            </tr>
                            <tr>
                                <td width="60px" align="right" style="letter-spacing: -0.5px;">送&nbsp;&nbsp;货&nbsp;&nbsp;至：</td>
                                <td><p class="ma-le-6">
                                    <!-- 收货地址，物流运费 -start-->
                                    <ul class="list1">
                                        <li class="summary-stock">
                                            <div class="dd">
                                                <!--<div class="addrID"><div></div><b></b></div>-->
                                                <div class="store-selector">
                                                    <div class="text" style="width: 150px;"><div></div><b></b></div>
                                                    <div onclick="$(this).parent().removeClass('hover')" class="close"></div>
                                                </div>
                                                <span id="dispatching_msg" style="display: none;">有货</span>
                                                <select id="dispatching_select" style="display: none;">
                                                </select>
                                            </div>
                                        </li>
                                    </ul>
                                    <!-- 收货地址，物流运费 -end-->
                                    </p></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <form name="buy_goods_form" method="post" id="buy_goods_form" >
                <div class="right-area he315 ma-to--1" style="height: 268px;">
                    <div class="right-area-le30 pa-3-0-0-30">
                        <div class="pro-promotions-area">
                            <table class="promotions-tab he40-tr-td" width="100%" border="0" cellspacing="0" cellpadding="0">
                                <?php if(is_array($filter_spec)): foreach($filter_spec as $k=>$v): ?><tr>
                                        <td width="60px" align="right"><?php echo ($k); ?>：</td>
                                        <td>
                                            <ul class="choice-colol ma-le-6">
                                                <?php if(is_array($v)): foreach($v as $k2=>$v2): if($v2[src] != ''): ?><li>
                                                            <div class="color-sku-fant">
                                                                <div class="sku <?php if($k2 == 0 ): ?>sku-bo-blo<?php endif; ?>">
                                                    <a onClick="switch_spec(this)"><img src="<?php echo ($v2[src]); ?>"  onClick="$('#bigImage').attr('src','<?php echo ($v2[src]); ?>');$('#bigImage').attr('jqimg','<?php echo ($v2[src]); ?>');"/></a>
                                                    <input type="radio" style="display:none;" name="goods_spec[<?php echo ($k); ?>]" value="<?php echo ($v2[item_id]); ?>"  <?php if($k2 == 0 ): ?>checked="checked"<?php endif; ?>/>
                                                    <s></s>
                        </div>
                        <p><?php echo ($v2[item]); ?></p>
                    </div>
                    </li>
                    <?php else: ?>
                    <li>
                        <div class="sku  <?php if($k2 == 0 ): ?>sku-bo-blo<?php endif; ?>">
                        <a onClick="switch_spec(this);" class="choice-size"><?php echo ($v2[item]); ?></a>
                        <input type="radio"  style="display:none;" name="goods_spec[<?php echo ($k); ?>]" value="<?php echo ($v2[item_id]); ?>" <?php if($k2 == 0 ): ?>checked="checked"<?php endif; ?>  />
                        <s></s>
                </div>
                </li><?php endif; endforeach; endif; ?>
                </ul>
                </td>
                </tr><?php endforeach; endif; ?>
                <tr>
                    <td width="60px" align="right">购买数量：</td>
                    <td>
                        <ul class="choice-colol ma-le-6">
                            <li>
                                <a onClick="switch_num(-1);" class="choice-number fl" title="减" style="width:24px">-</a>
                                <input class="wi43 fl" type="text" value="1" name="goods_num" id="goods_num" readonly/>
                                <a onClick="switch_num(1);" class="choice-number fl" title="加" style="width:24px">+</a>
                            </li>
                        </ul>
                    </td>
                </tr>
                <?php if(empty($filter_spec)): ?><tr>
                        <td align="right" style="vertical-align:top">送&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;货：</td>
                        <td>由深圳珠宝咨询网从 全国最近点 发货，并提供售后服务。如有问题咨询在线客服~!<br/>上午08:30前完成下单下午送达,下午下单隔日第二天送达.</td>
                    </tr><?php endif; ?>
                </table>
        </div>
        <div class="join-a-shopping-cart fl" id="join_cart_now"><!-- location.href='<?php echo U('Home/Cart/cart');?>-->
            <a class="jrgwc-shopping-img jrgwc-shopping-img2" onClick="javascript:AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,1);">
                <span>立即购买</span>
            </a>
        </div>
        <div class="join-a-shopping-cart ma-le-210 fl" id="join_cart">
            <a class="jrgwc-shopping-img2" onClick="javascript:AjaxAddCart(<?php echo ($goods["goods_id"]); ?>,1,0);">
                <span>加入购物车</span>
            </a>
        </div>

        <div class="join-a-shopping-cart fl" id="no_join_cart_now" style="display: none"><!-- location.href='<?php echo U('Home/Cart/cart');?>-->
            <a class="jrgwc-shopping-img jrgwc-shopping-img2" onClick="" style="background: grey;border-color: grey;">
                <span>立即购买</span>
            </a>
        </div>
        <div class="join-a-shopping-cart ma-le-210 fl" id="no_join_cart" style="display: none">
            <a class="jrgwc-shopping-img2" onClick=""  style="background: grey;border-color: grey;">
                <span>加入购物车</span>
            </a>
        </div>

    </div>
</div>
<input type="hidden" name="goods_id" value="<?php echo ($goods["goods_id"]); ?>" />
</form>
</div>
</div>
</div>

<div style="display:none;" id="shopdilog">
    <div class="ui-popup ui-popup-modal ui-popup-show ui-popup-focus">
        <div i="dialog" class="ui-dialog">
            <div class="ui-dialog-arrow-a"></div>
            <div class="ui-dialog-arrow-b"></div>
            <table class="ui-dialog-grid">
                <tbody>
                <tr>
                    <td i="body" class="ui-dialog-body">
                        <div i="content" class="ui-dialog-content" id="content:1459321729418" style="width: 450px; height: auto;">
                            <div id="addCartBox" class="collect-public" style="display: block;">
                                <div class="colect-top">
                                    <i class="colect-icon"></i>
                                    <!--<i class="colect-fail"></i>-->
                                    <div class="conect-title">
                                        <span>添加成功</span>
                                        <div class="add-cart-btn fn-clear">
                                            <a href="javascript:;" class="ui-button ui-button-f80 fl go-shopping">继续购物</a>
                                            <a href="<?php echo U('Home/Cart/index');?>" class="ui-button ui-button-122 fl">去购物车结算</a>
                                        </div>
                                    </div>
                                </div>
                                <div id="watch">
                                    <span>购买此宝贝的用户还购买了：</span>
                                    <ul class="fn-clear buy-list">
                                        <li><a href="http://www.tp-shop.cn/item/201512CM310001099" class="watch-img" target="_blank"><img src="http://img01.fn-mart.com/C/item/2015/1231/201512C310000310/201512C310000245_134377335_200x200.jpg" alt=""></a><h4><a href="http://www.tp-shop.cn/item/201512CM310001099" target="_blank">巨圣冬季2015新款马丁靴女 中跟粗跟保暖英伦骑士风短靴流苏套筒圆头L854174068</a></h4><p><q class="fn-rmb">¥</q><strong class="fn-rmb-num">89</strong></p></li><li><a href="http://www.tp-shop.cn/item/201504CM150040438" class="watch-img" target="_blank"><img src="http://img02.fn-mart.com/C/item/2015/0415/201504C150036513/201504C150002841_817918286_200x200.jpg" alt=""></a><h4><a href="http://www.tp-shop.cn/item/201504CM150040438" target="_blank">百依恋歌 夏装新款大码显瘦短袖T恤女士韩版图案棉打底衫</a></h4><p><q class="fn-rmb">¥</q><strong class="fn-rmb-num">89</strong></p></li><li><a href="http://www.tp-shop.cn/item/201505CM210000863" class="watch-img" target="_blank"><img src="http://img03.fn-mart.com/C/item/2015/0521/201505C210000474/_328370304_200x200.jpg" alt=""></a><h4><a href="http://www.tp-shop.cn/item/201505CM210000863" target="_blank">洁婷透气双U日用纤巧棉柔卫生巾尝4片/包</a></h4><p><q class="fn-rmb">¥</q><strong class="fn-rmb-num">4.2</strong></p></li><li><a href="http://www.tp-shop.cn/item/201508CM240002876" class="watch-img" target="_blank"><img src="http://img04.fn-mart.com/C/item/2015/0824/201508C240000788/201508C240000750_047866800_200x200.jpg" alt=""></a><h4><a href="http://www.tp-shop.cn/item/201508CM240002876" target="_blank">美纳福 真空收纳压缩袋 2特大4大4中2手卷送电泵</a></h4><p><q class="fn-rmb">¥</q><strong class="fn-rmb-num">135</strong></p></li></ul>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td i="footer" class="ui-dialog-footer" style="display: none;">
                        <div i="statusbar" class="ui-dialog-statusbar" style="display: none;"></div>
                        <div i="button" class="ui-dialog-button"></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="layout ma-to-20 ov-hi">
    <div class="wi240 ov-hi fl">
        <div class="product-history-area">
            <div class="hi47 co-grey">
                <h3 class="fl browse-his">推荐商品</h3>
                <!--<span class="fr pa-15-16-0-0"><a class="del-dust cu-po"></a></span>-->
            </div>
            <div class="history-bott">
                <ul class="history-comm">
                    <?php
 $md5_key = md5("select * from `__PREFIX__goods` where is_recommend = 1 order by goods_id desc limit 10"); $result_name = $sql_result_v = S("sql_".$md5_key); if(empty($sql_result_v)) { $Model = new \Think\Model(); $result_name = $sql_result_v = $Model->query("select * from `__PREFIX__goods` where is_recommend = 1 order by goods_id desc limit 10"); S("sql_".$md5_key,$sql_result_v,1); } foreach($sql_result_v as $k=>$v): ?><li>
                            <div>
                                <p class="p-img-comm fl">
                                    <a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$v[goods_id]));?>"><img class="img-comm" src="<?php echo (goods_thum_images($v["goods_id"],60,60)); ?>" alt=""></a>
                                </p>
                                <p class="p-name-comm">
                                    <a href="<?php echo U('Home/Goods/goodsInfo',array('id'=>$v[goods_id]));?>"><?php echo ($v["goods_name"]); ?></a>
                                </p>
                                <p class="p-price-comm">
                                    <b>¥<?php echo ($v["shop_price"]); ?></b>
                                </p>
                            </div>
                        </li><?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="wi940 ov-hi fr">
        <div class="comm-param">
            <div class="parame-title">
                <div class="par-titles co-grey">
                    <ul class="commodity-xq tab_li">
                        <li class="current cliks" onClick="switch_tab(this,'tab1')">
                            <a>商品详情</a>
                        </li>
                        <li class="cliks" onClick="switch_tab(this,'tab2')">
                            <a>用户评价（<?php echo ($commentStatistics['c0']); ?>）</a>
                        </li>
                        <li class="cliks" onClick="switch_tab(this,'tab3')">
                            <a>规格参数</a>
                        </li>
                        <li class="cliks" onClick="switch_tab(this,'tab4')">
                            <a>售后服务</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-------------------商品详情------------------>
            <div class="parame-bott cliks-bn" style="display:" id="tab1">
                <div class="commodity-num pro-feature-area">
                    <div class="pro-disclaimer-area ma-to-20">
                        <?php echo (htmlspecialchars_decode($goods["goods_content"])); ?>
                    </div>
                </div>
            </div>
            <!-------------------规格参数------------------>
            <div class="parame-bott cliks-bn" style="display:none"  id="tab3">
                <div class="commodity-num pro-feature-area wi850">
                    <table class="modity-zhut" width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <th colspan="2"><h3>商品属性</h3></th>
                        </tr>
                        <?php if(is_array($goods_attr_list)): foreach($goods_attr_list as $k=>$v): ?><tr>
                                <td class="wi143"><?php echo ($goods_attribute[$v[attr_id]]); ?></td>
                                <td class="pa-5-0"><?php echo ($v[attr_value]); ?></td>
                            </tr><?php endforeach; endif; ?>
                    </table>
                </div>
            </div>
            <!-------------------售后服务------------------>
            <div class="parame-bott cliks-bn" style="display:none"  id="tab4">
                <div class="commodity-num pro-feature-area wi850 padding36-0-36-0">
                    <p class="fo-si-14 li-hi-1-5 fo-fa">
                        本产品全国联保，享受三包服务，质保期为：一年质保
                        <br>
                        如因质量问题或故障，凭厂商维修中心或特约维修点的质量检测证明，享受7天内退货，15日内换货，15日以上在质保期内享受免费保修等三包服务！
                        <br>
                        售后服务电话：400-830-8300
                        <br>
                        <span>深圳珠宝咨询网官网： <a href="http://zbzixun.com">http://zbzixun.com</a></span>
                        <br>
                    </p>
                </div>
            </div>
            <!-------------------用户评价------------------>
            <div class="parame-bott ov-hi" style="display:none"  id="tab2">
                <div class="evaluation-top fo-fa di-in-bl">
                    <div class="eval-le1 fl wi146 te-al">
                        <span><b><?php echo ($commentStatistics['rate1']); ?></b>%</span>
                        <em>好评度</em>
                    </div>
                    <div class="eval-le2 fl wi123 pa-to-7">
                        <dl>
                            <dt>好评<em>(<?php echo ($commentStatistics['rate1']); ?>%)</em></dt>
                            <dd><s style=" width:94%"></s></dd>
                        </dl>
                        <dl>
                            <dt>中评<em>(<?php echo ($commentStatistics['rate2']); ?>%)</em></dt>
                            <dd><s style=" width:2%"></s></dd>
                        </dl>
                        <dl>
                            <dt>差评<em>(<?php echo ($commentStatistics['rate3']); ?>%)</em></dt>
                            <dd><s style=" width:4%"></s></dd>
                        </dl>
                    </div>
                    <div class="eval-le3 fl wi516">
                        <dl>
                            <dt>买家评论事项：购买后有什么问题, 满意, 或者不不满, 都可以在这里评论出来, 这里评论全部源于真实的评论.</dt>
                        </dl>
                    </div>
                    <div class="eval-le4 fl wi150 pa-to-43 te-al">
                        <a href="<?php echo U('Home/User/comment');?>">发表评价</a>
                    </div>
                </div>
                <div class="evaluation-cen fo-fa">
                    <div class="eval-cen-le fl pa-le-12">
                        <ul>
                            <li class="curres cliks">
                                <a href="javascript:void(0);" data-t='1'>
                                                <span>
                                                    全部评价
                                                    <em>(<?php echo ($commentStatistics['c0']); ?>)</em>
                                                </span>
                                </a>
                            </li>
                            <li class="cliks">
                                <a href="javascript:void(0);" data-t='2'>
                                                <span>
                                                    好评
                                                    <em>(<?php echo ($commentStatistics['c1']); ?>)</em>
                                                </span>
                                </a>
                            </li>
                            <li class="cliks">
                                <a href="javascript:void(0);" data-t='3'>
                                                <span>
                                                    中评
                                                    <em>(<?php echo ($commentStatistics['c2']); ?>)</em>
                                                </span>
                                </a>
                            </li>
                            <li class="cliks">
                                <a href="javascript:void(0);" data-t='4'>
                                                <span>
                                                    差评
                                                    <em>(<?php echo ($commentStatistics['c3']); ?>)</em>
                                                </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--------用户评价-start--------------->
                <!--<link rel="stylesheet" href="/Public/bootstrap/css/bootstrap.min.css" type="text/css">--->
                <div class="evaluation-bott pa-to-25 cliks-bn" style="display:block" id="ajax_comment_return">
                    <!--ajax 然后分页数据-->
                </div>
                <script>
                    $(document).ready(function(){
                        commentType = 1; // 评论类型
                        ajaxComment(1,1);// ajax 加载评价列表
                    });

                    // 好评差评 切换
                    $(".eval-cen-le a").click(function(){
                        $(".eval-cen-le li").removeClass('curres');
                        $(this).parent().addClass('curres');
                        commentType = $(this).data('t');// 评价类型   好评 中评  差评
                        ajaxComment(commentType,1);
                    });

                    // 用ajax分页显示评论
                    function ajaxComment(commentType,page){
                        $.ajax({
                            type : "GET",
                            url:"/index.php?m=Home&c=Goods&a=ajaxComment&goods_id=<?php echo ($goods['goods_id']); ?>&commentType="+commentType+"&p="+page,//+tab,
                            success: function(data){
                                $("#ajax_comment_return").html('');
                                $("#ajax_comment_return").append(data);
                            }
                        });
                    }
                </script>
                <!--------用户评价-end---------------->
            </div>
        </div>
        <div class="comm-param">
            <div class="parame-title">
                <div class="par-titles co-grey">
                    <ul class="commodity-xq ask_tab_li consult_ul">
                        <li class="current cliks">
                            <a data-t='0'>全部咨询</a>
                        </li>
                        <li class="cliks">
                            <a data-t='1'>商品咨询</a>
                        </li>
                        <li class="cliks">
                            <a data-t='2'>支付</a>
                        </li>
                        <li class="cliks">
                            <a data-t='3'>配送</a>
                        </li>
                        <li class="cliks">
                            <a data-t='4'>售后</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-------------------咨询列表----------------->
            <div class="parame-bott cliks-bn" style="display:block" id="ask_tab1">
                <div class="commodity-num" id="ajax_consult_return">
                    <!--ajax return-->
                </div>
            </div>
            <script>
            var _url = "<?php echo U('mall');?>";
            $(".search").attr("action", _url);
                $(document).ready(function(){
                    consult_type = 0; // 咨询类型
                    ajaxConsult(consult_type,1);// ajax 加载咨询列表
                });
                // 咨询类型切换
                $(".consult_ul a").click(function(){
                    $(".consult_ul li").removeClass('current');
                    $(this).parent().addClass('current');
                    consult_type = $(this).data('t');// 咨询类型 商品咨询 支付咨询 配送咨询 售后咨询
                    ajaxConsult(consult_type,1);
                });

                // 用ajax分页显示咨询
                function ajaxConsult(consult_type,page){
                    $.ajax({
                        type : "GET",
                        url:"/index.php?m=Home&c=Goods&a=ajax_consult&goods_id=<?php echo ($goods['goods_id']); ?>&consult_type="+consult_type+"&p="+page,//+tab,
                        success: function(data){
                            $("#ajax_consult_return").html('');
                            $("#ajax_consult_return").append(data);
                        }
                    });
                }
            </script>
            <!-------------------咨询列表-end---------------->
            <!-------------------发表咨询------------------>
            <div class="parame-bott ma-to--21">
                <div class="commodity-num">
                    <div class="spxqer-top-t">
                        <h3 class="spxqer-topt-h3">发表咨询</h3>
                    </div>
                    <div class="spxqer-cen">
                        <div class="spxqer-top pa-17-0-14">
                            <span class="colo-ora">温馨提示：</span>
                            因产线可能更改商品包装、产地、附配件等未及时通知，且每位咨询者购买、提问时间等不同。为此，客服给到的回复仅对提问者3天内有效，其他网友仅供参考！给您带来的不便还请谅解，谢谢！
                        </div>
                        <div class="form-edit-area">
                            <form action="<?php echo U('Home/Goods/goodsConsult');?>" method="post" id="form_consult" name="form_consult" onSubmit="return check_form_consult(this);">
                                <div class="form-table">
                                    <p>
                                        <b>商品咨询：</b>
                                        <input type="radio" name="consult_type" value="1" checked /><label for="">商品咨询</label>
                                        <input type="radio" name="consult_type" value="2" /><label for="">支付</label>
                                        <input type="radio" name="consult_type" value="3" /><label for="">配送</label>
                                        <input type="radio" name="consult_type" value="4" /><label for="">售后</label>
                                    </p>
                                    <p>网&nbsp;名:<input type="text" name="username" id="username" placeholder="请输入网名" /></p>
                                    <p>内&nbsp;容:<textarea name="content" id="content"  class="textarea"></textarea></p>
                                    <p>
                                        验证码:<input  type="text" name="verify_code" placeholder="不区分大小写"/>
                                        <img  id="verify_code" width="80" height="40" src="/index.php?m=Home&c=Index&a=verify&type=consult&fontSize=20&length=4" />
                                        <a><img  src="/Template/pc/default/Static/images/chg_image.png"  onclick="verify(this)" /></a>
                                    </p>
                                </div>
                                <div class="form-butt">
                                    <input type="hidden" name="goods_id" id="goods_id"  value="<?php echo ($goods['goods_id']); ?>"/>
                                    <input type="submit" class="bu-tj" value="提交">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script>
            $(".menu-list li").eq(1).attr("class","active");
                // 商品咨询表单验证
                function check_form_consult(f){
                    if($.trim($('input[name="username"]').val()).length == 0)
                    {
                        layer.msg('请填写一个网名', {
                            icon: 1,   // 成功图标
                            time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        });

                        return false;
                    }
                    if($.trim($('textarea[name="content"]').val()).length == 0)
                    {

                        layer.msg('请填输入咨询内容', {
                            icon: 1,   // 成功图标
                            time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        });
                        return false;
                    }
                    if($.trim($('input[name="verify_code"]').val()).length == 0)
                    {
                        layer.msg('请填输入验证码', {
                            icon: 1,   // 成功图标
                            time: 2000 //2秒关闭（如果不配置，默认是3秒）
                        });
                        return false;
                    }
                    return true;
                }
            </script>
            <!-------------------发表咨询 end----------------->
        </div>
    </div>
</div>

<script>




    $(document).ready(function(){
        // 更新商品价格
        get_goods_price();
        $(".jqzoom").jqueryzoom({
            xzoom: 480,
            yzoom: 480,
            offset: 30,
            position: "right",
            preload: 1,
            lens: 1
        });
    });

    /**
     * 切换规格
     */
    function switch_spec(spec)
    {
        $(spec).siblings('input').trigger('click');	 // 让隐藏的 单选按钮选中
        $(spec).parent().parent().parent().parent().find("div.sku").removeClass('sku-bo-blo'); //   清空勾选图标
        $(spec).parent().addClass('sku-bo-blo'); // 当前 加上勾选图标
        // 更新商品价格
        get_goods_price();
    }

    /**
     * 购买商品数量加加减减
     */
    function switch_num(num)
    {
        var num2 = parseInt($("#goods_num").val());
        num2 += num;
        if(num2 < 1) num2 = 1; // 保证购买数量不能少于 1
        $("#goods_num").val(num2); // 修改商品购买数量
        // 更新商品价格
        //get_goods_price();
    }
    // 用作 sort 排序用
    function sortNumber(a,b)
    {
        return a - b;
    }
    /*** 查询商品价格*/
    function get_goods_price()
    {

        var goods_price = <?php echo ($goods["shop_price"]); ?>; // 商品起始价
        var store_count = <?php echo ($goods["store_count"]); ?>; // 商品起始库存

        var spec_goods_price = <?php echo ($spec_goods_price); ?>;  // 规格 对应 价格 库存表   //alert(spec_goods_price['28_100']['price']);
        // 如果有属性选择项
        if(spec_goods_price != null)
        {
            goods_spec_arr = new Array();
            $("input[name^='goods_spec']:checked").each(function(){
                goods_spec_arr.push($(this).val());
            });
            var spec_key = goods_spec_arr.sort(sortNumber).join('_');  //排序后组合成 key
            goods_price = spec_goods_price[spec_key]['price']; // 找到对应规格的价格
            store_count = spec_goods_price[spec_key]['store_count']; // 找到对应规格的库存
        }

        var goods_num = parseInt($("#goods_num").val());
        // 库存不足的情况
        if(goods_num > store_count)
        {
            goods_num = store_count;
            layer.msg('库存仅剩 '+store_count+' 件', {icon: 2}); //alert('库存仅剩 '+store_count+' 件');
            //$("#goods_num").val(goods_num);
        }

        var flash_sale_price = parseFloat("<?php echo ($goods['flash_sale']['price']); ?>");
        (flash_sale_price > 0) && (goods_price = flash_sale_price);

        $("#goods_price").html(goods_price); // 变动价格显示
    }

    /**
     * 切换 商品详情  用户评价  规格参数  包装清单  售后服务
     */
    function switch_tab(cur,tab)
    {
        $("#tab1,#tab2,#tab3,#tab4").hide(); // 先全部隐藏
        $("#"+tab).show();	// 再显示其中一个
        $("ul.tab_li li").removeClass("current"); // 先全部样式去除
        $(cur).addClass("current"); //  单独的给当前点击这个加上
    }

    // 验证码切换
    function verify(){
        $('#verify_code').attr('src','/index.php?m=Home&c=Index&a=verify&type=consult&fontSize=20&length=4&r='+Math.random());
    }

    //缩略图切换
    $('.small-pic-li').each(function(i,o){
        var lilength = $('.small-pic-li').length;
        $(o).hover(function(){
            $(o).siblings().removeClass('current');
            $(o).addClass('current');
            $('#zoomimg').attr('src',$(o).find('img').attr('data-img'));
            $('#zoomimg').attr('jqimg',$(o).find('img').attr('data-big'));
            if(i==0){
                $('.next-left').addClass('disabled');
            }
            if(i+1==lilength){
                $('.next-right').addClass('disabled');
            }
        });
    })

    //前一张缩略图
    $('.next-left').click(function(){
        var newselect = $('.small-pic>.current').prev();
        $('.small-pic-li').removeClass('current');
        $(newselect).addClass('current');
        $('#zoomimg').attr('src',$(newselect).find('img').attr('data-img'));
        $('#zoomimg').attr('jqimg',$(newselect).find('img').attr('data-big'));
        var index = $('.small-pic>li').index(newselect);
        if(index==0){
            $('.next-left').addClass('disabled');
        }
        $('.next-right').removeClass('disabled');
    })

    //后前一张缩略图
    $('.next-right').click(function(){
        var newselect = $('.small-pic>.current').next();
        $('.small-pic-li').removeClass('current');
        $(newselect).addClass('current');
        $('#zoomimg').attr('src',$(newselect).find('img').attr('data-img'));
        $('#zoomimg').attr('jqimg',$(newselect).find('img').attr('data-big'));
        var index = $('.small-pic>li').index(newselect);
        if(index+1 == $('.small-pic>li').length){
            $('.next-right').addClass('disabled');
        }
        $('.next-left').removeClass('disabled');
    })
</script>

<!--------收货地址，物流运费-开始-------------->
<script src="/Template/pc/default/Static/js/location.js"></script>
<!--------收货地址，物流运费--结束-------------->

<script src="/Public/js/jqueryUrlGet.js"></script><!--获取get参数插件-->
<script> set_first_leader(); //设置推荐人 </script>

<!--------footer-开始-------------->
<div class="introduce">
	<div class="introduce-content text-center">
		<h3>深圳市胡博士珠宝文化传播有限公司</h3>
		<p>
			拥有或与相关内容提供者共同拥有深圳珠宝咨询网站内相关内容（包括文字、图片、音频、视频资料及页面设计、编排、软件等）的版权和其他相关知识产权。 如您对本网站的相关版权有任何异议或认为侵犯了您的合法权益，请及时与我们联系。
		</p>
	</div>
</div>

<div class="link">
	<h4 class="text-center">友情链接</h4>
	<ul class="clearfix">
		<?php if(is_array($friend_link)): $i = 0; $__LIST__ = $friend_link;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($vo["link_url"]); ?>" target="_blank"><?php echo ($vo["link_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
</div>
<div class="footer text-center">
	<div class="footer-link">
		<a href="<?php echo U('Public/contact');?>">关于我们</a>
		<a href="<?php echo U('Public/contact');?>">联系我们 </a>
		<a href="#">在线客服</a>
		<a href="#" style="border: 0;">加入我们</a>
	</div>
	<p>Copyright © 2016 All Rights Reserved 深圳市珠宝职业学院 版权所有 </p>
	<p>粤ICP备 15050282号 www.shenzhen.cn</p>
</div>
<div class="glyphicon glyphicon-chevron-up go-up"></div>
<script src="/Public/home/js/bootstrap.min.js"></script>
<script src="/Public/js/layer/layer.js"></script>
<!--
<script>
(function($){
		var url = document.location.href;
		if(url.search("id") === (-1)){
			$("#navigation").find("li").eq(0).addClass("active").siblings("li").removeClass("active");
		}else{
			var newstr = url.slice(url.search("id"));
			switch (Number(newstr.slice(3))){
				case 1:
					$(".navigation").find("li").eq(1).addClass("active").siblings("li").removeClass("active");
				break;
				case 2:
					$(".navigation").find("li").eq(2).addClass("active").siblings("li").removeClass("active");
				break;
				case 12:
					$(".navigation").find("li").eq(3).addClass("active").siblings("li").removeClass("active");
				break;
				case 16:
					$(".navigation").find("li").eq(4).addClass("active").siblings("li").removeClass("active");
				break;
				case 19:
					$(".navigation").find("li").eq(5).addClass("active").siblings("li").removeClass("active");
				break;
			};
		};
	})(jQuery);
</script>
-->
<!--------footer-结束-------------->
</body>
</html>