<?php

if (isset($_SERVER['SERVER_SOFTWARE']) && strstr(strtolower($_SERVER['SERVER_SOFTWARE']),'engine')) {
    require_once 'google/appengine/api/cloud_storage/CloudStorageTools.php';
}

use google\appengine\api\cloud_storage\CloudStorageTools;

class userController extends Controller {
    protected $_user;
	protected $_prefix='users';
    
    /**
     * @return userModel
     */    
    protected function user($user=null)
    {
		if (is_null($this->_user)) $this->_user=new userModel();
		if (!is_null($user)) $this->_user->get($user);
		return $this->_user;
    }
    
    
    public function get_facebook()
    {
	
		$config=Bootstrap::$main->getConfig();
		$scope="email,public_profile";
		
		if (Bootstrap::$main->session('fb_likes')) $scope.=",user_likes";
		$this->check_input();
	
        $uri = $config['protocol'].'://' . $_SERVER['HTTP_HOST'] . Bootstrap::$main->getRoot() . 'user/facebook';
        
		if ($this->_getParam('redirect')) Bootstrap::$main->session('auth_redirect',$this->_getParam('redirect'));
		//elseif (!Bootstrap::$main->session('auth_redirect')) mydie('redirect parameter missing','error');

	
		if (isset($_GET['state']) && $_GET['state']==Bootstrap::$main->session('oauth2_state'))
		{
				if (isset($_GET['code']))
				{
					$url='https://graph.facebook.com/v2.9/oauth/access_token';
					$url.='?client_id='.$config['fb.app_id'];
					$url.='&redirect_uri='.urlencode($uri);
					$url.='&client_secret='.$config['fb.app_secret'];
					$url.='&code='.urlencode($_GET['code']);
					
	
					//parse_str($this->req($url),$token);
					$token=json_decode($this->req($url),true);
	
	
					if (isset($token['access_token']))
					{
						Bootstrap::$main->session('access_token',$token['access_token']);
						$auth = @json_decode(file_get_contents('https://graph.facebook.com/v2.9/me?fields=id,email,name&format=json&access_token='.$token['access_token']),true);
						$picture = @json_decode(file_get_contents('https://graph.facebook.com/v2.9/me/picture?redirect=false&type=normal&access_token='.$token['access_token']),true);
					 
						
						if (isset($auth['id']))
						{
							$md5hash='fb.'.$auth['id'];
							$email=isset($auth['email'])?$this->standarize_email($auth['email'],false):$md5hash;
							$user=$this->user()->find_one_by_md5hash($md5hash);
							$fbname=explode(' ',$auth['name']);
							
							if (!$user)
							{
								$user=$this->add(array(
								'firstname'=>$fbname[0],
								'lastname'=>$fbname[1],
								'md5hash'=>$md5hash,
								'email'=>$email
								),false);
							}
				
							$model=new userModel($user['id']);
							
							$model->firstname = $fbname[0];
							$model->lastname = $fbname[1];
							if(strstr($email,'@')) $model->email=$email;
							
							if (isset($picture['data']['url']) && Bootstrap::$main->appengine) {
					
								$file=Tools::saveRoot($this->_prefix.'/'.$auth['id'].'.jpg');
								file_put_contents($file,file_get_contents($picture['data']['url']));
								$photo=CloudStorageTools::getImageServingUrl($file,['size'=>0+Bootstrap::$main->getConfig('user_thumb_size'),'crop'=>true,'secure_url'=>true]);
								
								$model->photo = $photo;
						
							}
				
							if (!$model->url) $model->url='https://www.facebook.com/'.$auth['id'];
				
							$model->save();
				
							$data=$model->data();
				
							Tools::log('fb-login',['get'=>$_GET,'token'=>$token,'auth'=>$auth, 'user'=>$data]);
							
							if(isset($data['password'])) unset($data['password']);
							Bootstrap::$main->session('user',$data);
							Bootstrap::$main->session('auth', $auth);
							
							$this->redirect(Bootstrap::$main->session('auth_redirect'));
							
						}
						else
						{
							if (isset($auth['error']))
							{
								Bootstrap::$main->session('error', $auth['error']['message']);
							}
							Tools::log('fb-login-error',['get'=>$_GET,'token'=>$token,'auth'=>$auth]);
							$this->redirect(Bootstrap::$main->session('auth_redirect'));
						}
						
					}               
					else
					{
						Tools::log('fb-login-error',['get'=>$_GET,'token'=>$token]);
						$this->redirect(Bootstrap::$main->session('auth_redirect'));
					}
					
				} else {
					$this->redirect(Bootstrap::$main->session('auth_redirect'));
				}
			} elseif (isset($_GET['state'])) {
				Tools::log('fb-login-error',['get'=>$_GET]);
				$this->redirect($uri);	    
			} else {
			
				$state=md5(rand(90000,1000000).time());
				Bootstrap::$main->session('oauth2_state',$state);
			
			
				$url='https://www.facebook.com/v2.9/dialog/oauth';
				$url.='?client_id='.$config['fb.app_id'];
				$url.='&redirect_uri='.urlencode($uri);
				$url.='&state='.$state;
				$url.='&scope='.urlencode($scope);
				
				$this->redirect($url);
			}
		
    }
    
    
    public function get_logout()
    {
		Bootstrap::$main->session('user',false);
		Bootstrap::$main->logout();
		return $this->status();
    }

    protected function standarize_email($email,$error=true)
    {
        $email=mb_convert_case($email,MB_CASE_LOWER);

        $email=str_replace(' ','',$email);
        if ($error && !preg_match('/^[^@]+@.+\..+$/',$email)) {
            return $this->error(4);
        }	
	
	return $email;
    }
    
    
    
    public function get()
    {
	
	if (!isset(Bootstrap::$main->user['id']))
	    return $this->status([],false);
	
	$user=Bootstrap::$main->user;    
	
		
        return $this->status($user);
    }
    
    protected function add($data)
    {
	$data['trust']=0;	
        $this->user()->load($data,true);
	$data=$this->user()->save();

        return $data;	
    }
    
    public function likes()
    {
	Bootstrap::$main->session('fb_likes',1);
	if (!Bootstrap::$main->session('access_token')) return false;
	$url='https://graph.facebook.com/v2.9/me/likes/'.Bootstrap::$main->getConfig('fb.fanpage').'?access_token='.Bootstrap::$main->session('access_token');
	$data=@json_decode(file_get_contents($url),true);
        if (!isset($data['data'])) return false;
	return count($data['data'])>0;
    }
    
}
