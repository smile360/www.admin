<?php
/**
 * 验证码发送
 * @param $mobile 手机号码
 * @param $content 发送内容
 * @param $type 验证码类型
 */
function send_sms($mobile,$content,$type=''){

}


/**
 * 面包屑导航  用于前台用户中心
 * 根据当前的控制器名称 和 action 方法
 */
function navigate_user()
{    
    $navigate = include APP_PATH.'Common/Conf/navigate.php';    
    $location = strtolower('Home/'.CONTROLLER_NAME);
    $arr = array(
        '首页'=>'/',
        $navigate[$location]['name']=>U('/Home/'.CONTROLLER_NAME),
        $navigate[$location]['action'][ACTION_NAME]=>'javascript:void();',
    );
    return $arr;
}

/**
*  面包屑导航  用于前台商品
 * @param type $id 商品id  或者是 商品分类id
 * @param type $type 默认0是传递商品分类id  id 也可以传递 商品id type则为1
 */
function navigate_goods($id,$type = 0)
{
    $cat_id = $id; //
    // 如果传递过来的是
    if($type == 1){
        $cat_id = M('goods')->where("goods_id = $id")->getField('cat_id');
    }
    $categoryList = M('GoodsCategory')->getField("id,name,parent_id");

    // 第一个先装起来
    $arr[$cat_id] = $categoryList[$cat_id]['name'];
    while (true)
    {
        $cat_id = $categoryList[$cat_id]['parent_id'];
        if($cat_id > 0)
            $arr[$cat_id] = $categoryList[$cat_id]['name'];
        else
            break;
    }
    $arr = array_reverse($arr,true);
    return $arr;
}

//传分类id获取分类名称
function getCateName($cat_id){
    switch ($cat_id) {
        case '88':
            return '快讯';
            break;
        case '89':
            return '涨停板';
            break;
        case '90':
            return '精华版';
            break;
        case '91':
            return '实时内情';
            break;
        case '92':
            return '新声TV';
            break;
        case '93':
            return '三板客';
            break;
        case '94':
            return '深度';
            break;
        case '95':
            return '酷公司';
            break;
        case '96':
            return 'CEO新声';
            break;
        case '97':
            return '互联网金融';
            break;
        case '98':
            return 'O2O智能硬件';
            break;
        case '99':
            return '创业投资';
            break;
    }
}
//循环把分类的数组编程字符串
    function getcates($arr){
        $str = '';
        foreach ($arr as $key => $value) {
            foreach ($value as $k => $v) {
                $str .= $v.',';
            }
        }
        $string  = rtrim($str,',');
        return $string;
    }



