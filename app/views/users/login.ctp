<?php ?>
<style type="text/css">
.error-message {
float:none !important;
}
.text {
padding-top:0px;
}
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

<!--mid Content Start-->
<div class="mid-content">
	
	<?php if(!empty($errors)){
		$error_meaasge="Please add some information in the mandatory fields highlighted red below.";
	?>
		<div class="error_msg_box"> 
			<?php echo $error_meaasge;?>
		</div>
	<?php }?>
	<?php
		if ($session->check('Message.flash')){ ?>
			<?php echo $session->flash();?>
	<?php 	} ?>
	
	<!---<div class="breadcrumb-widget"><?php echo $html->link("Home","/",array('escape'=>false));?> &gt; <?php echo $html->link("My Account",array("controller"=>"users","action"=>"my_account"),array('escape'=>false));?> &gt; <span>Sign in</span></div> -->

	<!--Make me an Offer &trade; Start-->
	<div class="side-content breadcrumb-widget">
		<h4 class="inner-gray-bg-head"><span>Sign In</span></h4>
		<!--Gray Fade Box Start-->
		<div class="gray-fade-bg-box padding white-bg-box">
			<!--Form Left Widget Start-->
			<div class="form-left-widget">
				<!--Form Widget Start-->
				<?php echo $form->create('User',array('action'=>'login/'.base64_encode($url),'method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
				<div class="form-widget">
					<ul>
						<li>
							<label>Please enter your e-mail address:</label>
							<div class="form-field-widget">
								<?php
								if(!empty($errors['emailaddress'])){
				  					$emailaddress='form-textfield error_message_box';
								}else{
									$emailaddress='form-textfield';
								}
								echo $form->input('User.emailaddress',array('size'=>'30','maxlength'=>'50','class'=>$emailaddress,'label'=>false,'error'=>false,'div'=>true));?>
								<p><?php echo $html->link("We are commited to your privacy",array("controller"=>"pages","action"=>"view",'security-and-data-protection-policy'),array('escape'=>false,"style"=>"color:#0C327E"));?></p>
							</div>
						</li>
						<li>
							<label>Do you have a password?</label>
							<div class="form-field-widget">
								<p>
								<?php
								//change by Smartdata as per client requirement
							//	$options=array('1'=>'<strong> No, I am a new customer<br></strong><span class="instructions-line line-break">(You\'ll create a password here)</span></p><p>','0'=>'<strong> No, I am a returning customer.</strong><br><span class="instructions-line line-break">and my password is:</span></p>');
									
								$options=array('1'=>'<strong> No, I am a new customer<br></strong><span class="instructions-line line-break" style="margin-bottom:18px;"></span></p><p>','0'=>'<strong> Yes, I have an account.</strong><br><span class="instructions-line line-break" style="margin-bottom:16px;">and my password is:</span></p>');
								$attributes=array('legend'=>false,'label'=>false,'onClick'=>'setFocustoPassword(this.value);');
								echo $form->radio('customer',$options,$attributes);
								?>
								<?php echo $form->input('User.password1',array('size'=>'30','class'=>'form-textfield','type'=>'password','label'=>false,'div'=>false));?>
								<p><?php echo $html->link("Forgot your password?",array("controller"=>"users","action"=>"forgot-password-assistant"),array('maxlength'=>'30','escape'=>false,"style"=>"color:#0C327E"));?></p>
							</div>
						</li>
						<li>
							<label>&nbsp;</label>
							<div class="form-field-widget">
								<input type="submit" name="button2" class="login-button" value=" " />
							</div>
						</li>
					</ul>
				</div>
				<!--Form Widget Closed-->
				<?php echo $form->end();?>
			</div>
			<!--Form Left Widget Start-->
			<!--Form Left Widget Start-->
			<div class="form-right-widget">
				<div class="secure-content">
					<p><strong>You are in a secure environment</strong>.</p>
					<p>By Signing in you are agreeing to our <?php echo $html->link("Conditions of Use",array("controller"=>"pages","action"=>"view",'conditions-of-use'),array('escape'=>false,'target'=>'_blank'));?> and <?php echo $html->link("Privacy Notice",array("controller"=>"pages","action"=>"view",'privacy-policy'),array('escape'=>false,'target'=>'_blank'));?></p>
				</div>
			</div>
			<!--Form Left Widget Start-->
			<div class="clear"></div>
		</div>
		<!--Gray Fade Box Closed-->
	</div>
	<!--Make me an Offer &trade; Closed-->
</div>
<!--mid Content Closed-->
<script>
function setFocustoPassword(value_field){
	if(value_field == 0){
		jQuery('#UserPassword1').focus();
	}
}
</script>