<?php /***** ucwords(strtolower($bar)); *****/
/**
	* Users Controller class
	* PHP versions 5.1.4
	* @date Oct 14, 2010
	* @Purpose:This controller handles all the functionalities regarding user management.
	* @filesource
	* @author     Ramanpreet Pal Kaur
	* @revision
	* @copyright  Copyright @ 2009 smartData
	* @version 0.0.1 
**/
App::import('Sanitize');
class UsersController extends AppController {
	var $name =  "Users";
	var $helpers =  array('Html', 'Form', 'Javascript','Session','Validation','Ajax','Common', 'Format','Calendar');
	var $components =  array('RequestHandler','Email','Common');
	var $paginate =  array();
	var $uses =  array('User');
	var $permission_id = 2; // for customers 
	
	
	/**
	* @Date: Nov 12, 2010
	* @Method : beforeFilter
	* Created By: kulvinder singh
	* @Purpose: This function is used to validate admin user permissions
	* @Param: none
	* @Return: none 
	**/
	function beforeFilter(){
		parent::beforeFilter();
		//check session other than admin_login page
		$this->detectMobileBrowser();
		$includeBeforeFilter = array('admin_export','admin_index', 'admin_add', 'admin_status','admin_view',
					     'admin_delete', 'admin_multiplAction', 'admin_user_changepassword' );
		if (in_array($this->params['action'],$includeBeforeFilter)){
			// validate admin session
			$this->checkSessionAdmin();
			
			// validate admin users for this module
			$this->validateAdminModule($this->permission_id);
			
		}
	}
	
	
	/** 
	@function:		registration
	@description		to registration a new customer and sending an email to the registered customer
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function registration($form_product = null,$from_giftcertificate = null){
		$logged_in_user = $this->Session->read('User');
		if(!empty($logged_in_user)){
			$this->Session->setFlash('You are already a registered member.');
			$this->redirect('/homes');
		}
		$this->set('title_for_layout','Choiceful.com Registration');
		$new_customer = $this->Session->read('newcustomer');
		if ($this->RequestHandler->isMobile()) {
            	// if device is mobile, change layout to mobile
           		 $this->layout = 'mobile/home';
           		 	}else{
			$this->layout = 'front';
		}
		$titles = $this->Common->get_titles();
		$this->set('title',$titles);
		// Only for Mobile
		$this->set('form_product',$form_product);
		$this->set('from_giftcertificate',$from_giftcertificate);
		$countries = $this->Common->getcountries();
		$this->set('countries',$countries);
		App::import('Model','Address');
		$this->Address = new Address;

		if(!empty($this->data)){
			//$this->data['User']['contact_by_phone'] = $this->data['User']['contact_phone'];
			//$this->data['User']['contact_by_partner'] = $this->data['User']['contact_partner'];
			$this->data['User']['tc'] = $this->data['User']['terms_conditions'];
			$this->User->set($this->data);
			$userValidate = $this->User->validates();
			$sessUserData = $this->Session->read('sessUserData');
			if ($this->RequestHandler->isMobile()) {
				// if device is mobile, change layout to mobile
					if(!empty($userValidate)){
					$this->Session->write('sessUserData', $this->data);
					$this->redirect('/users/registration2/'.$form_product.'/'.$from_giftcertificate);
					exit;
					}
				}
			if(!empty($userValidate)){
				$original_password =$this->data['User']['newpassword'];
				$this->data['User']['password'] = md5($this->data['User']['newpassword']);

				$this->data['User']['firstname'] = ucwords(strtolower($this->data['User']['firstname']));
				$this->data['User']['lastname'] =ucwords(strtolower($this->data['User']['lastname']));

				$this->data['Address']['add_firstname'] = ucwords(strtolower($this->data['User']['firstname']));
				$this->data['Address']['add_lastname'] =ucwords(strtolower($this->data['User']['lastname']));
				$this->data['Address']['add_address1'] = ucwords(strtolower($this->data['User']['address1']));
				$this->data['Address']['add_address2'] = ucwords(strtolower($this->data['User']['address2']));
				$this->data['Address']['add_city']    = ucwords(strtolower($this->data['User']['city']));
				$this->data['Address']['country_id'] = $this->data['User']['country_id'];
				$this->data['Address']['add_state']  = $this->data['User']['state'];
				$this->data['Address']['add_phone'] = $this->data['User']['phone'];
				$this->data['Address']['add_postcode'] = $this->data['User']['postcode'];
				$this->data['Address']['primary_address'] = 1;
				
				$this->data = $this->cleardata($this->data);
				$this->data['User'] = Sanitize::clean($this->data['User']);
				$this->User->set($this->data);
				if($this->User->save()){
					$last_inserted_user = $this->User->getLastInsertId();
					$this->data['Address']['user_id'] = $last_inserted_user;
					$this->data['Address'] = Sanitize::clean($this->data['Address']);
					$this->Address->set($this->data);
					$this->Address->save($this->data);
					$this->Session->delete('newcustomer');
					/** Send email after registration **/
					$this->Email->smtpOptions = array(
						'host' => Configure::read('host'),
						'username' =>Configure::read('username'),
						'password' => Configure::read('password'),
						'timeout' => Configure::read('timeout')
					);
					
					
					//$this->Email->replyTo=Configure::read('replytoEmail');
					$this->Email->sendAs= 'html';
					$link=Configure::read('siteUrl');
					
					/******import emailTemplate Model and get template****/
					App::import('Model','EmailTemplate');
					$this->EmailTemplate = new EmailTemplate;
					/**
					table: email_templates
					id: 1
					description: Customer registration
					*/
					$template = $this->Common->getEmailTemplate(1);
					$this->Email->from = Configure::read('fromEmail');
					$data=$template['EmailTemplate']['description'];
					$this->Email->subject = $template['EmailTemplate']['subject'];
					$this->set('data',$data);
					$this->Email->to = $this->data['User']['email'];
					/******import emailTemplate Model and get template****/
					$this->Email->template='commanEmailTemplate';
					if($this->Email->send()) {
						//$this->Session->setFlash('Thanks for registration.');
					} else{
						$this->Session->setFlash('An error occurred while sending an email to the email address provided. Please reset your email address.','default',array('class'=>'flashError'));
					}
					$inserted_user_id = $this->User->getLastInsertId();
					$this->User->expects(array('Seller'));
						
					$userinfo = $this->User->find('first',array('conditions'=>array("User.id"=>$inserted_user_id),'fields'=>array("User.id","User.firstname","User.lastname","User.title","User.user_type","User.email","User.status","User.suspend_date","Seller.id")));
					$this->Session->write('User',$userinfo['User']);
					$this->User->id = $userinfo['User']['id'];
					
					//Add on 8 July 2013 for login Detail
						$ipaddress = $_SERVER['REMOTE_ADDR'];
						$currentdatetime = date("Y-m-d H:i:s");
						App::import('Model','UserLog');
						$this->UserLog = new UserLog;
						
						$this->data['UserLog']['user_id'] = $userinfo['User']['id'];
						$this->data['UserLog']['status'] = '1';
						$this->data['UserLog']['login_time'] = $currentdatetime;
						//$this->data['UserLog']['status'] = '1';
						$this->data['UserLog']['ip_address'] = $ipaddress;
						$this->UserLog->set($this->data);
						$this->UserLog->save($this->data);
						$lastLoginId = $this->UserLog->getLastInsertId();
						$this->Session->write('lastLoginId',$lastLoginId);
					//END on 8 July 2013 for login Detail
					
