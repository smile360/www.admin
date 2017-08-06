<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace Mobile\Controller;

class UploadController extends MobileBaseController {

    //base64图片上传
	public function upload(){
        $baseUrl = trim(I('post.imgData'));
        $baseID = trim(I('post.imgID'));

        $array = explode(',', $baseUrl);
        $suffix = $this -> urlExplode($array[0]);
        // $base64 = base64_decode(substr(strstr($base,','),1));
        $base64 = base64_decode($array[1]);
        $result = $this -> BaseImg($base64, $suffix);
        if ($result) {
            $this -> ajaxReturn( $result );
        } 
	}
    /** 
    * 处理base64图处理
    * @param 1 base64编码
    * @param 2 文件格式
    * @return  $url ; 
    **/
    public function BaseImg( $base, $suffix){
        $path = __ROOT__."Public/upload/consult/".date("Y-m-d")."/";
        $url = $path.date("Ymd").uniqid().".".$suffix;
        if (!is_dir($path))mkdir($path, 0777,true);
        $img = file_put_contents($url,$base);
        if( $img ){
            return '/'.$url;
        }else{
            return false;
        }
    }

    /**
    * Auth xz
    * Date 2016-09-18 15:00
    * 处理base64图片后缀名
    * @param $string
    * @return $string png|jpge|gif..;
    **/
    public function urlExplode($string) {
        $image = explode("/", $string);
        $name = explode("；", $image[1]);
        return $name[0];
    }

    /***点X删除图片清除S缓存***/
    public function ImageDel() {
        $image = trim(I("post.image_id"));
        $image_url = S(session('id').$image);
        @unlink(($_SERVER['DOCUMENT_ROOT']) . '/' . $image_url);
        S(session('id').$image, null);
    }
	public function UploadAction(){
		if($_FILES['fileList']['name'] != ''){ 
            $res = $this->img_upload('consult');
            if($res['code'] == '200'){
                exit($res['url']);
            }else{
                exit('400');
            }
        }
	}

	/**上传图片处理
    * @param $string 目录名;
    * @return $string 存放路径;
    **/
    function img_upload($dir = ''){
        $path = __ROOT__."Public/upload/$dir/"; //制作新文件地址
        if (!is_dir($path))mkdir($path, 0777); //如果文件夹不存在，创建文件夹并赋予权限
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728 ;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg','bmp','tmp');
        $upload->rootPath = $path;
        $upload->saveExt = 'jpg';
        $info = $upload->upload();
        if(!$info)
        {
            return array('code'=>'102','msg'=>$upload->getError());
        }else{  
            if($dir == 'consult')
            {
                foreach($info as $file)
                {
                    $url = '/'.$path.$file['savepath'].$file['savename'];
                    $image = new \Think\Image(); 
                    $image->open($path.$file['savepath'].$file['savename']);
                    $image->crop(850, 400)->save($path.$file['savepath'].$file['savename']);
                    return array('code'=>'200','url'=>$url);
                }
            }else{

                foreach($info as $file){ 
                    $url = '/'.$path.$file['savepath'].$file['savename'];
                    return array('code'=>'200','url'=>$url);
                }   
            }
        }
    }

	/*
	 删除上传的图片
	*/
	public function delupload(){
		$action=isset($_GET['action']) ? $_GET['action'] : null;
		$filename= isset($_GET['filename']) ? $_GET['filename'] : null;
		$filename= str_replace('../','',$filename);
		$filename= trim($filename,'.');
		$filename= trim($filename,'/');
		if($action=='del' && !empty($filename)){
			$size = getimagesize($filename);
			$filetype = explode('/',$size['mime']);
			if($filetype[0]!='image'){
				return false;
				exit;
			}
			unlink($filename);
			exit;
		}
	}    
}