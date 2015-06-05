<?php


class userController extends Controller {
    protected $_user;
    
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
	
	if (Bootstrap::$main->session('fb_friends')) $scope.=",user_friends";
	$this->check_input();
	
        $uri = $config['protocol'].'://' . $_SERVER['HTTP_HOST'] . Bootstrap::$main->getRoot() . 'user/facebook';
        
	if ($this->_getParam('redirect')) Bootstrap::$main->session('auth_redirect',$this->_getParam('redirect'));
	elseif (!Bootstrap::$main->session('auth_redirect')) mydie('redirect parameter missing','error');
	
	if (isset($_GET['state']) && $_GET['state']==Bootstrap::$main->session('oauth2_state'))
	{
            if (isset($_GET['code']))
            {
		$url='https://graph.facebook.com/oauth/access_token';
		$url.='?client_id='.$config['fb.app_id'];
		$url.='&redirect_uri='.urlencode($uri);
		$url.='&client_secret='.$config['fb.app_secret'];
		$url.='&code='.urlencode($_GET['code']);
		
		parse_str($this->req($url),$token);


                if (isset($token['access_token']))
                {   
		    $auth = @json_decode(file_get_contents('https://graph.facebook.com/v2.3/me?format=json&access_token='.$token['access_token']),true);
                    $picture = @json_decode(file_get_contents('https://graph.facebook.com/v2.3/me/picture?redirect=false&type=normal&access_token='.$token['access_token']),true);
                 
	    
                    if (isset($auth['id']) && isset($auth['email']))
                    {
			$md5hash='fb.'.$auth['id'];
			$email=$this->standarize_email($auth['email'],false);
			$user=$this->user()->find_one_by_md5hash($md5hash);
			
			if (!$user)
			{
			    $user=$this->add(array(
				'firstname'=>$auth['first_name'],
				'lastname'=>$auth['last_name'],
				'md5hash'=>$md5hash,
				'email'=>$email
			    ),false);
			}
			
			$model=new userModel($user['id']);
			
			if (!$model->firstname) $model->firstname = $auth['first_name'];
			if (!$model->lastname) $model->lastname = $auth['last_name'];
			if (isset($picture['data']['url']))
			    if (!$model->photo || strstr($model->photo,'fbcdn'))
				$model->photo = $picture['data']['url'];
				
			
			if (!$model->url) $model->url='https://www.facebook.com/'.$auth['id'];
			
			$model->save();
			
			$data=$model->data();
			unset($data['password']);
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
                        $this->redirect(Bootstrap::$main->session('auth_redirect'));
                    }
                    
                }               
                else
                {
                    $this->redirect(Bootstrap::$main->session('auth_redirect'));
                }
                
            } else {
                $this->redirect(Bootstrap::$main->session('auth_redirect'));
            }
        } elseif (isset($_GET['state'])) {
            $this->redirect($uri);	    
	} else {
	    
            $state=md5(rand(90000,1000000).time());
            Bootstrap::$main->session('oauth2_state',$state);
	    
	    
	    $url='https://www.facebook.com/v2.3/dialog/oauth';
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
    
}
