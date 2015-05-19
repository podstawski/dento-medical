<?php

if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
    require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
} else {
    require_once __DIR__.'/../classes/Image.php';
}

use google\appengine\api\cloud_storage\CloudStorageTools;


class imageController extends Controller {
    protected $_image;
    protected $_media_dir,$_media,$_prefix='img';

    /**
     * @return imageModel
     */    
    protected function image()
    {
	if (is_null($this->_image)) $this->_image=new imageModel();
	return $this->_image;
    }
    
    public function init()
    {
	parent::init();
	$this->_prefix='images';
	$this->_appengine=Bootstrap::$main->appengine;
	if (!$this->_appengine) {
            $this->_media=$_SERVER['REQUEST_URI'];
            if ($pos=strpos($this->_media,'?')) $this->_media=substr($this->_media,0,$pos);
            $this->_media=dirname(dirname($this->_media)).'/media';
	}
    }
    

    
    public function get()
    {	

	
	Bootstrap::$main->session('image-for-church',$this->id);
	
	$upload_url=Bootstrap::$main->getRoot().'image';
	if ($this->_appengine)
	{
	    $upload_url = CloudStorageTools::createUploadUrl($upload_url, []);
	}
	else
	{
	    $upload_url='http://'.$_SERVER['HTTP_HOST'].$upload_url;
	}
	
	$ret=array('success'=>true,'url'=>$upload_url);

    	
	return $ret;
    }
    
    public function post()
    {
	if (isset($_FILES))
	{
	    foreach ($_FILES AS $name=>$file)
	    {
		$f=$this->upload_file($file['tmp_name'],$file['name']);
		if (is_array($f)) return $this->status($f);
	    }
	}
	
	
	return $this->status();
    }
    

    
    
    
    protected function upload_file($tmp,$name)
    {
	if (!isset(Bootstrap::$main->user['id'])) return false;
	
	//mydie($this->_media_dir,$this->_media);
	$ext=@strtolower(end(explode('.',$name)));
	$user=Bootstrap::$main->user;
	
	
	$name=$this->_prefix.'/'.$user['md5hash'].'/'.md5_file($tmp).'.'.$ext;
	

	$original_name=$name;

		
	
	if ($this->_appengine) {
	    $file='gs://'.CloudStorageTools::getDefaultGoogleStorageBucketName().'/'.$name;
	    move_uploaded_file($tmp,$file);
	    
	} else {
	    $file=Tools::saveRoot($name);
	    move_uploaded_file($tmp,$file);

	}


	
	if (!file_exists($file) || !filesize($file)) $this->error(18);

	$model=new imageModel();
	$model->author_id=$user['id'];
	$model->src=$name;
	$model->ip_uploaded=Bootstrap::$main->ip;
	$model->d_uploaded=Bootstrap::$main->now;
	$model->church = Bootstrap::$main->session('image-for-church');
	
	$exif=[];
	$imagesize=@getimagesize($file,$exif);
	if (!is_array($imagesize) || !$imagesize[0]) $imagesize=[5000,5000];
	
	if (is_array($exif)) foreach ($exif  AS $k=>$a)
	{
	    
	    if (substr($a,0,4)=='Exif')
	    {
		$matches=[];
		preg_match_all('/[0-9]{4}:[0-9]{2}:[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/',$a,$matches);
		$d='';
		
		if (isset($matches[0][1])) {
		    $d=$matches[0][1];
		} elseif (isset($matches[0][0])) {
		    $d=$matches[0][0];
		}
		if ($d)
		{

		    $d=preg_replace('/([0-9]{4}):([0-9]{2}):([0-9]{2})/','\1-\2-\3',$d);		    
		    $model->d_taken=strtotime($d);
		}
	    }
	    
	}
	
	if ($this->_appengine) {
	    $model->url = CloudStorageTools::getImageServingUrl($file,['size'=>0+Bootstrap::$main->getConfig('image_size')]);
	    $model->square = CloudStorageTools::getImageServingUrl($file,['size'=>0+Bootstrap::$main->getConfig('square_size'),'crop'=>true]);
	
	} else {
	    $image=new Image($file);
	    
	    $w=$h=0;
	    if ($imagesize[0] > Bootstrap::$main->getConfig('image_size'))
	    {
		$w=Bootstrap::$main->getConfig('image_size');
		$img=preg_replace("/\.$ext\$/",'-i.'.$ext,$file);
		$image->min($img,$w,$h,true);
		$model->url='http://'.$_SERVER['HTTP_HOST'].$this->_media.'/'.preg_replace("/\.$ext\$/",'-i.'.$ext,$name);
	    } else $model->url='http://'.$_SERVER['HTTP_HOST'].$this->_media.'/'.$name;

	    
	    $w=$h=0;
	    
	    $w=$h=Bootstrap::$main->getConfig('square_size');
	    $square=preg_replace("/\.$ext\$/",'-s.'.$ext,$file);
	    $image->min($square,$w,$h,false,true);
	    $model->square='http://'.$_SERVER['HTTP_HOST'].$this->_media.'/'.preg_replace("/\.$ext\$/",'-s.'.$ext,$name);	    
	}
		
	
	$model->save();
	$ret=$model->data();
	
	
	return $this->status($ret);
    }
    
    public function put()
    {
	$this->requiresLogin();
	$user=Bootstrap::$main->user;
	
	
	$this->check_input(['labels'=>1]);

	$id=0+$this->id;
	$data=false;
	if ($id) $data=$this->image()->get($id);
	
	if (!$data) $this->error(18);
	if ($data['user']!=$user['id']) $this->error(19);

	
	$model=$this->image();
	foreach (['title','description'] AS $f)
	{
	    if (isset($this->data[$f])) $model->$f=$this->data[$f];
	}
		
	$model->save();

	
	return $this->status($model->data());
    }
    
    public function delete()
    {
	$this->requiresLogin();
	$user=Bootstrap::$main->user;

	$id=0+$this->id;
	$data=false;
	if ($id) $data=$this->image()->get($id);
	
	if (!$data) $this->error(18);
	if ($data['user']!=$user['id']) $this->error(19);
	
	if ($this->_appengine) {
	    $file='gs://'.CloudStorageTools::getDefaultGoogleStorageBucketName().'/'.$data['src'];
	    CloudStorageTools::deleteImageServingUrl($file);
	} else {
	    $file=$this->_media_dir.'/'.$data['src'];
	    $ext=@end(explode('.',$file));
	    @unlink(preg_replace("/\.$ext\$/",'-t.'.$ext,$file));
	    @unlink(preg_replace("/\.$ext\$/",'-s.'.$ext,$file));
	}
	@unlink($file);
	$this->image()->remove($data['id']);
	return $this->status();
	
    }


}
