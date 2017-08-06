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
 * Author: JY
 * Date: 2015-09-09
 */
namespace Admin\Model;
use Think\Model;
class OrderModel extends Model {
    //protected $tablePrefix = 'tp_';
    protected $patchValidate = true; // 系统支持数据的批量验证功能，
    protected $_validate = array(
        array('consignee','require','收货人称必须填写！',1 ,'',3),  // 1 必须验证
        array('address','require','地址必须填写',1,'',3), // 在新增的时候验证name字段是否唯一
    );
}
