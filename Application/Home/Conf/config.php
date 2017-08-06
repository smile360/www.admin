<?php
return array(
	//'配置项'=>'配置值'           
    // 'SHOW_PAGE_TRACE'           => true,
    // 'DB_DEBUG' => true,
    // 'DB_SQL_LOG' => true,
    //'DB_SQL_BUILD_CACHE' => true, // sql 缓存
    //'DB_SQL_BUILD_QUEUE' => 'File', // 文件缓存
   // 'DB_SQL_BUILD_LENGTH' => 200, // SQL缓存的队列长度
    //'DATA_CACHE_TIME' => 60,
   // 'DATA_CACHE_TYPE' =>  'File', 
    // 'LOAD_EXT_CONFIG' => 'html',	// 加载其他自定义配置文件 html.php
    'URL_HTML_SUFFIX'       =>  'html',
    'HTML_CACHE_ON'     =>    true, // 开启静态缓存   
    'HTML_CACHE_TIME'   =>    1,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  =>    '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES'  =>     array(  // 定义静态缓存规则
         // 定义格式1 数组方式
         //'静态地址'    =>     array('静态规则', '有效期', '附加规则'), 
        'index:index'=>array('{:module}_{:controller}_{:action}',TPSHOP_CACHE_TIME),  // 首页静态缓存 3秒钟       
        //'index:goodsList'=>array('{:module}_{:controller}_{:action}',300),  // 列表页静态缓存 3秒钟 无参数 post 提交的很难缓存
        //'Goods:goodsList'=>array('{:module}_{:controller}_{:action}_{id})_{p}',TPSHOP_CACHE_TIME),  // 列表页静态缓存 3秒钟
        //ajax 请求的商品列表内容在 ajaxGoodsList 函数中  S($keys,$html,300); 缓存
        'Goods:goodsInfo'=>array('{:module}_{:controller}_{:action}_{id}',TPSHOP_CACHE_TIME),  // 商品详情页静态缓存 3秒钟                
        'Goods:ajaxComment'=>array('{:module}_{:controller}_{:action}_{goods_id}_{commentType}_{p}',TPSHOP_CACHE_TIME),  // 商品评论页静态缓存 3秒钟        
        'Goods:ajax_consult'=>array('{:module}_{:controller}_{:action}_{goods_id}_{consult_type}_{p}',TPSHOP_CACHE_TIME),  // 商品咨询页静态缓存 3秒钟                
        // 商品详情的规格价格缓存在 ajaxGoodsPrice 函数里面 S($keys,$price,300);// 缓存数据300秒  
    	'channel:index'=>array('{:module}_{:controller}_{:action}_{id}',TPSHOP_CACHE_TIME),  // 列表页静态缓存 3秒钟
    ), 
    'TMPL_PARSE_STRING' => array( 
        '__CSS__' => __ROOT__.'/Public/home/css/', 
        '__JS__'     => __ROOT__.'/Public/home/js/', 
        '__IMG__'     => __ROOT__.'/Public/home/img/', 
        '__UPLOAD__' => __ROOT__.'/Pulibc/home/upload/detail' ,
        '__STATIC__'     => '/Template/pc/default/Static',
        ),
    
    //默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => 'Public:dispatch_jump',
    //默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => 'Public:dispatch_jump',
        
);