					$this->User->saveField('online_flag',1);
					$this->redirect('/users/my_account');
					/** Send email after registration **/
				} else {
					$this->data['User']['newpassword'] = '';
					$this->data['User']['newconfirmpassword'] = '';
					$errorArray = $this->User->validationErrors;
					$this->Session->setFlash('There was problem in saving data. Please try again later.','default',array('class'=>'flashError'));
				}
			} else {
				$this->data['User']['newpassword'] = '';
				$this->data['User']['newconfirmpassword'] = '';
				foreach($this->data['User'] as $field_index => $user_info){
					$this->data['User'][$field_index] = html_entity_decode($user_info);
					$this->data['User'][$field_index] = str_replace('&#039;',"'",$this->data['User'][$field_index]);
				}
				$errorArray = $this->User->validationErrors;

				$this->set('errors',$errorArray);
			}
		} else{
			if(!empty($new_customer)){
				$this->data['User']['email'] = $new_customer['email'];
				$this->data['User']['newpassword'] = $new_customer['password'];
			}
		}
	}
	function test(){
		$data = array();
		$data['User']['id'] ='254201';
		$data['User']['mobile_users']= 1;
		$this->User->save($data, array('validate'=>false));
		exit;
	}
	
	//Only for mobile device because registraion form is divided into two part
	function registration2($form_product = null,$from_giftcertificate = null){
	$logged_in_user = $this->Session->read('User');
		if(!empty($logged_in_user)){
			$this->Session->setFlash('You are already a registered member.');
			$this->redirect('/homes');
		}
		$new_customer = $this->Session->read('newcustomer');
		if ($this->RequestHandler->isMobile()) {
            	// if device is mobile, change layout to mobile
           		 $this->layout = 'mobile/home';
           		 	}else{
			$this->layout = 'front';
		}
		//$this->layout = 'front';
		$titles = $this->Common->get_titles();
		$this->set('title',$titles);
		//Only for Mobile
		$this->set('form_product',$form_product);
		$this->set('from_giftcertificate',$from_giftcertificate);
		$countries = $this->Common->getcountries();
		$this->set('countries',$countries);
		App::import('Model','Address');
		$this->Address = new Address;

		if(!empty($this->data)){
			
			$this->User->set($this->data);
			$userValidate = $this->User->validates();
			$this->data['User']['tc'] = $this->data['User']['terms_conditions'];
			
			       if(!empty($userValidate)){
				$original_password =$this->data['User']['newpassword'];
				$this->data['User']['password'] = md5($this->data['User']['newpassword']);
				$this->data['User']['firstname'] = ucwords(strtolower($this->data['User']['firstname']));
				$this->data['User']['lastname'] =ucwords(strtolower($this->data['User']['lastname']));
				
				$this->data['Address']['add_firstname'] = ucwords(strtolower($this->data['User']['firstname']));
				$this->data['Address']['add_lastname'] =ucwords(strtolower($this->data['User']['lastname']));
				$this->data['Address']['add_address1'] = ucwords(strtolower($this->data['User']['address1']));
				$this->data['Address']['add_address2'] = ucwords(strtolower($this->data['User']['address2']));
				$this->data['Address']['add_city']    = ucwords(strtolower($this->data['User']['city']));
				$this->data['Address']['country_id'] = $this->data['User']['country_id'];
				$this->data['Address']['add_state']  = $this->data['User']['state'];
				$this->data['Address']['add_phone'] = $this->data['User']['phone'];
				$this->data['Address']['add_postcode'] = $this->data['User']['postcode'];
				$this->data['Address']['primary_address'] = 1;
				$this->data['User'] = Sanitize::clean($this->data['User']);
				
				if ($this->RequestHandler->isMobile()) {
					$this->data['User']['mobile_users'] =1;
				}else{
					$this->data['User']['mobile_users'] =0;
				}
				$this->User->set($this->data);
				if($this->User->save()){
					$last_inserted_user = $this->User->getLastInsertId();
					$this->data['Address']['user_id'] = $last_inserted_user;
					$this->data['Address'] = Sanitize::clean($this->data['Address']);
					$this->Address->set($this->data);
					$this->Address->save($this->data);
					$this->Session->delete('newcustomer');
					/** Send email after registration **/
					$this->Email->smtpOptions = array(
						'host' => Configure::read('host'),
						'username' =>Configure::read('username'),
						'password' => Configure::read('password'),
						'timeout' => Configure::read('timeout')
					);
					
					
					//$this->Email->replyTo=Configure::read('replytoEmail');
					$this->Email->sendAs= 'html';
					$link=Configure::read('siteUrl');
					
					/******import emailTemplate Model and get template****/
					App::import('Model','EmailTemplate');
					$this->EmailTemplate = new EmailTemplate;
					/**
					table: email_templates
					id: 1
					description: Customer registration
					*/
					$template = $this->Common->getEmailTemplate(1);
					$this->Email->from = Configure::read('fromEmail');
					$data=$template['EmailTemplate']['description'];
					$this->Email->subject = $template['EmailTemplate']['subject'];
					$this->set('data',$data);
					$this->Email->to = $this->data['User']['email'];
					/******import emailTemplate Model and get template****/
					$this->Email->template='commanEmailTemplate';
					if($this->Email->send()) {
						//$this->Session->setFlash('Thanks for registration.');
					} else{
						$this->Session->setFlash('An error occurred while sending an email to the email address provided. Please reset your email address.','default',array('class'=>'flashError'));
					}
					$inserted_user_id = $this->User->getLastInsertId();
					$this->User->expects(array('Seller'));
						
					$userinfo = $this->User->find('first',array('conditions'=>array("User.id"=>$inserted_user_id),'fields'=>array("User.id","User.firstname","User.lastname","User.title","User.user_type","User.email","User.status","User.suspend_date","Seller.id")));
					$this->Session->write('User',$userinfo['User']);
					$this->User->id = $userinfo['User']['id'];
					$this->User->saveField('online_flag',1);
					//$this->redirect('/users/my_account');
					if($form_product == '1'){
						if($from_giftcertificate == '2')
							$this->redirect('/checkouts/giftcertificate_step2/1');
						else
							$this->redirect('/checkouts/step2/step2-choiceful-checkout-gift-options');
					}else{
						$this->redirect('/users/my_account');
					}
					/** Send email after registration **/
				} else {
					$this->data['User']['newpassword'] = '';
					$this->data['User']['newconfirmpassword'] = '';
					$errorArray = $this->User->validationErrors;
					$this->Session->setFlash('There was problem in saving data. Please try again later.','default',array('class'=>'flashError'));
				}
			} else {
				$this->data['User']['newpassword'] = '';
				$this->data['User']['newconfirmpassword'] = '';
				foreach($this->data['User'] as $field_index => $user_info){
					$this->data['User'][$field_index] = html_entity_decode($user_info);
					$this->data['User'][$field_index] = str_replace('&#039;',"'",$this->data['User'][$field_index]);
				}
				$errorArray = $this->User->validationErrors;
				$this->set('errors',$errorArray);
			}
		}else{
			if(!empty($new_customer)){
				$this->data['User']['email'] = $new_customer['email'];
				$this->data['User']['newpassword'] = $new_customer['password'];
			}
		}
		
	}

	/**
	*
	* Generate random string of given length
	* @param $length
	* @return $string: Random string
	*
	*/
	function get_random_string($length = 8) {
		$string = '';
			$string.=time();
		   for ($x = 1; $x <= $length; $x++) {
			  switch ( rand(1, 3) ) {
				//  Add a random digit, 0-9
				case 1:
				$string .= rand(0, 9);
				break;
				//  Add a random upper-case letter
				case 2:
				$string .= chr( rand(65, 90) );
				break;
				//  Add a random lower-case letter
				case 3:
				$string  .= chr( rand(97, 122) );
				break;
			  }
		}
		return $string;
	}


	/** 
	@function:		forgotpassword
	@description		to send an email after generating a new passord to the user
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function forgotpassword(){
		if ($this->RequestHandler->isMobile()) {
            	// if device is mobile, change layout to mobile
           			$this->layout = 'mobile/home';
           		}else{
				$this->layout = 'front';
		}
		$this->set('title_for_layout','Choiceful.com Forgot Your Password');
		if(!empty($this->data)) {
			
			$this->User->set($this->data);
			if($this->User->validates()) {
					
				$this->data = $this->cleardata($this->data);
				$this->data['User'] = Sanitize::clean($this->data['User']);
					
				$email = $this->data['User']['emailaddress'];
				$userDetails = $this->User->find('first',array('conditions'=>array("User.email"=>$email)));
				$newPassword =  $this->get_random_string();
				
				if(!empty($userDetails)){
					$this->Email->smtpOptions = array(
						'host' => Configure::read('host'),
						'username' =>Configure::read('username'),
						'password' => Configure::read('password'),
						'timeout' => Configure::read('timeout')
					);
					$this->Email->to = $userDetails['User']['email'];
					
					$this->Email->replyTo=Configure::read('replytoEmail');
					$this->Email->sendAs= 'html';
					App::import('Model','EmailTemplate');
					$this->EmailTemplate = new EmailTemplate;
					/**
					table: 		email_templates
					id:		3
					description:	User forget password
					*/
					$template = $this->Common->getEmailTemplate(3);
					$this->Email->from = $template['EmailTemplate']['from_email'];
					$data=$template['EmailTemplate']['description'];
					$data=str_replace('[userpassword]',$newPassword,$data);
					$data=str_replace('[customerfirstname]',$userDetails['User']['firstname'],$data);
					$data=str_replace('[DATE]',date('m-d-Y',time()),$data);
					$this->Email->subject = $template['EmailTemplate']['subject'];
					$this->set('data',$data);
					$this->Email->template='commanEmailTemplate';
					if($this->Email->send()){
						$this->User->id = $userDetails['User']['id'];
						$this->User->saveField('password',md5($newPassword));
						$this->Session->setFlash('An email has been sent to you which contains your new password. Please check your inbox.');
						$this->redirect(array('controllers'=>'users','action'=>'login'));
					} else {
						$this->Session->setFlash('An error occurred while sending the email to the email address provided by you. Please contact Customer Support at '.@Configure::read('phone').' to reset your email address and password.','default',array('class'=>'flashError'));
						$this->redirect(array('controllers'=>'users','action'=>'forgotpassword'));
					}
				} else{
					$errors = array();
					$errors[] = 'The email address you entered does not match our records. Please try again.';
					$this->set('errors',$errors);
				}
			} else {
				$errors = array();
				
				foreach($this->data['User'] as $field_index => $user_info){
					$this->data['User'][$field_index] = html_entity_decode($user_info);
					$this->data['User'][$field_index] = str_replace('&#039;',"'",$this->data['User'][$field_index]);
				}
				$this->set('errors',$this->User->validationErrors);
			}
		}else{
			$this->set('errors','');
		}
	}


	/** 
	@function:		login
	@description		to login
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function login($url = null) {
		$subadomain=".choiceful.com";	
		$subadomain=null;
		$currentCookieParams = session_get_cookie_params();
		$expire=time()+500;
		//Configure::write('debug',2);
		if ($this->RequestHandler->isMobile()) {
            	// if device is mobile, change layout to mobile
           			$this->layout = 'mobile/home';
           		}else{
				$this->layout = 'front';
		}
		$this->set('title_for_layout','Choiceful.com Signin');
		$url = base64_decode($url);
		$this->set('url',$url);
		$user_id=$this->Session->read('User.id');
		$temp_controller='';
		$temp_action='';
		App::import('Model','Address');
		$this->Address = new Address;
		if(!empty($user_id))
			$this->redirect('/homes/');
		$errors='';
		if(!empty($this->data)) {
			$this->User->set($this->data);
			$userValidate = $this->User->validates();
			if(!empty($userValidate)){
				$this->data = $this->cleardata($this->data);
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				if(htmlentities($this->data['User']['customer']) != 1) {
					$email = trim($this->data['User']['emailaddress']);
					$saved_password = trim($this->data['User']['password1']);
					$user_password = md5(mysql_real_escape_string(trim($this->data['User']['password1'])));
					$email = mysql_real_escape_string($email);
					$this->User->expects(array('Seller'));
					$userinfo_user = $this->User->find('first',array(
						'conditions'=>array("User.email"=>$email,"User.password"=>$user_password),
						'fields'=>array("User.id","User.title","User.user_type","User.email","User.status","User.suspend_date","User.suspend","Seller.id","User.firstname","User.lastname","Seller.status")));
					$userinfo = $userinfo_user;
					if(!empty($userinfo['User']['suspend'])) {
						if(!empty($userinfo['User']['suspend_date'])) {
							if(strtotime($userinfo['User']['suspend_date'])>strtotime(date('Y-m-d'))) {
								$suspended = true;
							} else{
								$suspended = false;
							}
						} else{
							$suspended = false;
						}
					} else{
						$suspended = false;
					}
					if($userinfo['User']['status'] == '0'){
						$this->Session->setFlash('Your account has been deactivated. Please contact us to find out more.','default',array('class'=>'flashError'));
					} elseif(!empty($suspended)) {
						$this->Session->setFlash('Your account has been temporarily suspended. Please contact us to find out more.','default',array('class'=>'flashError'));
					} elseif(($userinfo['User']['status'] == '1') ) {
						$userinfo['User']['seller_id'] = $userinfo['Seller']['id'];
						$userinfo['User']['seller_status'] = $userinfo['Seller']['status'];
						$this->Session->delete('User');
						$this->Session->delete('saved_password' );
						
						//$expire=time()+60*60*24*30;
						//setcookie("theuserloggedIn",$userinfo['Seller']['id'], $expire);
						
						$this->Session->write('User',$userinfo['User']);
						setcookie("sli_user_id", $userinfo['User']['id'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]);
						setcookie("sli_user_name", $userinfo['User']['firstname'].' '.$user_session['User']['lastname'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]); 
						setcookie("sli_user_name", $userinfo['User']['firstname'].' '.$user_session['User']['lastname'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]); 
						setcookie("sli_loginDomain", $_SERVER['HTTP_HOST'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]); 
						$this->User->id = $userinfo['User']['id'];
						$this->User->saveField('online_flag',1);
						$this->Session->write('saved_password',$saved_password);
						
						//Add on 3 July 2013 for login Detail
						$ipaddress = $_SERVER['REMOTE_ADDR'];
						$currentdatetime = date("Y-m-d H:i:s");
						App::import('Model','UserLog');
						$this->UserLog = new UserLog;
						
						$this->data['UserLog']['user_id'] = $userinfo['User']['id'];
						$this->data['UserLog']['status'] = '1';
						$this->data['UserLog']['login_time'] = $currentdatetime;
						//$this->data['UserLog']['status'] = '1';
						$this->data['UserLog']['ip_address'] = $ipaddress;
						$this->UserLog->set($this->data);
						$this->UserLog->save($this->data);
						$lastLoginId = $this->UserLog->getLastInsertId();
						$this->Session->write('lastLoginId',$lastLoginId);
						//END on 3 July 2013 for login Detail
						if(!empty($url)){
							if($url == "pages/view/contact-us" || $url == "sellers/choiceful-marketplace-sign-up"){
								$this->redirect('/'.$url);
							}else{
								$this->redirect('/'.str_replace('-','/',$url.'/1'));
							}
						}else{
							$this->redirect("/orders/view_open_orders/");
						}
					}else{
						$this->Session->setFlash('Username or password is not correct.','default',array('class'=>'flashError'));
					}
					$this->set("errors",$errors);
				}else{
					$is_already_user = $this->User->find('first',array('conditions'=>array('User.email'=>$this->data['User']['emailaddress'])));
					if(empty($is_already_user)){
						$newcustomer['email'] = $this->data['User']['emailaddress'];
						$newcustomer['password'] = $this->data['User']['password1'];
						$this->Session->write('newcustomer',$newcustomer);
						$this->redirect('/users/registration/');
					}else{
						$this->Session->setFlash('Your email address already exists in our system. Click on forgot your password if you would like us to send you a reminder','default',array('class'=>'flashError'));
					}
				}
			} else{
				$errorArray = $this->User->validationErrors;
				$this->set('errors',$errorArray);
			}
		} else{
			$this->data['User']['customer'] = 1;
		}
	}

	
	function resetUser(){
		$this->autoRender = false;
		if(isset($_POST['User'])){
			$userdata = unserialize($_POST['User']);
			$this->Session->write('User',$userdata);
			$this->Session->write('saved_password',$_POST['savedPassword']);
			$this->Session->write('lastLoginId',$_POST['lastLoginId']);
			echo "s";
		}
		else{
			echo "f";
		}
		die;
	}
	/**
	@function	:	logout
	@description	:	to logout from front end 
	@params		:	NULL
	@return		:	NULL
	created by	:	Ramanpreet Pal Kaur
	**/
	function logout(){
	
		/*$domains = explode('.',$_SERVER['HTTP_REFERER']); //print_r($domains); die('sds');
		if($domains['0'] == 'http://search'){ 
			$this->redirect('/users/sign-in');
			   die();
		}*/
		
		$this->checkSessionFrontUser(); //print_r($_SESSION); die();
		//if($this->Session->check('User.id'))
		$user_id = $this->Session->read('User.id');
		$this->User->id = $user_id;
		$this->User->saveField('online_flag',0);
		if(!empty($_SESSION)){ //print_r($_SESSION);die;
			foreach($_SESSION as $session_index => $session_variable){
				if($session_index == 'Config' || $session_index == 'SESSION_ADMIN'){
					continue;
				} else {
					echo '<script>localStorage.removeItem("User");
						localStorage.removeItem("savedPassword");
						localStorage.removeItem("lastLoginId");</script>';
					$this->Session->delete($session_index);
					$this->Session->delete('User');
					$this->Session->delete('User.id');
					$this->Session->delete('User.email');
					$subadomain=".choiceful.com";	
					$subadomain=null;
					$currentCookieParams = session_get_cookie_params();
					$expire=time()-60*60*24*30;
					setcookie("sli_user_id", '', $expire, $currentCookieParams["path"], $currentCookieParams["domain"]);
					setcookie("sli_user_name", '', $expire, $currentCookieParams["path"], $currentCookieParams["domain"]);
					setcookie("sli_total_item", '', $expire, $currentCookieParams["path"], $currentCookieParams["domain"]);
					setcookie("sli_total_price", '', $expire, $currentCookieParams["path"], $currentCookieParams["domain"]);
					setcookie("sli_loginDomain", '', $expire, $currentCookieParams["path"], $currentCookieParams["domain"]);
				}
			}
		}
		//$subadomain=".choiceful.com";	
		//$expire=time()-60*60*24*30;
		//$this->Cookie->delete('session_data');
		//$this->Cookie->delete('domain');
		//$this->Cookie->delete('currency');
		//echo '<pre>'; print_r($_COOKIE); 
		//if(!empty($_COOKIE['sli_user_id'])) {
			/*$_COOKIE['sli_user_id'] = '';
			$_COOKIE['sli_user_name'] = '';
			$_COOKIE['sli_total_item'] = '';
			$_COOKIE['sli_total_price'] = '';*/
			//setcookie("sli_user_id", '', $expire,'/',$subadomain);
			//setcookie("sli_user_name", '', $expire,'/',$subadomain);
			//setcookie("sli_total_item", '', $expire,'/',$subadomain);
			//setcookie("sli_total_price", '', $expire,'/',$subadomain);
		//}
		//echo '<pre>'; print_r($_COOKIE); die();
		//$this->Cookie->destroy();
		//$this->User->save();
		$this->Session->write("logout_action", true);
		$this->redirect("/homes");
	}

	/** 
	@function:		my_account
	@description		to display account information of a new customer
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function my_account($updatepassword = null){
		$this->set('updatepassword',$updatepassword);
		$this->checkSessionFrontUser();
		if ($this->RequestHandler->isMobile()) {
            	// if device is mobile, change layout to mobile
           		 $this->layout = 'mobile/product';
           		 	}else{
			$this->layout = 'front';
		}
		$this->set('title_for_layout','Choiceful.com: My Account - Change Name, E-Mail Address, Password');
		$user_id = $this->Session->read('User.id');
		
		
		$this->data = $this->User->find('first',array('conditions'=>array('User.id'=>$user_id),'fields'=>array('User.email','User.firstname','User.lastname')));
		
		App::import('Model','Address');
		$this->Address = new Address;
		if ($this->RequestHandler->isMobile()) {
			$this->set('user_id',$user_id);
			
			$addresses_id =  $this->Address->find('all',array('conditions'=>array('Address.user_id'=>$user_id,'Address.primary_address'=>'1'),'fields'=>array('Address.id')));
			$addresses_id=$addresses_id[0]['Address']['id'];
			$this->set('addresses_id',$addresses_id);
		}
		//$user_addinfo = $this->Address->getprimary_add($user_id,array('Address.add_firstname','Address.add_lastname'));

// 		$this->data['User']['firstname'] = $user_addinfo['Address']['add_firstname'];
// 		$this->data['User']['lastname'] = $user_addinfo['Address']['add_lastname'];
	}
	

	/** 
	@function:		my_account
	@description		to display account information of a new customer
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function manage_addresses(){
		$this->checkSessionFrontUser();
		$this->layout='front';
		$this->set('title_for_layout','Choiceful.com: My Account - Manage Address Book');
		$user_id = $this->Session->read('User.id');
		App::import('Model','Address');
		$this->Address = new Address;
			
		$this->Address->expects(array('Country'));
			
		$all_addresses =  $this->Address->find('all',array('conditions'=>array('Address.user_id'=>$user_id),'fields'=>array('Address.id','Address.add_firstname','Address.add_lastname','Address.add_address1','Address.add_address2','Address.add_postcode','Address.add_city', 'Address.add_state', 'Address.primary_address','Address.country_id','Address.add_phone'),'order'=>array('Address.primary_address DESC')));
		$this->set('addresses',$all_addresses);
	}

	/** 
	@function:		add_address
	@description		to add addresses in your addressbook
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Nov 10, 2010
	*/
	function add_address($id = null){
		$this->checkSessionFrontUser();
		if ($this->RequestHandler->isMobile()) {
			$this->layout='ajax';
		}else{
			$this->layout='front';
		}
		$this->set('title_for_layout','Choiceful.com: My Account - Add New Address');
		$user_id = $this->Session->read('User.id');
		$countries = $this->Common->getcountries();
		App::import('Model','Address');
		$this->Address = new Address;
		$firstname_length = 0;
		// pr($this->data);
		$this->set('countries',$countries);
		$name[0] = '';$name[1] = '';
		
		if(!empty($this->data)){
			
			$this->data = $this->cleardata($this->data);
			//$this->data = Sanitize::clean($this->data, array('encode' => false));
			
			if(!empty($this->data['Address']['add_name'])){
				$name = explode(' ',$this->data['Address']['add_name']);
				
			} else{
				$name[0] = '';$name[1] = '';
			}
			$this->data['Address']['user_id'] = $user_id;
			if(!empty($name[0])){
				$this->data['Address']['add_firstname'] = ucwords(strtolower($name[0]));
				$firstname_length = strlen($name[0]) + 1;
				$this->data['User']['firstname'] = ucwords(strtolower($name[0]));
			} if(!empty($name[1])){
				$this->data['Address']['add_lastname1'] = ucwords(strtolower($name[1]));
				$this->data['User']['lastname'] = ucwords(strtolower($name[1]));
			} if(!empty($firstname_length)){
				$lastnameis = substr($this->data['Address']['add_name'],$firstname_length);
				$this->data['Address']['add_lastname1'] = ucwords(strtolower($lastnameis));
				$this->data['User']['lastname'] = ucwords(strtolower($lastnameis));
			}
			$this->data['Address']['add_address1'] = ucwords(strtolower($this->data['Address']['add_address1']));
			$this->data['Address']['add_address2'] = ucwords(strtolower($this->data['Address']['add_address2']));
			$this->data['Address']['add_city'] = ucwords(strtolower($this->data['Address']['add_city']));

			$this->Address->set($this->data);
			$this->data['User']['id'] = $user_id;
			$this->User->set($this->data);
			if($this->Address->validates()){
				$this->data['Address']['add_lastname'] = $this->data['Address']['add_lastname1'];
				$this->data['Address'] = Sanitize::clean($this->data['Address']);
				$this->Address->set($this->data);
				
				if($this->Address->save($this->data)){
					$saved_fields = 1;
				} else{
					$saved_fields = 0;
				}
				//pr($this->data); die(__FILE__);
				if(!empty($this->data['Address']['id'])){
					$primary = $this->Address->find('first',array('conditions'=>array('Address.id'=>$this->data['Address']['id']),'fields'=>array('Address.primary_address')));
					$this->data['User'] = Sanitize::clean($this->data['User']);
					$this->User->set($this->data);
					if(!empty($primary['Address']['primary_address'])){
						if($this->User->save($this->data)){
							$saved_fields = 1;
						} else{
							$saved_fields = 0;
						}
					}
				}
				if($saved_fields == 1){
					$this->Session->setFlash('Address has been added successfully.');
					if ($this->RequestHandler->isMobile()) {
						$this->viewPath = 'users/mobile';
						$this->render('add_address');
					}else{
						$this->redirect(array('controller'=>'users','action'=>'manage_addresses'));
					}
				} else{
					foreach($this->data['Address'] as $field_index => $adds){
						$this->data['Address'][$field_index] = html_entity_decode($adds);
						$this->data['Address'][$field_index] = str_replace('&#039;',"'",$this->data['Address'][$field_index]);
					}
					$this->Session->setFlash('Please enter a valid full name.','default',array('class'=>'flashError'));
				}
			} else{
				foreach($this->data['Address'] as $field_index => $adds){
					$this->data['Address'][$field_index] = html_entity_decode($adds);
					$this->data['Address'][$field_index] = str_replace('&#039;',"'",$this->data['Address'][$field_index]);
				}
				$errorArray = $this->Address->validationErrors;
				$this->set('errors',$errorArray);
			}
		}else{
			if(!empty($id)){
				$this->data = $this->Address->find('first',array('conditions'=>array('Address.id'=>$id,'Address.user_id'=>$user_id)));
				
				if(!empty($this->data)){
					$this->data['Address']['id'] = $id;
					foreach($this->data['Address'] as $field_index => $adds){
						$this->data['Address'][$field_index] = html_entity_decode($adds);
						
						$this->data['Address'][$field_index] = str_replace('&#039;',"'",$this->data['Address'][$field_index]);
						
					}
				}
				$this->data['Address']['add_name'] = $this->data['Address']['add_firstname'].' '.$this->data['Address']['add_lastname'];
			}
			$this->set('errors','');
		}
	}
	/** 
	@function:		delete_address
	@description		to delete addresse from your addressbook
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Nov 10, 2010
	*/
	function delete_address($id = null){
		$this->checkSessionFrontUser();
		$user_id = $this->Session->read('User.id');
		App::import('Model','Address');
		$this->Address = new Address;
		if(!empty($id)){
			$is_address_exists = $this->Address->find('first',array('conditions'=>array('Address.id'=>$id,'Address.user_id'=>$user_id)));
			if(!empty($is_address_exists)){
				if(empty($is_address_exists['Address']['primary_address'])){
					if($this->Address->deleteAll("Address.id ='".$id."'")){
						$this->Session->setFlash("Address has been successfully deleted from your address book.");
					} else {
						$this->Session->setFlash("Address has not been deleted from your address book.",'default',array('class'=>'flashError'));
					}
				} else{
					$this->Session->setFlash("This is primary address for your account you can't delete it",'default',array('class'=>'flashError'));
				}
			} else{
				$this->Session->setFlash("That address was not existed in your address book",'default',array('class'=>'flashError'));
			}
		}
		$this->redirect('manage_addresses');
	}

	/** 
	@function:		email_alerts
	@description		
	@Created by: 		
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function email_alerts(){
		$this->checkSessionFrontUser();
		$this->set('title_for_layout','Choiceful.com: My Account - Manage Email Preferences & Alerts');
		$user_id = $this->Session->read('User.id');
		$this->layout='front';
		App::import('Model','Department');
		$this->Department = new Department;
		App::import('Model','UserDepartment');
		$this->UserDepartment = new UserDepartment;
		$departments = $this->Department->find('list',array('conditions'=>array('Department.status'=>1),'fields'=>array('id','name'),'order'=>array('Department.id')));
		$this->set('departments',$departments);
		if(!empty($this->data)){
			if(empty($this->data['User']['donot_email'])){
				$this->data['UserDepartment']['user_id'] = $user_id;
				$this->UserDepartment->deleteAll("UserDepartment.user_id ='".$user_id."'");
				foreach($this->data['email_alerts'] as $department_id){
					$this->data['UserDepartment']['id'] = 0;
					if(!empty($department_id)){
						$this->data['UserDepartment']['department_id'] = $department_id;
						$this->UserDepartment->set($this->data);
						$this->UserDepartment->save($this->data);
					}
				}
			} else{
				$this->UserDepartment->deleteAll("UserDepartment.user_id ='".$user_id."'");
			}
			$user_departments = $this->UserDepartment->find('list',array('conditions'=>array('UserDepartment.user_id'=>$user_id),'fields'=>array('id','department_id')));
			$this->set('user_departments',$user_departments);
			$this->Session->setFlash("Information has been updated successfully.");
		} else{
			$user_departments = $this->UserDepartment->find('list',array('conditions'=>array('UserDepartment.user_id'=>$user_id),'fields'=>array('id','department_id')));
			$this->set('user_departments',$user_departments);
		}
	}

	/** 
	@function:		my_account
	@description		to display account information of a new customer
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function events_calendar(){
		$this->checkSessionFrontUser();
		$this->layout='front';
		$this->set('title_for_layout','Choiceful.com: My Account - Special Occasion Calendar');
		$current_date = date('d-m-Y');
		$currnt_date = explode('-',$current_date);
		$current_month = $currnt_date[1];
		$cuurent_year = $currnt_date[2];
		$calendarValues = $this->Calendar($cuurent_year,$current_month);
		$this->set('data',$calendarValues['data']);
		$this->set('month',$calendarValues['month']);
		$this->set('year',$calendarValues['year']);
		$this->set('base_url',$calendarValues['base_url']);
	}

	/** 
	@function:		add_event
	@description		to add events for users
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function add_event($id=Null){
		$this->checkSessionFrontUser();
		$user_id = $this->Session->read('User.id');
		App::import('Model','Event');
		$this->Event = new Event;
		if(!empty($this->data)){
			
			$this->data = $this->cleardata($this->data);
			//$this->data = Sanitize::clean($this->data, array('encode' => false));
			
			$this->Event->set($this->data);
			$added_date = explode('-',$this->data['Event']['eventdate']);
			if($this->Event->validates()){
				if(!empty($this->data['Event']['eventdate'])){
					$this->data['Event']['user_id'] = $user_id;
					$this->data['Event']['event_date'] = $this->data['Event']['eventdate'];
					$added_date = explode('-',$this->data['Event']['event_date']);
					$added_year = $added_date[0];
					$added_month = $added_date[1];
					$this->data['Event'] = Sanitize::clean($this->data['Event']);
					$this->Event->set($this->data);
					if($this->Event->save($this->data['Event'])) {
						if(empty($this->data['Event']['id'])){
							$this->Session->setFlash('Event has been added successfully.');
						}else{
							$this->Session->setFlash('Event has been updated successfully.');
						}
						$this->data['Event']['id'] = '';
						$this->data['Event']['message'] = '';
					}else {
						$this->Session->setFlash('Event has not been added successfully.','default',array('class'=>'flashError'));
					}
				} else{
					$this->Session->setFlash('Please select a date from the calendar to create an event','default',array('class'=>'flashError'));
				}
			} else{
				$errorArray = $this->Event->validationErrors;
				$this->set('errors',$errorArray);
			}
			$calendar_date = $added_date;
			$display_year = $calendar_date[0];
			$display_month = $calendar_date[1];
			$mont = strtolower(date('F',strtotime($this->data['Event']['eventdate'])));
			$yr = date('Y',strtotime($this->data['Event']['eventdate']));
			$this->set('glb_month',$mont);
			$this->set('glb_year',$yr);
			$calendarValues = $this->Calendar($display_year,$display_month);
		} else{
			if(!empty($id)){
				$current_event = $this->Event->find('first',array('conditions'=>array('Event.id'=>$id)));
				$this->data = $current_event;
				$event_date = $current_event['Event']['event_date'];
				$event_date = explode('-',$event_date);
				$event_month = $event_date[1];
				$event_year = $event_date[0];
				$this->layout='ajax';
				$this->set('cal_values',1);
				$this->data['Event']['message'] = html_entity_decode($this->data['Event']['message']);
				
				$this->data['Event']['message'] = str_replace('&#039;',"'",$this->data['Event']['message']);
				$calendarValues = $this->Calendar($event_year,$event_month);
			} else{
			}
		}
		$this->set('data',$calendarValues['data']);
		$this->set('month',$calendarValues['month']);
		$this->set('year',$calendarValues['year']);
		$this->set('base_url',$calendarValues['base_url']);
		$this->layout='ajax';
	}

	/** 
	@function:		delete_event
	@description		to delete selected events for users
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Oct 18, 2010
	*/
	function delete_event($id=Null){
		if(!empty($id)){
			App::import('Model','Event');
			$this->Event = new Event;

			$current_event = $this->Event->find('first',array('conditions'=>array('Event.id'=>$id)));
			if(!empty($current_event)){
				if($this->Event->deleteAll('id='.$id))
					$this->Session->setFlash('Event has been deleted successfully.');
				else
					$this->Session->setFlash('Event has not been deleted.','default',array('class'=>'flashError'));
			}
			$event_date = $current_event['Event']['event_date'];
			$event_date = explode('-',$event_date);
			$event_month = $event_date[1];
			$event_year = $event_date[0];
			$calendarValues = $this->Calendar($event_year,$event_month);
		} else{
			$current_date = ('d-m-Y');
			$currnt_date = explode('-',$current_date);
			$current_month = $current_date[1];
			$cuurent_year = $current_date[2];
			$calendarValues = $this->Calendar($cuurent_year,$current_month);
		}
	}

	/**
	@function	:	Calendar
	@description	:	User calendar in events
	@params		:	$year,$month
	@created	:	
	@credated by	:	Ramanpreet Pal Kaur
	**/
	function Calendar($year=null,$month=null) {
		################# calendar ###################################
		$calendarAjax=0;
		if(is_numeric($month))
			$month = strtolower(date( 'F', mktime(0, 0, 0, $month) ));
		$month_list = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');

		$day_list = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
		// NOT not used in the current helper version but used in the data array
		if(intval($year)>1900) {
			$year=$year;
		}else {
			$year=date('Y');
		}

		if(in_array($month,$month_list))
			$month=$month;
		else
			$month=date('M');

		$data = null;
		if(is_null($year) || is_null($month)) {
			$year = date('Y');
			$month = date('M');
			$month_num = date('n');
			$item = null;
		}


		$flag = 0;
		for($i = 0; $i < 12; $i++) { // check the month is valid if set
			if(strtolower($month) == $month_list[$i]) {
				if(intval($year) != 0) {
					$flag = 1;
					$month_num = $i + 1;
					$month_name = $month_list[$i];
					break;
				}
			}
		}
		if($flag == 0) { // if no date set, then use the default values
			$year = date('Y');
			$month = date('M');
			$month_name = date('F');
			$month_num = date('m');
		}
		$data=array();
		$returnVal=array();
		$returnVal['data']=$data;
		$returnVal['month']=$month;
		$returnVal['year']=$year;
		$returnVal['base_url']= Configure::read('siteUrl');
		$this->set('data',$returnVal['data']);
		$this->set('month',$returnVal['month']);
		$this->set('year',$returnVal['year']);
		$this->set('base_url',$returnVal['base_url']);
		if($this->RequestHandler->isAjax() == 0) {
			$this->layout = 'front';
		}else {
			$calendarAjax=1;
			$this->layout='ajax';
			Configure::write('debug', 3);
			$this->set('calendarAjax',$calendarAjax);
			$this->viewPath = 'elements/useraccount' ;
			$this->render('calendar');
		}
		return $returnVal;
		################# calendar ###################################
	}


	
	
	/********************************************** */
	
	/** 
	@function:		admin_index
	@description		to display all buyers on admin end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		
	*/
	function admin_index() {
		
		$user_id = $this->Session->read('SESSION_ADMIN.id');
		$this->layout='layout_admin';
		
		App::import('Model','VCustomerOrder');
		$this->VCustomerOrder = new VCustomerOrder;
		
		$criteria = 1;
		/** for paging and sorting we are setting values */
		if(empty($this->data)){
 			if(isset($this->params['named']['searchin']))
				$this->data['Search']['searchin']=$this->params['named']['searchin'];
			else
				$this->data['Search']['searchin']='';
				
			if(isset($this->params['named']['keyword']))
				$this->data['Search']['keyword']=$this->params['named']['keyword'];
			else
				$this->data['Search']['keyword']='';
			if(isset($this->params['named']['showtype']))
				$this->data['Search']['show']=$this->params['named']['showtype'];
			else
				$this->data['Search']['show']='';
		}
		/** **************************************** **/
		$this->set('title_for_layout','Manage Customers');
		$value = '';	
		$show = '';
		$matchshow = '';
		$fieldname = '';
		/** SEARCHING **/
		$reqData = $this->data;
		$options['All'] = 'All';
		$options['firstname'] = "Firstname";
		$options['lastname'] = "Lastname";
		$options['email'] = "Email";
		$options['mobile_users'] = "Mobile Users";
		$showArr = $this->getStatus();
		$this->set('showArr',$showArr);
		$this->set('options',$options);
			
			
		if(!empty($this->data['Search'])){
			if(!empty($this->data['Search']['searchin'])){
				$fieldname = $this->data['Search']['searchin'];
			} else{
				$fieldname = 'All';
			}
				
			$value = $this->data['Search']['keyword'];
            if(isset($this->data['Search']['show']) && !empty($this->data['Search']['show'])){
				$show = $this->data['Search']['show'];
			} else{
				$show = 'All';
			}
			if($show == 'Active'){
				$matchshow = '1';
			}
			if($show == 'Deactive'){
				$matchshow = '0';
			}
				
				
			$value1 = Sanitize::escape($value);
			
			
			if($value !="") {
				if(trim($fieldname)=='All'){
					$criteria .= " and (VCustomerOrder.firstname LIKE '%".$value1."%' OR VCustomerOrder.lastname LIKE '%".$value1."%' OR VCustomerOrder.email LIKE '%".$value1."%')";
				} else {
					if(trim($fieldname)!=''){
						if(isset($value) && $value!=="") {
							if($fieldname == "mobile_users"){
								$criteria .= " and VCustomerOrder.".$fieldname."= '1'";
							}else{
								$criteria .= " and VCustomerOrder.".$fieldname." LIKE '%".$value1."%'";
							}
						}
					}
				}
			}
			if($fieldname == "mobile_users"){
				$criteria .= " and VCustomerOrder.".$fieldname."= '1'";
			}
			if(isset($show) && $show!==""){
				if($show == 'All'){
				} else {
					$criteria .= " and VCustomerOrder.status = '".$matchshow."'";
					$this->set('show',$show);
				}
			}
			
		}
		$this->set('keyword', $value);
		$this->set('show', $show);
		$this->set('fieldname',$fieldname);
		$this->set('heading','Manage Users');	
			
			
		/* ******************* page limit sction **************** */
		$sess_limit_name = $this->params['controller']."_limit";
		$sess_limit_value = $this->Session->read($sess_limit_name);
		if(!empty($this->data['Record']['limit'])){
		   $limit = $this->data['Record']['limit'];
		   $this->Session->write($sess_limit_name , $this->data['Record']['limit'] );
		}elseif( !empty($sess_limit_value) ){
			$limit = $sess_limit_value;
		}else{
			$limit = $this->records_per_page;  // set default value
		}
		$this->data['Record']['limit'] = $limit;
		/* ******************* page limit sction **************** */
		
		
		
		$this->paginate = array(
			'limit' => $limit,
			'order' => array(
				'VCustomerOrder.id' => 'DESC'
			),
			'conditions'=>array('VCustomerOrder.user_type'=>0),
			'fields' => array(
				'VCustomerOrder.id',
				'VCustomerOrder.mobile_users',
				'VCustomerOrder.title',
 				'VCustomerOrder.firstname',
 				'VCustomerOrder.lastname',
				'VCustomerOrder.email',
				'VCustomerOrder.password',
				'VCustomerOrder.status',
				'VCustomerOrder.user_type',
				'VCustomerOrder.modified',
				'VCustomerOrder.created',
				'VCustomerOrder.total_order',
				'VCustomerOrder.total_logins',
				'VCustomerOrder.login_time',
			)
		);
		$criteria_mobile = $criteria;
		$criteria_mobile .= " and VCustomerOrder.mobile_users = '1'";
		//$this->set('total_users',$this->VCustomerOrder->find('count',array('conditions'=>$criteria)));
		$this->set('mobile_users',$this->VCustomerOrder->find('count',array('conditions'=>$criteria_mobile)));
		$this->set('usersArr',$this->paginate('VCustomerOrder',$criteria));	
			
			
			
			
			
			
			
		/*if(!empty($this->data['Search'])){
			if(!empty($this->data['Search']['searchin'])){
				$fieldname = $this->data['Search']['searchin'];
			} else{
				$fieldname = 'All';
			}
				
			$value = $this->data['Search']['keyword'];
			if(!empty($this->data['Search']['show'])){
				$show = $this->data['Search']['show'];
			} else{
				$show = 'All';
			}
			if($show == 'Active'){
				$matchshow = '1';
			}
			if($show == 'Deactive'){
				$matchshow = '0';
			}
				
				
			$value1 = Sanitize::escape($value);
			
			
			if($value !="") {
				if(trim($fieldname)=='All'){
					$criteria .= " and (User.firstname LIKE '%".$value1."%' OR User.lastname LIKE '%".$value1."%' OR User.email LIKE '%".$value1."%')";
				} else {
					if(trim($fieldname)!=''){
						if(isset($value) && $value!=="") {
							$criteria .= " and User.".$fieldname." LIKE '%".$value1."%'";
						}
					}
				}
			}
			if(isset($show) && $show!==""){
				if($show == 'All'){
				} else {
					$criteria .= " and User.status = '".$matchshow."'";
					$this->set('show',$show);
				}
			}
			
		}
		$this->set('keyword', $value);
		$this->set('show', $show);
		$this->set('fieldname',$fieldname);
		$this->set('heading','Manage Users');
		
		/* ******************* page limit sction **************** */
		/*$sess_limit_name = $this->params['controller']."_limit";
		$sess_limit_value = $this->Session->read($sess_limit_name);
		if(!empty($this->data['Record']['limit'])){
		   $limit = $this->data['Record']['limit'];
		   $this->Session->write($sess_limit_name , $this->data['Record']['limit'] );
		}elseif( !empty($sess_limit_value) ){
			$limit = $sess_limit_value;
		}else{
			$limit = $this->records_per_page;  // set default value
		}
		$this->data['Record']['limit'] = $limit;*/
		/* ******************* page limit sction **************** */
		
		
		
		/*$this->paginate = array(
			'limit' => $limit,
			'order' => array(
				'User.id' => 'DESC'
			),
			'conditions'=>array('User.user_type'=>'0'),
			'fields' => array(
				'User.id',
				'User.title',
 				'User.firstname',
 				'User.lastname',
				'User.email',
				'User.password',
				'User.status',
				'User.user_type',
				'User.modified',
				'User.created',
			)
		);
		$this->set('total_users',$this->User->find('count',array('conditions'=>$criteria)));
		$this->set('usersArr',$this->paginate('User',$criteria));*/
		
	}


	
	/** 
	@function:		admin_add
	@description		to add all buyers from admin end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		
	*/
	function admin_add($id = null) {
		if(!is_null($id) ){
			$this->set('listTitle','Update');
			$id = base64_decode($id);
		}else{
			$this->set('listTitle','Add New');
		}
		
		$this->set('id',$id);
		$logged_in_user_id = $this->Session->read('SESSION_ADMIN.id');
		$this->layout = 'layout_admin';
			
		$titles = $this->Common->get_titles();
		$this->set('title',$titles);
		
		App::import('Model','Address');
		$this->Address = new Address;
		
		// get a list of all country 
		$countries = $this->Common->getcountries();
		$this->set('countries',$countries);
		

		if(!empty($this->data)){
			
			$this->data['User']['tc'] = '1';
			$this->User->set($this->data);
			$this->Address->set($this->data);
			$userValidate = $this->User->validates();
			$addValidate = $this->Address->validates();
			if(!empty($userValidate) && !empty($addValidate) ){
				if(!empty($this->data['Address']['id'])){
					$this->data['Address']['add_firstname'] = ucwords(strtolower($this->data['User']['firstname']));
					$this->data['Address']['add_lastname']   = ucwords(strtolower($this->data['User']['lastname']));
				}
				
			
				$this->data['User']['firstname'] = ucwords(strtolower($this->data['User']['firstname']));
				$this->data['User']['lastname']  = ucwords(strtolower($this->data['User']['lastname']));
				
				$this->data['Address']['add_address1'] = ucwords(strtolower($this->data['Address']['add_address1']));
				$this->data['Address']['add_address2'] = ucwords(strtolower($this->data['Address']['add_address2']));
				$this->data['Address']['add_city']     = ucwords(strtolower($this->data['Address']['add_city']));
				
				$this->data['Address']['primary_address'] = 1;
				if(!empty($this->data['User']['id']) ){ // in case of edit user
					if(empty($this->data['Address']['user_id'])){
						$this->data['Address']['user_id'] = $this->data['User']['id'];
					}
					$this->data['Address'] = Sanitize::clean($this->data['Address']);
					$this->Address->set($this->data);

					//pr($this->data); die;
					$this->Address->save(); // save address  information
				
					if(!empty($this->data['User']['suspend_date'])){
						$sus_date_array = explode('/',$this->data['User']['suspend_date']);
						$sus_date = $sus_date_array[2].'-'.$sus_date_array[1].'-'.$sus_date_array[0];
						$this->data['User']['suspend_date'] = $sus_date;
					}
					$this->data['User'] = Sanitize::clean($this->data['User']);
					$this->User->set($this->data);
					if($this->User->save()){ // save user information 
						$this->Session->setFlash('Information updated successfullly.');
						$this->redirect('/admin/users/');
					}else{
						$this->Session->setFlash('Information  not updated.','default',array('class'=>'flashError'));
					}
				} else { // Add customer
					if(!empty($this->data['User']['password'])){
						$original_password = $this->data['User']['password'];
						$this->data['User']['password'] = md5($this->data['User']['password']);
						$this->data['User']['confirmpassword'] = md5($this->data['User']['confirmpassword']);
					}
					if(!empty($this->data['User']['suspend_date'])){
						$sus_date_array = explode('/',$this->data['User']['suspend_date']);
						$sus_date = $sus_date_array[2].'-'.$sus_date_array[1].'-'.$sus_date_array[0];
						$this->data['User']['suspend_date'] = $sus_date;
					}
					
					$this->data['User'] = Sanitize::clean($this->data['User']);
					$this->User->set($this->data);
					if($this->User->save()){
					// first time addition
					$this->data['Address']['user_id'] = $this->User->getLastInsertId();
					$this->data['Address']['add_firstname'] = ucwords(strtolower($this->data['User']['firstname']));
					$this->data['Address']['add_lastname']   = ucwords(strtolower($this->data['User']['lastname']));
					//  save the address book records
					$this->data['Address'] = Sanitize::clean($this->data['Address']);
					$this->Address->set($this->data['Address']);
					$this->Address->save(); // save address  information
						/** Send email after registration **/
						$this->Email->smtpOptions = array(
							'host' => Configure::read('host'),
							'username' =>Configure::read('username'),
							'password' => Configure::read('password'),
							'timeout' => Configure::read('timeout')
						);
						
						$this->Email->replyTo=Configure::read('replytoEmail');
						$this->Email->sendAs= 'html';
						App::import('Model','EmailTemplate');
						$this->EmailTemplate = new EmailTemplate;
						$link=Configure::read('siteUrl');
						/******import emailTemplate Model and get template****/
						App::import('Model','EmailTemplate');
						$this->EmailTemplate = new EmailTemplate;
						/**
						table: 		email_templates
						id:		1
						description:	Customer registration
						*/
						$template = $this->Common->getEmailTemplate(1);
						$this->Email->from = $template['EmailTemplate']['from_email'];
						
						$data=$template['EmailTemplate']['description'];
						$this->Email->subject = $template['EmailTemplate']['subject'];
						$this->set('data',$data);
						$this->Email->to = $this->data['User']['email'];
						/******import emailTemplate Model and get template****/
						$this->Email->template='commanEmailTemplate';
						if($this->Email->send()) {
							$this->Session->setFlash('New customer added successfully.');
							$this->redirect('/admin/users/');
						} else{
							$this->Session->setFlash('An error occurred while sending the email to the email address provided by you.','default',array('class'=>'flashError'));
							$this->redirect('/admin/users/');
						}
						/** Send email after registration **/
					} else {
						if(!empty($this->data['User']['suspend_date'])){
							$new_sus_date_array = explode('-',$this->data['User']['suspend_date']);
							$new_sus_date = $new_sus_date_array[2].'/'.$new_sus_date_array[1].'/'.$new_sus_date_array[0];
							$this->data['User']['suspend_date'] = $new_sus_date;
						}
						
						foreach($this->data['User'] as $field_index => $user_info){
							$this->data['User'][$field_index] = html_entity_decode($user_info);
							$this->data['User'][$field_index] = str_replace('&#039;',"'",$this->data['User'][$field_index]);
						}
						foreach($this->data['User'] as $field_index => $user_info){
							$this->data['User'][$field_index] = html_entity_decode($user_info);
							$this->data['User'][$field_index] = str_replace('&#039;',"'",$this->data['User'][$field_index]);
						}
						$this->Session->setFlash('Customer not added successfully.','default',array('class'=>'flashError'));
					}
				}
			} else {
				foreach($this->data['User'] as $field_index => $user_info){
					$this->data['User'][$field_index] = html_entity_decode($user_info);
					$this->data['User'][$field_index] = str_replace('&#039;',"'",$this->data['User'][$field_index]);
				}
				$errorArray1 = $this->User->validationErrors;
				$errorArray2 = $this->Address->validationErrors;
				$errorArray = array_merge($errorArray1,$errorArray2);
				$this->set('errors',$errorArray);
			}
		} else{
			if(!empty($id)) {
				$this->User->id = $id;
				$this->data= $this->User->read();
				$addressdataArr = $this->Address->find('first',array('conditions'=>array('Address.user_id'=>$id, 'Address.primary_address' => '1')));
				$this->data['Address'] = $addressdataArr['Address'];
				//pr($this->data);
				$new_sus_date = array();
				if(!empty($this->data['User']['suspend_date'])){
					$new_sus_date_array = explode('-',$this->data['User']['suspend_date']);
					$new_sus_date = $new_sus_date_array[2].'/'.$new_sus_date_array[1].'/'.$new_sus_date_array[0];
					$this->data['User']['suspend_date'] = $new_sus_date;
				}
				//pr($this->data);
				foreach($this->data['User'] as $field_index => $user_info){
					$this->data['User'][$field_index] = html_entity_decode($user_info);
					$this->data['User'][$field_index] = str_replace('&#039;',"'",$this->data['User'][$field_index]);
				}
			}
		}
	}

	/** 
	@function:		admin_status
	@description		to change status of buyers from admin end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		
	*/
	function admin_status($id = null,$status= null) {
		
		if(!empty($id)) {
			$this->data['User']['id'] = $id;
			if($status == 0){
				$this->data['User']['status'] = 1;
			} else{
				$this->data['User']['status'] = 0;
			}
			$this->User->save($this->data);
		}
		$this->Session->setFlash('Information updated successfully.');
		$this->redirect('index');
	}

	/** 
	@function:		admin_delete
	@description		to delete selected buyer from admin end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		
	*/
	function admin_delete($id = null) {
			
		App::import('Model','Order');
		$this->Order = new Order;
		$user_orders = $this->Order->find('all',array('conditions'=>array('Order.user_id'=>$id)));
			
		if(empty($user_orders)){
			$this->User->expects(array('Address','CertificateReview','Event','Review','UserDepartment', 'Seller' ));
			$this->User->id =  $id;
			if($this->User->delete($id)){
				$this->Session->setFlash('Record has been deleted successfully.');	
				$this->redirect('/admin/users/index');
			}else{
				
				$this->Session->setFlash('Error in deleting record.','default',array('class'=>'flashError'));
			}
			$this->redirect('/admin/users/index');
		} else{
			$this->User->id =  $id;
			$this->User->saveField('status',0);
			$this->Session->setFlash('This user has placed orders with choiceful so can\'t delete it.','default',array('class'=>'flashError'));
			$this->redirect('/admin/users/index');
		}
	}


	
	/** 
	@function:		admin_multiplAction
	@description		to delete,active, inactive selected buyers from admin end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		
	*/
	function admin_multiplAction(){
		if($this->data['User']['status']=='active'){
			foreach($this->data['select'] as $id){
				if(!empty($id)){
					$this->User->id=$id;
					$this->User->saveField('status','1');
					$this->Session->setFlash('Information updated successfully.');
				}
			}
		} elseif($this->data['User']['status']=='inactive'){
			foreach($this->data['select'] as $id){
				if(!empty($id)){
					$this->User->id=$id;
					$this->User->saveField('status','0');
					$this->Session->setFlash('Information updated successfully.');	
				}
			}
		} elseif($this->data['User']['status']=='del'){
			App::import('Model','Order');
			$this->Order = new Order;
			$user_orders = $this->Order->find('all',array('conditions'=>array('Order.user_id'=>$id)));
			if(empty($user_orders)){
				$this->User->expects(array('Address','CertificateReview','Event','Review','UserDepartment','CertificateSearctag'));
				foreach($this->data['select'] as $id){
					if(!empty($id)){
						$this->User->delete($id);
						$this->Session->setFlash('Information deleted successfully.');	
					}
				}
			} else{
				$this->User->id =  $id;
				$this->User->saveField('status',0);
				$this->Session->setFlash('This user has placed orders with choiceful so can\'t delete it.','default',array('class'=>'flashError'));
			}
		}
		/** for searching and sorting*/
		if(empty($this->data)){
			if(isset($this->params['named']['searchin']))
				$this->data['Search']['searchin']=$this->params['named']['searchin'];
			else
				$this->data['Search']['searchin']='';
			if(isset($this->params['named']['keyword']))
				$this->data['Search']['keyword']=$this->params['named']['keyword'];
			else
				$this->data['Search']['keyword']='';
			if(isset($this->params['named']['showtype']))
				$this->data['Search']['show']=$this->params['named']['showtype'];
			else
				$this->data['Search']['show']='';
		}
		/** for searching and sorting*/
		$this->redirect('/admin/users/index/keyword:'.$this->data['Search']['keyword'].'/searchin:'.$this->data['Search']['searchin'].'/showtype:'.$this->data['Search']['show']);
	}


	/**
	@function:admin_view 
	@description		view users details,
	@Modify:		NULL
	@Created Date:		Oct 13,2010
	*/
	function admin_view($id){
		$id = base64_decode($id);
		$this->_validateId($id);
		$this->layout = 'layout_admin';
		$this->set('list_title','View Customer Details');
		$this->User->id = $id;
		$this->User->expects(array('UserDepartment'));
		
		$this->data = $this->User->read();
		
		App::import('Model','Address');
		$this->Address = new Address;
		$this->Address->expects(array('Country'));
		$primary_add = $this->Address->getprimary_add($id);
		$this->data['User']['address1'] = $primary_add['Address']['add_address1'];
		$this->data['User']['address2'] = $primary_add['Address']['add_address2'];
		$this->data['User']['postcode'] = $primary_add['Address']['add_postcode'];
		$this->data['User']['city'] = $primary_add['Address']['add_city'];
		$this->data['User']['state'] = $primary_add['Address']['add_state'];
		$this->data['User']['phone'] = $primary_add['Address']['add_phone'];
		$this->data['Country'] = $primary_add['Country'];
		
		$departments = $this->Common->getdepartments();
		$this->set('departments',$departments);
	}

	/** 
	@function:		validateId 
	@description:		Validate of ID,
	@params:		id
	@Modify:		NULL
	@Created Date:		Oct 13,2010
	*/
	function _validateId($id){
		if(empty($id) || !is_numeric($id)){
			$this->Session->setFlash('Id is missing.','default',array('class'=>'flashError'));
			$this->redirect('/admin/users/');
		}
	}
	
	
	/** 
	@function:		admin_export 
	@description:		export users data as csv
	@params:		
	@Modify:		
	@Created Date:		Oct 20,2010
	*/
	function admin_export(){
		$countries = $this->Common->getcountries();
		App::import('Model','Address');
		$this->Address = new Address;
		$this->data = $this->User->find('all',array('conditions'=>array('User.user_type'=>'0')));
		#Creating CSV
 		$csv_output = "Title, First Name, Last Name, Email, Address1, Address2, Town/City, State/County, Postcode, Country, Phone, Contact By Phone, Contact By partner, Status, Created on Date ";
		$csv_output .= "\n";
		
		if(count($this->data) > 0){
			foreach($this->data as $value){
				
				foreach($value['User'] as $field_index => $user_info){
					$value['User'][$field_index] = html_entity_decode($user_info);
					$value['User'][$field_index] = str_replace('&#039;',"'",$value['User'][$field_index]);
				}
				


				$addressDetails = $this->Address->getprimary_add($value['User']['id']);
				
// 				foreach($addressDetails['Address'] as $field_index => $add_info){
// 					$addressDetails['Address'][$field_index] = html_entity_decode($add_info);
// 					$addressDetails['Address'][$field_index] = str_replace('&#039;',"'",$addressDetails['Address'][$field_index]);
// 				}
				$contact_by_phone    = $value['User']['contact_by_phone'] == 1? 'Yes':'No';
				$contact_by_partner  = ($value['User']['contact_by_partner'] == 1)?('Yes'):('No');
				$user_status 	     = ($value['User']['status'] == 1)?('Active'):('InActive');
				$created_date        = date('m/d/Y',strtotime($value['User']['created']));
				$address1 = $addressDetails['Address']['add_address1'];
				$address2 = $addressDetails['Address']['add_address2'];
				$postcode = $addressDetails['Address']['add_postcode'];
				$state_county = $addressDetails['Address']['add_state'];
				$city = $addressDetails['Address']['add_city'];
				if(!empty($addressDetails['Address']['add_phone'])){
					$phone_number = "#".$addressDetails['Address']['add_phone'];
				}else{
					$phone_number = $addressDetails['Address']['add_phone'];
				}
				$country_id = $addressDetails['Address']['country_id'];
				$country_name = (!empty($country_id) )?($countries[$country_id]):('');
				$csv_output .="".str_replace(",",' || ',
				$value['User']['title']).",".str_replace(",",' || ',
				$value['User']['firstname']).",".str_replace(",",' || ',
				$value['User']['lastname']).",".str_replace(",",' || ',
				$value['User']['email']).",".str_replace(",",' || ',
				$address1).",".str_replace(",",' || ',
				$address2).",".str_replace(",",' || ',
				$city).",".str_replace(",",' || ',
				$state_county).",".str_replace(",",' || ',
				$postcode).",".str_replace(",",' || ',
				$country_name).",".str_replace(",",' || ',
				$phone_number).",".str_replace(",",' || ',
				$contact_by_phone).",".str_replace(",",' || ',
				$contact_by_partner).",".str_replace(",",' || ',
				$user_status).",".str_replace(",",' || ',
				$created_date).",\n";
			}
		}else{
			$csv_output .= "No Record Found.."; 
		}
		$filePath="users_".date("Ymd").".csv";
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=".$filePath."");
		header("Pragma: no-cache");
		header("Expires: 0");
		print $csv_output;
		exit;
	}

	/** @Date: Nov 11, 2010
	* @Method : admin_change_password
	* Created By: Ramanpreet Pal Kaur
	* @Purpose: This function is to change users password by admin.
	* @Param: $id
	* @Return: none 
	**/
	function admin_user_changepassword($id = null){
		$this->set('title_for_layout', 'Change Customer Password');
		$this->layout = 'layout_admin';
		$this->set('id',$id);
		if(!empty($this->data)) {
			
			
			$errors = array();
			$this->User->set($this->data);
			if($this->User->validates()){
				$this->data['User']['password'] = $this->data['User']['newpassword'];
				$this->data['User']['confirmpassword'] = $this->data['User']['newconfirmpassword'];
				$this->data['User'] = $this->data['User'];
				$userid =  $id;
				$userDetails = $this->User->findById($userid,array('password','id'));
				if(!empty($userDetails)){
					$this->User->id = $id;
					$this->User->saveField('password',md5($this->data['User']['password']));
					$this->Session->setFlash('Password changed successfully');
					$this->redirect('/admin/users');
					$this->data['User']['newpassword'] = '';
					$this->data['User']['newconfirmpassword'] = '';
				} else{
					$this->Session->setFlash('User not exists.','default',array('class'=>'flashError'));
				}
			} else{
				$this->set('errors',$this->User->validationErrors);
			}
		} else{

		}
	}


	/** @Date: Nov 11, 2010
	* @Method : send_eventmail
	* Created By: Ramanpreet Pal Kaur
	* @Purpose: This function is to send mails to all users for upcoming events added by them (By cron job)
	* @Param: none
	* @Return: none
	**/
	function send_eventmail(){
		
		//ini_set('memory_limit', '9999M');
		//set_time_limit(0);
		App::import('Model','Event');
		$this->Event = new Event;
		$current_date = date('Y-m-d');
		//$next_date = date("Y-m-d", strtotime($current_date)+(48*60*60));
		$this->Event->expects(array('User'));
		//$all_upcoming_events = $this->Event->find('all',array('conditions'=>array('Event.event_date = "'.$next_date.'"'),'fields'=>array('Event.id','Event.user_id','Event.message','Event.event_date','User.id','User.firstname','User.lastname','User.email'),'order'=>'Event.user_id'));
		$all_upcoming_events = $this->Event->find('all',array('conditions'=>array('DATE_ADD(curdate(), INTERVAL 1 DAY) = Event.event_date'),'fields'=>array('Event.id','Event.user_id','Event.message','Event.event_date','User.id','User.firstname','User.lastname','User.email'),'order'=>'Event.user_id'));
		if(!empty($all_upcoming_events)){
			/** Send upcominf events email **/
			$this->Email->smtpOptions = array(
				'host' => Configure::read('host'),
				'username' =>Configure::read('username'),
				'password' => Configure::read('password'),
				'timeout' => Configure::read('timeout')
			);
			
			$this->Email->replyTo=Configure::read('replytoEmail');
			$this->Email->sendAs= 'html';
			$link=Configure::read('siteUrl');
			/******import emailTemplate Model and get template****/
			App::import('Model','EmailTemplate');
			$this->EmailTemplate = new EmailTemplate;
			foreach($all_upcoming_events as $event){
				/**
				table: 		email_templates
				id:		4
				description:	Customer event
				*/
				$template = $this->Common->getEmailTemplate(4);

				$this->Email->from = $template['EmailTemplate']['from_email'];
				$data=$template['EmailTemplate']['description'];
				$event_date = date('l j F, Y',strtotime($event['Event']['event_date']));
				$data=str_replace('[DayDateMonth]',$event_date,$data);
				$data=str_replace('[EventTitle]',$event['Event']['message'],$data);
				$template['EmailTemplate']['subject'] = str_replace('[EventTitle]', $event['Event']['message'], $template['EmailTemplate']['subject']);
				$this->Email->subject = $template['EmailTemplate']['subject'];
				$this->set('data',$data);
				$this->Email->to = $event['User']['email'];
				//$this->Email->to = array('vcvreddy@yahoo.com','gyanp.sdei@gmail.com');
				
				
				/******import emailTemplate Model and get template****/
				$this->Email->template='commanEmailTemplate';
				$this->Email->send();
			}
		}
		die;
	}



	/** 
	@function:		sign_in
	@description		to login with pop up window
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Nov  26, 2010
	*/
	function sign_in($from_url = null) {
		$subadomain=".choiceful.com";
		$subadomain=null;
		$currentCookieParams = session_get_cookie_params();
		if ($this->RequestHandler->isMobile()) {
            	// if device is mobile, change layout to mobile
           		 $this->layout = 'ajax';
           		 $divid=$from_url;
          		 $this->set('updateDivId',$divid);
           		 }else{
			$this->layout = 'front_popup';
		}
		$user_id=$this->Session->read('User.id');
		$temp_controller='';
		$temp_action='';
		$errors='';
		if(!empty($this->data)) {
			//echo "<pre>";print_r($this->data);echo "</pre>";die;
			$this->data = $this->cleardata($this->data);
			//$this->data = Sanitize::clean($this->data, array('encode' => false));
			
			$this->User->set($this->data);
			$userValidate = $this->User->validates();
			if(!empty($userValidate)){
				
				$email = mysql_real_escape_string(trim($this->data['User']['emailaddress']));
				$saved_password = $this->data['User']['password1'];
				$user_password = md5(mysql_real_escape_string(trim($this->data['User']['password1'])));
				$this->User->expects(array('Seller'));
				$userinfo = $this->User->find('first',array('conditions'=>array("User.email"=>$email,"User.password"=>$user_password),'fields'=>array("User.id","User.title","User.user_type","User.email","User.firstname","User.lastname","User.status","User.suspend_date","Seller.id","Seller.status")));
				if(!empty($userinfo['User']['suspend'])) {
					if(!empty($userinfo['User']['suspend_date'])) {
						if(strtotime($userinfo['User']['suspend_date'])>strtotime(date('Y-m-d'))) {
							$suspended = true;
						} else{
							$suspended = false;
						}
					} else{
						$suspended = false;
					}
				} else{
					$suspended = false;
				}
				if($userinfo['User']['status'] == '0'){
					$this->Session->setFlash('Your account has been deactivated. Please contact us to find out more.','default',array('class'=>'flashError'));
				} elseif(!empty($suspended)) {
					$this->Session->setFlash('Your account has been temporarily suspended. Please contact us to find out more.','default',array('class'=>'flashError'));
				} elseif(($userinfo['User']['status'] == '1') ) {
					//print_R($userinfo);die;
					$userinfo['User']['seller_id'] = $userinfo['User']['id'];
					$userinfo['User']['seller_status'] = $userinfo['User']['status'];
					$this->Session->delete('User');
					$this->Session->delete('saved_password' );
					$this->Session->write('User',$userinfo['User']);
					$this->Session->write('saved_password',$saved_password);
					$this->User->id = $userinfo['User']['id'];
					$this->User->saveField('online_flag',1);
					setcookie("sli_user_id", $userinfo['User']['id'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]);
					setcookie("sli_user_name", $userinfo['User']['firstname'].' '.$user_session['User']['lastname'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]); 
					setcookie("sli_user_name", $userinfo['User']['firstname'].' '.$user_session['User']['lastname'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]); 
					setcookie("sli_loginDomain", $_SERVER['HTTP_HOST'], $expire, $currentCookieParams["path"], $currentCookieParams["domain"]); 
					$this->User->id = $userinfo['User']['id'];
					$this->User->saveField('online_flag',1);
					$this->Session->write('saved_password',$saved_password);
// 					echo "<script type=\"text/javascript\">parent.jQuery.fancybox.close();
// 					parent.jQuery.location.reload(true);
// 					</script>";
					//echo "<script type=\"text/javascript\">parent.location.reload(true);parent.jQuery.fancybox.close();</script>";
					if ($this->RequestHandler->isMobile()) {
						echo "<script type=\"text/javascript\">parent.location.reload(true);</script>";
					}else{
						echo "<script type=\"text/javascript\">parent.location.reload(true);parent.jQuery.fancybox.close();</script>";
					}
					
				} else {
					$this->Session->setFlash('The email address or password is not correct, please check and try again.','default',array('class'=>'flashError'));
				}
			} else{
				$errorArray = $this->User->validationErrors;
				$this->set('errors',$errorArray);
			}
		} else{
		}
	}


	/** 
	@function:		update_name
	@description		to update users firstname and last name from front end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		Nov  26, 2010
	*/
	function update_name(){
		$this->layout = 'ajax';
		$userId = $this->Session->read('User.id');
		$this->viewPath = 'elements/useraccount' ;

		if(!empty($userId)){
			if(!empty($this->data)){
					
				$this->data = $this->cleardata($this->data);
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				
				$this->User->set($this->data);
				if($this->User->validates()){
					App::import('Model','Address');
					$this->Address = new Address;
					$user_addinfo = $this->Address->find('first',array('conditions'=>array('Address.user_id'=>$userId,'Address.primary_address'=>'1'),'fields'=>array('Address.id')));
					$this->data['Address']['id'] = $user_addinfo['Address']['id'];
					$this->data['Address']['primary_address'] = 1;
					$this->data['Address']['user_id'] = $userId;
					$this->data['Address']['add_firstname'] = ucwords(strtolower($this->data['User']['firstname']));
					
					$this->data['User']['id'] = $userId;
					$this->data['User']['firstname'] = ucwords(strtolower($this->data['User']['firstname']));
					$this->data['User'] = Sanitize::clean($this->data['User']);
					$this->data['Address'] = Sanitize::clean($this->data['Address']);
					$this->Address->set($this->data);
					$this->User->set($this->data);
					if($this->User->save($this->data)){
						$saved_flag = 1;
					} else{
						$saved_flag = 0;
					}
					if($this->Address->save($this->data)){
						$saved_flag = 1;
					} else {
						$saved_flag = 0;
					}
					if($saved_flag == 1){
						$this->Session->setFlash('Name has been updated successfully.');
						$user_detail = $this->User->find('first',array('conditions'=>array('User.id'=>$userId),'fields'=>array('User.firstname')));
						$this->data['User']['firstname'] = $user_detail['User']['firstname'];
						$this->render('name');
					} else {
						$this->Session->setFlash('Name has not been updated, please try again.','default',array('class'=>'flashError'));
						$this->render('update_name');
					}
				} else{
					foreach($this->data['User'] as $field_index => $user_info){
						$this->data['User'][$field_index] = html_entity_decode($user_info);
						$this->data['User'][$field_index] = str_replace('&#039;',"'",$value['User'][$field_index]);
					}
					$errorArray = $this->User->validationErrors;
					$this->set('errors',$errorArray);
				}
			} else{
				$user_detail = $this->User->find('first',array('conditions'=>array('User.id'=>$userId),'fields'=>array('User.firstname','User.lastname')));
				$this->data['User']['firstname'] = html_entity_decode($user_detail['User']['firstname']);
				if(!empty($user_detail)){
					$this->data['User']['firstname'] = html_entity_decode(str_replace('&#039;',"'",$user_detail['User']['firstname']));
				}

				$this->render('update_name');
			}
		} else{
			echo 'Please login again';
		}
	}

	/** 
	@function:		update_email
	@description		to update email address for registered customer
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		
	*/
	function update_email(){
		$this->layout = 'ajax';
		$userId = $this->Session->read('User.id');
		$this->viewPath = 'elements/useraccount' ;
		if(!empty($userId)){
			if(!empty($this->data)){
				$this->data = $this->cleardata($this->data);
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				$this->data['User']['id'] = $userId;
				$this->User->set($this->data);
				if($this->User->validates()){
					$this->data['User'] = Sanitize::clean($this->data['User']);
					$this->User->set($this->data);
					if($this->User->save($this->data)){
						$this->Session->setFlash('Email has been updated successfully.');
						$user_detail = $this->User->find('first',array('conditions'=>array('User.id'=>$userId),'fields'=>array('User.email')));
						$this->data['User']['email'] = $user_detail['User']['email'];
						$this->render('email');
					} else {
						$this->Session->setFlash('Email has not been updated, please try again.','default',array('class'=>'flashError'));
						$this->render('update_email');
					}
				} else{
					$errorArray = $this->User->validationErrors;
					$this->set('errors',$errorArray);
				}
			}else{
				$user_detail = $this->User->find('first',array('conditions'=>array('User.id'=>$userId),'fields'=>array('User.email')));
				if(!empty($user_detail)){
					$this->data['User']['email'] = $user_detail['User']['email'];
				}
				$this->render('update_email');
			}
		} else{
			echo 'Please login again';
		}
	}

	
	/** 
	@function:		update_password
	@description		to update password from front end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:		
	*/
	function update_password(){
		$this->layout = 'ajax';
		$userId = $this->Session->read('User.id');
		if ($this->RequestHandler->isMobile()) {
			$this->viewPath = 'users/mobile';
		}else{
			$this->viewPath = 'elements/useraccount';
		}
		
		if(!empty($userId)){
			if(!empty($this->data)){
				
				$this->data = $this->cleardata($this->data);
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				
				$this->User->set($this->data);
				if($this->User->validates()){
					$user_password = $this->User->find('first',array('conditions'=>array('User.id'=>$userId,'User.password'=>md5($this->data['User']['oldpassword']))));
					if(!empty($user_password)){
						$setdata['User']['password']= md5($this->data['User']['newpassword']);
						$setdata['User']['id'] = $userId;
						$this->data = $setdata;
					$this->data['User'] = Sanitize::clean($this->data['User']);
						$this->set($this->data);
						if($this->User->save($this->data)){
							$this->data = '';
							$this->Session->setFlash('Password has been updated successfully');
							if ($this->RequestHandler->isMobile()) {
								$this->render('update_password');
							}else{
								$this->render('change_password');
							}
						
						} else {
							$this->Session->setFlash('Password has not been updated, please try again.','default',array('class'=>'flashError'));
							$this->render('update_password');
						}
					} else{
						$this->Session->setFlash('Your old password does not match our record please try again.','default',array('class'=>'flashError'));
						$this->data['User']['oldpassword'] = "";
						$this->render('update_password');
					}
				} else{
					$errorArray = $this->User->validationErrors;
					$this->set('errors',$errorArray);
				}
			} else{
				$this->render('update_password');
			}
		} else{
			$this->Session->setFlash('Please login again.','default',array('class'=>'flashError'));
		}
	}


