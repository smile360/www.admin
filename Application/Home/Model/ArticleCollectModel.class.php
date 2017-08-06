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
class ArticleCollectModel extends Model
{
    protected $tableName = 'article_collect';
    
    /**
     * 根据文章id和用户id'查看有没有收藏
     * @param $article_id int
     * @param $user_id int
     * @return array
     */
    public function getArticleCollect($article_id,$user_id){
        if(empty($user_id)){
            return false;
        }
        if(empty($article_id)){
            return false;
        }
        $where = " article_id = ".$article_id." and user_id = ".$user_id.' ';
        
        $res = M('article_collect')->where($where)->find();
   
        if($res){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * 查看用户的收藏数量
     * @param $user_id int
     * @return array
     */
    public function getCollectNum($user_id){
        if(empty($user_id)){
            return false;
        }
        $res = M('article_collect')->where(" user_id = $user_id ")->count();
        if($res){
            return $res;
        }else{
            return 0;
        }
    }
    /**
     * 获取用户的收藏文章信息
     * @param $user_id int
     * @return array
     */
    public function getCollectArticle($user_id){
        if(empty($user_id)){
            return false;
        }
        //获取用户收藏的文章id
        $article_ids = $this->getArticleId($user_id);
        $sql = "SELECT * from tp_article WHERE article_id in (".$article_ids.")";
        $sql = "SELECT article_id,title,add_time,click,thumb,status,description from tp_article WHERE article_id in (".$article_ids.")";
        $res = M('article')->query($sql);
        if($res){
            return $res;
        }else{
            return '您还没有收藏的文章哦~';
        }
        
    }
    //根据用户名收藏的文章id
    private function getArticleId($user_id){
        if(empty($user_id)){
            return false;
        }
        $collect = M('article_collect')->where(" user_id = $user_id ")->select();
            $var = '';
        foreach ($collect as $key => $value) {
            $arr .= $value['article_id'].',';
        }
        $str = rtrim($arr,',');
        return $str;
    }
    //统计收藏
    public function getcollectCount( $id ){
        if(empty($id)){
            return false;
        }
        $result = M("article_collect")->where(array('article_id'=>$id))->count();
        return $result;
    }
    //统计点赞
    public function getArticleLike( $article_id , $user_id ){
        if(empty( $article_id) || empty($user_id)){
            return false;
        }
        $result = M('article_like')->where(array("article_id"=>$article_id,"user_id"=>$user_id))->count();
        return $result;
    }
    
    //统计喜欢
    public function getlikeCount( $id ){
        if(empty($id)){
            return false;
        }
        $result = M("article_like")->where(array('article_id'=>$id))->count();
        return $result;
    }
}
