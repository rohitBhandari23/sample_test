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
}
?>