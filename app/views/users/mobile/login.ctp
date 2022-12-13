<?php ?>
<style type="text/css">
.error-message {
float:none !important;
}
.text {
padding-top:0px;
}
.message{ margin-left: -8px; }
</style>
<?php echo $javascript->link(array('jquery-1.3.2.min'), false); ?>
<script type="text/javascript" language="javascript">	
jQuery(document).ready(function(){
	setCookie('user_registration', '');
});

function setCookie(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
}
</script>
<!--Main Content Starts--->
             <section class="maincont nopadd">
                <section class="prdctboxdetal">
		<h4 class="orng-clr">Sign in</h4>
                    <!---->
		<?php if(!empty($errors)){
			$error_meaasge = "Please add some information in the mandatory fields highlighted red below.";?>
				<div class="error_msg_box"> 
					<?php echo $error_meaasge;?>
				</div>
					
			<?php }?>
		<?php
			if ($session->check('Message.flash')){ ?>
				<?php echo $session->flash();?>
		<?php 	} ?>
			
                    <p class="lgtgray applprdct">Please sign in using your e-mail address and existing Choiceful password, then click on the Sign In button </p>
                    <!---->
                    <?php echo $form->create('User',array('action'=>'login/'.base64_encode($url),'method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
                    <ul class="signinlist">
                       <li>What's your e-mail address?</li>
                       <li>
			<?php
				if(!empty($errors['emailaddress'])){
					$emailaddress='error_message_box';
				}else{
					$emailaddress='';
				}
				echo $form->input('User.emailaddress',array('maxlength'=>'50','label'=>false, 'class'=>$emailaddress,'div'=>false,'error'=>false,'onBlur'=>'changeEmailField();','onClick'=>'changeEmailField();'));
			?>
                       </li>
                       <li>Do you have a Choiceful.com password?</li>
                       <li class="margin-top">
                       <?php
				$options=array('1'=>'<label> No, I am a new customer</label>','0'=>'<label> Yes, I have a password</label>');
				$attributes=array('separator'=>'<li class="margin-top">','legend'=>false,'label'=>false,'onClick'=>'setFocustoPassword(this.value);');
				echo $form->radio('customer',$options,$attributes);
			?>
			</li>
                       
                       <li class="margin-top">
                       <?php echo $form->input('User.password1',array('maxlength'=>'30' , 'type'=>'password', 'style' => 'width:73%; padding:6px 5px; border:1px solid #cecece; margin:6px 0;' , 'onClick'=>'setFocustoYes()','label'=>false,'div'=>false));?>
                       </li>
                       <li class="margin-top"><input type="submit" value="Sign In" class="signinbtnwhyt" /></li>
                       <li class="applprdct toppadd">
                       		<?php echo $html->link("Forgotten your password?",array("controller"=>"users","action"=>"forgot-password-assistant"),array('maxlength'=>'30','escape'=>false));?>
                       </li>
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
          <!--Navigation End-->
		
<script>
function setFocustoPassword(value_field){
	if(value_field == 0){
		jQuery('#UserPassword1').focus();
	}
}
function setFocustoYes(value_field){
		jQuery('#UserCustomer0').attr("checked", "checked");
}


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