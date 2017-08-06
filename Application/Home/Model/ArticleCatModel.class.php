<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: dyr
 * Date: 2016-08-23
 */

namespace Home\Model;

use Think\Model;

/**
 * @package Home\Model
 */
class ArticleCatModel extends Model
{
    protected $tableName = 'article_cat';
    
    /**
     * 获取文章分类
     * @param $limit string 
     * @param $asc str 排序
     * @return array
     */
    public function getArticleCate($limit){
        if(empty($limit)){
            return false;
        }
        $articleCat =  M('article_cat');
        $result = $articleCat->where("show_in_nav=1")->order("sort_order")->limit($limit)->select();
        // echo $articleCat->getLastSql();die;
        return $result;
    }

    
}
