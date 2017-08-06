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
class ArticleModel extends Model
{
    protected $tableName = 'article';
    
    /**
     * 根据分类获取文章信息
     * @param $cat_id string
     * @return array
     */
    public function getArticleInfo($cat_id,$limit){
        $article = M('article');
        if(empty($cat_id)){
            return false;
        }
        switch ($cat_id) {
            case 'yue':
                $yue = M('articleCat')->field("cat_id")->where("show_in_nav=1")->order("sort_order")->limit(4)->select();
                $res = $this->getcates($yue);
                $where = "cat_id in ('$res') and is_open = 1 ";
                $order = ' cat_id and article_id desc ';
                $result = $article->where($where)->order($order)->limit($limit)->select();
                // echo $article->getLastSql();die;
                // var_dump($result);die;
                return $result;
                break;
            case 'all':
                $where = "is_open = 1";
                $order = ' article_id desc ';
                $result = $article->where($where)->order($order)->select();
                // var_dump($result);die;
                return $result;
                break;
            default:
                $where = "is_open = 1 and cat_id = $cat_id ";
                $order = ' article_id desc';
                $result = $article->where($where)->order($order)->select();
                // var_dump($result);die;
                return $result;
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

    
}
