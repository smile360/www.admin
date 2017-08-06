<?php 
namespace Vendor\Wxpay;
require_once "PayNotifyBasic.class.php";

class PayNotify extends PayNotifyBasic{

	public function overWriteCall( $data ){
		$Model = M("Order");
		//金额对比
		// $total_fee = $Model->where(array( "order_no"=>$data['attach'] ))
		// 				   ->field("order_no,money")
		// 				   ->find();
		$array = array(
				"pay_status"=>1,
				"pay_time"=>time(),
				"pay_code"=>"Wxpay",
				"pay_name"=>"微信支付",
			);
		$result = $Model->where(array( "order_no"=>$data['attach'] ))
						->setField( $array );
		// if( $result && ($total_fee['money'] == $data['total_fee']/100) ){
		if( $result ){
			return "TRUE";
		}else{
			return  "NOTHING";
		}
	}
}