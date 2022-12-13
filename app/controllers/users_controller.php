<?php
/**
	* Users Controller class
	* PHP versions 5.1.4
	* @date Oct 14, 2010
	* @Purpose:This controller handles all the functionalities regarding user management.
	* @filesource
	* @author	X
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
	var $permission_id = 2;
	
	/**
	* @Date: X
	* @Method : beforeFilter
	* Created By: X
	* @Purpose: This function is used to validate admin user permissions
	* @Param: none
	* @Return: none 
	**/
	function beforeFilter(){
		parent::beforeFilter();
		$this->detectMobileBrowser();
		$includeBeforeFilter = array('admin_export','admin_index', 'admin_add', 'admin_status','admin_view', 'admin_delete', 'admin_multiplAction', 'admin_user_changepassword' );
		if (in_array($this->params['action'],$includeBeforeFilter)){
			// validate admin session
			$this->checkSessionAdmin();
			
			// validate admin users for this module
			$this->validateAdminModule($this->permission_id);
		}
	}
	
	/**
     * @Date: X
     * @Method : beforeFilter
     * Created By: X
     * @Purpose: This function is used to validate admin user permissions
     * @Param: none
     * @Return: none 
     * */
	function registration($form_product = null,$from_giftcertificate = null){
		$logged_in_user = $this->Session->read('User');
		if(!empty($logged_in_user)){
			$this->Session->setFlash('You are already a registered member.');
			$this->redirect('/homes');
		}
		$this->set('title_for_layout','X Registration');
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


	//Only for mobile device because registraion form is divided into two part
	function registration2($form_product = null,$from_giftcertificate = null){
	$logged_in_user = $this->Session->read('User');
		if(!empty($logged_in_user)){
			$this->Session->setFlash('You are already a registered member.');
			$this->redirect('/homes');
		}
		$new_customer = $this->Session->read('newcustomer');
		if ($this->RequestHandler->isMobile()) {
           		 $this->layout = 'mobile/home';
        }else{
			$this->layout = 'front';
		}
		$titles = $this->Common->get_titles();
		$this->set('title',$titles);
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
					
					$this->Email->sendAs= 'html';
					$link=Configure::read('siteUrl');
					
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
							$this->redirect('/checkouts/step2/step2-X-checkout-gift-options');
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
	@function:		login
	@description		to login
	@Created by: 		X
	@params		
	@Modify:		NULL
	@Created Date:		X
	*/
	function login($url = null) {
		$subadomain="X";	
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
		$this->set('title_for_layout','X.com Signin');
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
							if($url == "pages/view/contact-us" || $url == "sellers/X-marketplace-sign-up"){
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
}
?>
