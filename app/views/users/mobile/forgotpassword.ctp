<?php echo $javascript->link(array('jquery-1.3.2.min', 'formvalidation'), false); ?>
<!--mid Content Start-->
 <!--Main Content Starts--->
             <section class="maincont nopadd">
                <section class="prdctboxdetal">
		  
                	<?php
				 if(!empty($errors)){
					 if(!empty($errors['emailaddress'])){
						 $error_meaasge="Please add some information in the mandatory fields highlighted red below.";
					 }else{
						 $error_meaasge=$errors[0];
					 }
				 ?>
				 <div class="error_msg_box"> 
					 <?php echo $error_meaasge;?>
				 </div>
			<?php }?>
			
                    <h4 class="orng-clr">Password Assistance</h4>
                    <p class="lgtgray applprdct">Enter the e-mail address associated with your Choiceful.com account, then click Continue. We'll email you a link to a page where you can easily reset your password. You'll be required to go to your email account to complete this process.</p>
                    <?php echo $form->create('User',array('action'=>'forgotpassword','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
                    <ul class="signinlist">
                       <li>What's your e-mail address?</li>
                       <li>
                       	<?php
				 if(!empty($errors['emailaddress'])){
					 $emailaddress='form-textfield error_message_box';
				 }else{
					 $emailaddress='form-textfield';
				 }
				    
			echo $form->input('User.emailaddress',array('label'=>false,'class'=>$emailaddress,'div'=>false,'error'=>false,'onBlur'=>'changeEmailField();','onClick'=>'changeEmailField();'));?>
                       </li>
                       <li class="margin-top">
                      <?php echo $form->button('Continue',array('type'=>'submit','class'=>'signinbtnwhyt cntnu','div'=>false));?>
                        <!--<input type="button" class="signinbtnwhyt cntnu" value="Continue">--></li>
                       <li class="toppadd">&nbsp;</li>
                    </ul>
                    <?php echo $form->end();?>
                </section>
             </section>
          <!--Main Content End--->
          
          <!--Navigation Starts-->
             <nav class="nav">
                      <ul class="maincategory yellowlist">
                         <?php echo $this->element('mobile/nav_footer');?>
                      </ul>
             </nav>
          <!--Navigation End--><!--mid Content Closed-->
<?php
echo $validation->rules(array('User'),array('formId'=>'frmUser'));
?>
<script>
function changeEmailField(){
	var emails = jQuery('#UserEmailaddress').val();
	if(emails == 'example:joe@example.com'){
		jQuery('#UserEmailaddress').val('');
	} else if(emails == ''){
		jQuery('#UserEmailaddress').val('example:joe@example.com');
	} else{

	}
}

var emails = jQuery('#UserEmailaddress').val();
if(emails == ''){
	jQuery('#UserEmailaddress').val('example:joe@example.com');
}

</script>