/** 
	@function:		update_profile
	@description		to update password from front end
	@Created by: 		Ramanpreet Pal
	@params		
	@Modify:		NULL
	@Created Date:
	ONLY for Mobile
	*/
	function update_profile(){
		$this->layout = 'ajax';
		$userId = $this->Session->read('User.id');
		$this->viewPath = 'users/mobile/';
		if(!empty($userId)){
			if(!empty($this->data)){
				$this->data['User']['id'] = $userId;
				$this->User->set($this->data);
				if($this->User->validates()){
					$newpassword=$this->data['User']['password'];
					$this->data['User']['password'] = md5($this->data['User']['password']);
					$this->data['User'] = Sanitize::clean($this->data['User']);
					$this->User->set($this->data);
					
					if($this->User->save($this->data)){
						$this->data['User']['password'] = $newpassword;
						$this->Session->setFlash('Your profile has been updated successfully.');
						/*$user_detail = $this->User->find('first',array('conditions'=>array('User.id'=>$userId),'fields'=>array('User.email')));
						$this->data['User']['email'] = $user_detail['User']['email'];
						$this->render('email');*/
					} else {
						$this->Session->setFlash('Your profile has not been updated, please try again.','default',array('class'=>'flashError'));
						$this->render('users/my_account');
					}
				} else{
					$errorArray = $this->User->validationErrors;
					$this->set('errors',$errorArray);
				}
			} else{
				$user_detail = $this->User->find('first',array('conditions'=>array('User.id'=>$userId),'fields'=>array('User.email','User.firstname')));
				
				if(!empty($user_detail)){
					$this->data['User']['email'] = $user_detail['User']['email'];
					$this->data['User']['firstname'] = $user_detail['User']['firstname'];
					//$this->data['User']['password'] = $user_detail['User']['password'];
				}
				$this->set('errors','');
				$this->render('users/my_account');
			}
		} else{
			echo 'Please login again';
		}
	}

/*
function get_server_load($windows = 0) {
        $os = strtolower(PHP_OS);
        if(strpos($os, "win") === false) {
		if(file_exists("/proc/loadavg")) {
			$load = file_get_contents("/proc/loadavg");
			$load = explode(' ', $load);
			return $load[0];
		}
		elseif(function_exists("shell_exec")) {
			$load = explode(' ', `uptime`);
			return $load[count($load)-1];
		}
		else {
			return "";
		}
	}
	elseif($windows) {
		if(class_exists("COM")) {
			$wmi = new COM("WinMgmts:\\\\.");
			$cpus = $wmi->InstancesOf("Win32_Processor");
			
			$cpuload = 0;
			$i = 0;
			while ($cpu = $cpus->Next()) {
				$cpuload += $cpu->LoadPercentage;
				$i++;
			}
			$cpuload = round($cpuload / $i, 2);
			return "$cpuload%";
		}
		else {
			return "";
		}
        }
}


echo get_server_load();

// @exec("KILL 21553");
@exec("KILL 22121");
@exec("ps auxwww", $processList);
echo "<pre>";
print_r($processList);
echo get_server_load();*/

}
?>
