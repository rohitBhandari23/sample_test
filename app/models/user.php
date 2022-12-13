<?php
/**
* User Model class
*/
class User extends AppModel {
	var $name = 'User';
	var $assocs = array(
		'Seller' => array(
			'type' => 'hasOne',
			'className' => 'Seller',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'SellerSummary' => array(
			'type' => 'hasOne',
			'className' => 'Seller',
			'foreignKey' => 'user_id',
			'fields'=>array('id','free_delivery','threshold_order_value','business_display_name','bank_account_number','paypal_account_mail')
		),
		'Country' => array(
			'type' => 'belongsTo',
			'className' => 'Country',
			'foreignKey' => 'country_id',
		),
		'Event' => array(
			'type' => 'hasMany',
			'className' => 'Event',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Review' => array(
			'type' => 'hasMany',
			'className' => 'Review',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'CertificateReview' => array(
			'type' => 'hasMany',
			'className' => 'CertificateReview',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'CertificateSearctag' => array(
			'type' => 'hasMany',
			'className' => 'CertificateSearctag',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'UserDepartment' => array(
			'type' => 'hasMany',
			'className' => 'UserDepartment',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'Address' => array(
			'type' => 'hasMany',
			'className' => 'Address',
			'foreignKey' => 'user_id',
			'dependent' => true
		),
		'CertificateSearctag' => array(
			'type' => 'hasMany',
			'className' => 'CertificateSearctag',
			'foreignKey' => 'user_id',
			'dependent' => true
		),/*
		'OrderSellerOrders' => array(
			'type' => 'hasMany',
			'className' => 'OrderSeller',
			'foreignKey' => 'seller_id',
			'fields'=>array('count(id) as total_orders'),
			//'conditions' => array('OrderSeller.id = OrderSeller.seller_id')
		)*/
	);
	var $validate = array(
		'title' => array(
			'rule' => 'notEmpty',
			'message' => "Select a title"
		),
		'firstname' => array(
			'rule' => 'notEmpty',
			'message' => "Enter first name"
		),
		'lastname' => array(
			'rule' => 'notEmpty',
			'message' => "Enter last name"
		),
		'email' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => "Enter email address",
				'last' => true
			),
			'ruleName2' => array(
				'rule' => array('email'),
				'message' => "Enter valid email address"
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => "This email address already exists, please enter a different email address"
				//'on' => 'create',
			)
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => "Enter password.",
				'last' => true
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => "Password must be at least 6 characters long"
			)
		),
		'confirmpassword' => array(
			'checknewconfirmpassword' => array(
				'rule' => array('checknewconfirmpassword','password'),
				'message' => "Password and confirm password don't match",
				'last'=>true
			)
		),
		'oldpassword' => array(
			'NotEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter old password',
				'last'=>true
			),
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
		'newconfirmpassword' => array(
			'NotEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter new password',
				'last'=>true
			),
			'checknewconfirmpassword' => array(
				'rule' => array('checknewconfirmpassword','newpassword'),
				'message' => "Password and confirm password don't match",
				'last'=>true
			)
		),
		'newchangeconfpassword' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter confirm password.',
				'last' => true,
			),
			'newchangeconfpassword' => array(
				'rule' => array('checknewconfirmpassword','newpassword'),
				'message' => "Password and confirm password don't match",
				'last'=>true
			)
		),
		'address1' => array(
			'rule' => 'notEmpty',
			'message' => "Enter address line 1"
		),
		'city' => array(
			'rule' => 'notEmpty',
			'message' => "Enter city."
		),
		'state' => array(
			'rule' => 'notEmpty',
			'message' => "Enter state."
		),
		'postcode' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter postcode',
				'last' => true,
			),
			'vaild_postcode' => array(
				'rule' => array('vaild_postcode'),
				'message' => "Please enter vaild post code"
			),
		),
		'country_id' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Select country',
				'last' => true,
			),
			'vaild_country' => array(
				'rule' => array('vaild_country'),
				'message' => "Select country"
			),
		),
		'phone' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Enter phone number',
				'last' => true,
			),
			'vaild_ph' => array(
				'rule' => array('vaild_ph'),
				'message' => "Please enter vaild phone number"
			),
		),
		'terms_conditions' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'last' => true
			),
			'checked' => array(
				'rule' => array('terms_checked'),
				'message' => "Please confirm that you have read our terms and conditions"
			),
		),
		'emailaddress' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => "Enter your email",
				'last' => true
			),
			'ruleName2' => array(
				'rule' => array('email'),
				'message' => "Enter valid email address"
			),
		),
		'customer'=>array(
			'rule' => 'notEmpty',
			'message' => "Please select either you are a new customer or returning customer"
		)
	);

	function terms_checked($terms = null){
		if(!empty($terms['terms_conditions'])){
			return true;
		} else{
			return false;
		}
	}
	function checknewconfirmpassword($field = null,$second_field = null){
		
		if(!empty($this->data['User'][$second_field])){
			if(!empty($field)){
				foreach($field as $field_check){
					
					if(!empty($field_check)){
						if($field_check != $this->data['User'][$second_field]){
							return false;
						}else{
							return true;
						}
					} else{
						return 'Please enter both password and try it again';
					}
				}
			} else{
				return true;
			}
		} else{
			return true;
		}
	}

	function vaild_ph($field_phone = null){
		if(!empty($field_phone["phone"])){
			if(preg_match("/^[0-9 ]+$/", $field_phone["phone"]) === 0)
				return false;
			else 
				return true;
		}
	}
	function vaild_country($field_country = null){
		if(!empty($field_country["country_id"])){
			if($field_country["country_id"] == 7)
				return false;
			else 
				return true;
		}
	}

	function vaild_postcode($field_post = null){
		if(!empty($field_post["postcode"])){
			if(preg_match("/^[a-zA-Z0-9 ]+$/", $field_post["postcode"]) === 0)
				return false;
			else
				return true;
		}
	}
	
	//if(preg_match("/^[a-zA-Z0-9 _-.,:\"\']+$/", $_POST["address"]) === 0)
	// The above expression will allow a to z, A to Z, 0 to 9 space _ - . , : " '
}
?>