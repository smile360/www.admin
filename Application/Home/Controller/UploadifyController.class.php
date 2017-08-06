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
namespace Home\Controller;

class UploadifyController extends BaseController {

	public function upload(){
		$func = I('func');
		$path = I('path','temp');
		$info = array(
				'num'=> I('num'),
				'title' => '',
				'upload' =>U('Admin/Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'logo')),
				'size' => '4M',
				'type' =>'jpg,png,gif,jpeg',
				'input' => I('input'),
				'func' => empty($func) ? 'undefined' : $func,
		);
		$this->assign('info',$info);
		$this->display();
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
                    $image->thumb(850, 400,\Think\Image::IMAGE_THUMB_FILLED)->save($path.$file['savepath'].$file['savename']);
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