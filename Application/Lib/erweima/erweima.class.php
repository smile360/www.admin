<?php    
    
include "qrlib.php";    

class erweima
{
    public function make($path = '', $content = '')
    {
        if(empty($path))
        {
            return;       
        }

        //生成二维码
        QRcode::png($content, $path, 'L', '6', 2);
    }
}