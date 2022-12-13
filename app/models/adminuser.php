<?php
/**
* Adminuser Model class
*/
class Adminuser extends AppModel {

	var $name = 'Adminuser';
	var $assocs = array(
		'AdminuserPermission' => array(
		'type' => 'hasMany',
		'className' => 'AdminuserPermission',
		)
	);
	var $validate = array(
		'username' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => "Enter your username",
				'last' => true
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => "Username must be at least 6 characters long",
				'last' => true
			),
			'alphaNumeric' => array(
				'rule' => 'alphaNumeric',
				'message' => "Usernames must only contain letters and numbers",
				'last' => true
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => "Username already exists"
			)
		),
// 		'password' => array(
// 			'notEmpty' => array(
// 				'rule' => 'notEmpty',
// 				'message' => "Enter your password",
// 				'last' => true
// 			),
// 			'minLength' => array(
// 				'rule' => array('minLength', 6),
// 				'message' => "Password must be at least 6 characters long",
// 				'last'=>true
// 			),
// 		),
		'oldPassword' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter old password'
			),
			'ruleName2' => array(
				'rule' => array('isOldPasswordExists'),
				'message' => "Old password does not exists"
			)
		),
		'newpassword' => array(
			'NotEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter new password',
				'last'=>true
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => "Password must be at least 6 characters long",
				'last'=>true
			)
		),
		'confirmpassword' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter confirm password',
				'last' => true,
			),
			'checknewconfirmpassword' => array(
				'rule' => array('checknewconfirmpassword'),
				'message' => "Password and confirm password don't match",
				'last'=>true
			)
		),
		
		'password1' => array(
			'confirmpassword1' => array(
				'rule' => array('checkconfirmpassword'),
				'message' => "New password and confirm password don't match",
				'last'=>true
			)
		),
		'firstname' => array(
			'notEmpty' => array(
			'rule' => 'notEmpty',
			'message' => "Enter first name",
			'last' => true
			),
		),
		'lastname' => array(
			'rule' => 'notEmpty',
			'message' => "Enter last name"
		),
		'email' => array(
			'notEmpty' => array(
			'rule' => 'notEmpty',
			'message' => "Enter your email.",
			'last' => true
			),
			'ruleName2' => array(
			'rule' => array('email'),
			'message' => "Enter valid email address",
			'last' => true
			),
			'isUnique' => array(
			'rule' => 'isUnique',
			'message' => "Email address already exists"
			)
		),
		'status' => array(
			'rule' => array('checkstatus'),
// 			'message' => 'Select status for user.'
		)
	);
	function isOldPasswordExists($field = array()) {
		// Import Session Component
		App::import('Component', 'SessionComponent');
		$this->Session = new SessionComponent();
		App::import('Model', 'Adminuser');
		$this->Adminuser = new Adminuser();
		$userSession = $this->Session->read("SESSION_ADMIN");

		foreach( $field as $key => $value ){
			$v1 = md5(trim($value));
			$result = $this->Adminuser->find('first', array('conditions' => array('Adminuser.id' => $userSession['id'], 'password'=>$v1),'fields'=>array('Adminuser.id')));
			if(!is_array($result)){
				return false; 
			}
			return true;
		}
	}

	function checkconfirmpassword($field = null){
		if(!empty($field['password1'])){
			if($field['password1'] != $this->data['Adminuser']['confirmpassword1']){
				return false;
			}else{
				
				return true;
			}
		} else{
			return true;
		}
	}
	function checknewconfirmpassword($field = null){

		if(!empty($this->data['Adminuser']['password'])){
			if($field['confirmpassword'] != $this->data['Adminuser']['password']){
				return false;
			}else{
				return true;
			}
		} else if(!empty($this->data['Adminuser']['newpassword'])){
			if($field['confirmpassword'] != $this->data['Adminuser']['newpassword']){
				return false;
			}else{
				return true;
			}
		} else{
			return true;
		}
	}

	function checkstatus($field = Null){
		if(($field['status'] == '0') || ($field['status'] == '1')){
			return true;
		} else{
			return 'Select status for user';
		}
	}
}
?>