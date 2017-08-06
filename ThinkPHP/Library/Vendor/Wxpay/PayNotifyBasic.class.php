<?php
namespace Vendor\Wxpay;
require_once "WxPay.Api.php";
require_once "WxPay.Notify.php";
use Think\Log;

abstract class PayNotifyBasic extends \WxPayNotify{
    //查询订单
    public function Queryorder($transaction_id){
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
  
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg){
        $file = fopen('Public/log/Wechat/pay.log','a+'); //Public/log/Wechat/
        fwrite( $file , json_encode( $data ).json_encode( $msg ).'|--->操作时间'.date("Ymd-H:i:s")."\r\n");

   
        $notfiyOutput = array();
        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            fwrite( $file , "================================================\r\n");
            fwrite( $file , $msg .'|--->操作时间'.date("Ymd-H:i:s")."\r\n");
            fwrite( $file , "================================================\r\n");
            return false;
        }


        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            fwrite( $file , "================================================\r\n");
            fwrite( $file , $msg .'|--->操作时间'.date("Ymd-H:i:s")."\r\n");
            fwrite( $file , "================================================\r\n");
            return false;
        }
        
        switch ($this->overWriteCall($data)) {
            case 'TRUE':
                return true;
                break;
            case 'FALSE':
                fwrite( $file , "================================================\r\n");
                fwrite( $file , 'Handle Error|--->操作时间'.date("Ymd-H:i:s")."\r\n");
                fwrite( $file , "================================================\r\n");
                return false;
                break;
            case 'NOTHING':
                fwrite( $file , "================================================\r\n");
                fwrite( $file , 'Handle Nothing|--->操作时间'.date("Ymd-H:i:s")."\r\n");
                fwrite( $file , "================================================\r\n");
                break;
        }
        fclose( $file );
    }

    abstract public function overWriteCall($data);
}