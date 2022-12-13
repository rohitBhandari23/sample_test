<?php echo $javascript->link(array('jquery-1.3.2.min'), false);
?>
<!--mid Content Start-->
<script type="text/javascript" language="javascript">

//jQuery(document).ready(function(){
	username=getCookie('user_registration');
	if(username == 'yes'){
		history.go(+1);
	}
	                                         
//});

function setCookie(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
}

function getCookie(c_name)
{
if (document.cookie.length>0)
  {
	 c_start=document.cookie.indexOf(c_name + "=");
	if (c_start!=-1)
	  {
	      c_start=c_start + c_name.length+1;
	      c_end=document.cookie.indexOf(";",c_start);
	      if (c_end==-1) c_end=document.cookie.length;
	      return unescape(document.cookie.substring(c_start,c_end));
	   }
  }
	return "";
}

	
// function to provide the state dropdown

function displayState(){
	var countryId = jQuery("#UserCountryId").val();
	var stateFieldName = jQuery("#userStateFieldName").val();
	var selectedStateValue = jQuery("#AddressSelectedState").val();
	var errorstate = jQuery("#UserErrorstate").val();
	if(countryId == ''){
		countryId = '0';
	}
        if(selectedStateValue == ''){
		selectedStateValue = '1';
	}
	var selectclassName = 'select';
	var textclassName = 'form-textfield';
		
            var url = SITE_URL+'totalajax/DisplayStateBox/'+countryId+'/'+stateFieldName+'/'+selectedStateValue+'/'+selectclassName+'/'+textclassName+'/'+errorstate;
            //alert(url);
	    jQuery('#plsLoaderID').show();
	    jQuery('#fancybox-overlay-header').show();
	    
	jQuery.ajax({
		cache:false,
		async:false,
		type: "GET",
		url:url,
		success: function(msg){
			jQuery('#userStateTextSelect_div').html(msg);
			jQuery('#plsLoaderID').hide();
			jQuery('#fancybox-overlay-header').hide();
		}
	});
	
}
</script>
<div class="mid-content"><?php //pr($form); ?>

	<?php
	if(!empty($errors)){
		
		if(count($errors)==1 && !empty($errors['terms_conditions'])){
				$error_meaasge="Please confirm that you have read and accept out terms and conditions";
			}else{
				$error_meaasge="Please add some information in the mandatory fields highlighted red below.";
			}
	?>
		<div class="error_msg_box"> 
			<?php echo $error_meaasge;?>
		</div>
	<?php }?>
	
	<!---<div class="breadcrumb-widget"><?php echo $html->link("Home",'/',array('escape'=>false));?> &gt; <?php echo $html->link("Sign In",array("controller"=>"users","action"=>"login"),array('escape'=>false));?> &gt; <span>New Registration</span></div> --->
	<?php
	if ($session->check('Message.flash')){ ?>
		<div  class="messageBlock">
			<?php echo $session->flash();?>
		</div>
	<?php } ?>
	
	<!--Make me an Offer &trade; Start-->
	<div class="side-content breadcrumb-widget">
		<h4 class="inner-gray-bg-head"><span> Register</span></h4>
		<div class="gray-fade-bg-box padding white-bg-box">
			<!--<div class="errorlogin_msg" id="jsErrors">
				<?php //echo $this->element('errors'); ?>
			</div>-->
			<!--Form Widget Start-->
			<?php echo $form->create('User',array('action'=>'registration','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
			<div class="form-widget">
				<ul>
					<li>
						<label><span class="star">*</span> Title :</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.title'))){
				  					$errorTitle='select error_message_box';
								}else{
									$errorTitle='select';
								}
							echo $form->select('User.title',$title,null,array('type'=>'select','class'=>$errorTitle,'label'=>false,'error'=>false,'div'=>false,'size'=>1),'Select');
							//echo $form->error('User.title');?>
						</div>
					</li>
					<li>
						<label><span class="star">*</span> First Name:</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.firstname'))){
				  					$errorfirstname='form-textfield error-right error_message_box';
								}else{
									$errorfirstname='form-textfield error-right';
								}
								echo $form->input('User.firstname',array('size'=>'30','class'=>$errorfirstname,'maxlength'=>'30','label'=>false,'div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<label><span class="star">*</span> Last Name:</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.lastname'))){
				  					$errorlastname='form-textfield error-right error_message_box';
								}else{
									$errorlastname='form-textfield error-right';
								}
							
							echo $form->input('User.lastname',array('size'=>'30','class'=>$errorlastname,'label'=>false,'maxlength'=>'30','div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<label><span class="star">*</span> Email :</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.email'))){
				  					$erroremail='form-textfield error-right error_message_box';
								}else{
									$erroremail='form-textfield error-right';
								}
							echo $form->input('User.email',array('size'=>'30','class'=>$erroremail,'label'=>false,'maxlength'=>'50','div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<label><span class="star">*</span> Password :</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.newpassword'))){
				  					$errornewpassword='form-textfield error-right error_message_box';
								}else{
									$errornewpassword='form-textfield error-right';
								}
							echo $form->input('User.newpassword',array('size'=>'30','class'=>$errornewpassword,'type'=>'password','label'=>false,'maxlength'=>'30','div'=>false,'error'=>false));?>
						</div>
						<span style="color:#969696;" class="instructions-line line-break">Password should be at least 6 characters long. For enhanced security please add numbers and special characters (e.g. @).</span>
					</li>
					<li>
						<label><span class="star">*</span> Type it again :</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.newconfirmpassword'))){
				  					$errornewconfirmpassword='form-textfield error-right error_message_box';
								}else{
									$errornewconfirmpassword='form-textfield error-right';
								}
							echo $form->input('User.newconfirmpassword',array('size'=>'30','class'=>$errornewconfirmpassword,'type'=>'password','maxlength'=>'30','label'=>false,'div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<span style="color:#969696;" class="instructions-line">Please enter the address to which your payment (debit/credit) card is registered to  below. Your order could be delayed if this address does not match to that which is held by your banking authority.</span>
					</li>
					<li>
						<label><span class="star">*</span> Address Line 1:</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.address1'))){
				  					$erroraddress1='form-textfield error-right error_message_box';
								}else{
									$erroraddress1='form-textfield error-right';
								}
							echo $form->input('User.address1',array('size'=>'30','class'=>$erroraddress1,'maxlength'=>'30','label'=>false,'div'=>false,'error'=>false));?>
							<span style="color:#969696;" class="instructions-line line-break">Company name/House name number and Street, PO Box, C/O</span>
						</div>
					</li>
					<li>
						<label>Address Line 2:</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.address2'))){
				  					$erroraddress2='form-textfield error-right error_message_box';
								}else{
									$erroraddress2='form-textfield error-right';
								}
							echo $form->input('User.address2',array('size'=>'30','maxlength'=>'30','class'=>$erroraddress2,'label'=>false,'div'=>false,'error'=>false));?>
							<span style="color:#969696;" class="instructions-line line-break">Optional</span>
						</div>
					</li>
					<li>
						<label><span class="star">*</span> Town/City:</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.city'))){
				  					$errorcity='form-textfield error-right error_message_box';
								}else{
									$errorcity='form-textfield error-right';
								}
							echo $form->input('User.city',array('size'=>'30','maxlength'=>'30','class'=>$errorcity,'label'=>false,'div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<label><span class="star">*</span> Postcode:</label>
						<div class="form-field-widget">
							<?php 
							
							if(($form->error('User.postcode'))){
				  					$errorpostcode='form-textfield error-right error_message_box';
								}else{
									$errorpostcode='form-textfield error-right';
								}
							
							echo $form->input('User.postcode',array('size'=>'30','maxlength'=>'30','class'=>$errorpostcode,'label'=>false,'div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<label><span class="star">*</span> Country:</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.country_id'))){
				  					$errorcountry_id='select error_message_box';
								}else{
									$errorcountry_id='select';
								}
							echo $form->select('User.country_id',$countries,null,array('onchange'=>'displayState();','type'=>'select','class'=>$errorcountry_id,'label'=>false,'div'=>false,'size'=>1),'Select');
							//echo $form->error('User.country_id'); ?>
						</div>
					</li>
					<li>
						<label><span class="star">*</span>State/county:</label>
						<input type="hidden" name="userStateFieldName" id="userStateFieldName" value="User.state">
						<?php
						if(isset($this->data['User']['state'])){
						$this->data['User']['state'] = $this->data['User']['state'];
						}else{
						$this->data['User']['state'] = '';
						}
						echo $form->hidden('Address.selected_state', array('value'=>$this->data['User']['state'] ));?>
						
						<div class="form-field-widget" ><span id="userStateTextSelect_div">
						
						<?php 
						if(($form->error('User.state'))){
				  					$errorstate='form-textfield error-right error_message_box';
								}else{
									$errorstate='form-textfield error-right';
								}
						echo $form->input('User.state',array('size'=>'30','maxlength'=>'30','class'=>$errorstate,'label'=>false,'div'=>false,'error'=>false,'style'=>'padding-top:0px'));//echo $form->error('User.state'); ?>
						</span><?php if(!empty($errors['state'])){
						//echo '<div class="error-message">'.$errors['state'].'</div>'; 
						echo $form->hidden('errorstate', array('value'=>$errors['state']));
						}else{
						echo $form->hidden('errorstate', array('value'=>''));
						}?>
						</div>
						
					</li>
					<li>
						<label><span class="star">*</span> Phone Number:</label>
						<div class="form-field-widget">
							<?php 
							if(($form->error('User.phone'))){
				  					$errorphone='form-textfield error-right error_message_box';
								}else{
									$errorphone='form-textfield error-right';
								}
							echo $form->input('User.phone',array('size'=>'30','maxlength'=>'30','class'=>$errorphone,'label'=>false,'div'=>false, 'error'=>false));?>
						</div>
					</li>
					<li>
						<span class="instructions-line gray line-break">
							Choiceful.com respects your privacy and takes comprehensive measures to safeguard your personal and business information. We will not share your credentials and business data with any 3rd parties under any circumstances. By clicking on the Continue button you agree to our Privacy Policy. Please note you can amend your account details at any time by logging in to your Choiceful.com account. Please take a few moments to review our
							<?php echo $html->link("privacy policy",array("controller"=>"pages","action"=>"view",'privacy-policy'),array('escape'=>false,'class'=>"underline-link"));?>.
						</span>
					</li>
					<!--li>
						<span class="instructions-line gray line-break">
							As a Choiceful.com customer we will send you emails such as our e-newsletter, as well as catalogues and promotions through the post notifying you of relevent offers. If you do not wish to recieve such communictions, please use the  edit communication options in the My Account section. You can amend your preferences at any time by logging in to your Choiceful.com account. Please take a few moments to review the addintional preferences below and amends as necessary.
						</span>
					</li>
					<li>
						<?php //echo $form->checkbox("User.contact_phone",array("class"=>"checkbox error-right","label"=>false,"div"=>false)); ?>
						<strong>Contact by telephone</strong>
						<span class="instructions-line gray line-break">Ensure this box remains ticket if you'd like to be notified of revevant offers over the phone by one of our sales team.</span>
					</li>
					<li>
						<?php //echo $form->checkbox("User.contact_partner",array("class"=>"checkbox error-right","label"=>false,"div"=>false)); ?>
						<strong>Contact by partners</strong>
						<span class="instructions-line gray line-break">From time to time we would like to send you some information from carefully selected partners about discounts, products and services, which we think you'll be interested in. Ensure this box remains ticked if you'd like to receive these.</span>
					</li-->
					<li>
						<?php echo $form->checkbox("User.terms_conditions",array("class"=>"checkbox error-right","label"=>false,"div"=>false,"error"=>false)); ?>
						<strong>Please confirm that you have read our <?php echo $html->link("terms &amp; conditions",array("controller"=>"pages","action"=>"view",'conditions-of-use'),array('escape'=>false,'class'=>"underline-link"));
						//echo $form->error('User.terms_conditions'); ?></strong>
					</li>
					<li>
						<?php echo $form->button('',array('type'=>'submit','class'=>'yellow-continue','div'=>false, 'style'=>'cursor:pointer'));?>
					</li>
				</ul>
			</div>
			<!--Form Widget Closed-->
			<?php echo $form->end();?>
		</div>
	</div>
	<!--Make me an Offer &trade; Closed-->
</div>
<!--mid Content Closed-->
<script type="text/javascript" language="javascript">
var countryId = jQuery("#UserCountryId").val();
if(countryId >0){
 displayState();
  
}
</script>