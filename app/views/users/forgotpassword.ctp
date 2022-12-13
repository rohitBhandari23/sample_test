<?php //echo $javascript->link(array('jquery-1.3.2.min', 'formvalidation'), false); ?>

<!-- Script for disable button once clicked -->
<script>
jQuery(document).ready(function()  {
	//disable submit button after one click
	jQuery('#clickOnce').click(function(){
	if(jQuery("#UserEmailaddress").val() != "")
		{
			jQuery("#clickOnce").attr("disabled", "true");
			jQuery('#frmUser').submit();
		}
	});
});
</script>

<!--mid Content Start-->
<div class="mid-content">
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
	<!-- <div class="breadcrumb-widget"><?php echo $html->link("Home",array("controller"=>"homes","action"=>"index"),array('escape'=>false));?> &gt; <?php echo $html->link("My Account",array("controller"=>"users","action"=>"my_account"),array('escape'=>false));?> &gt; <span>Forgot Password</span></div> -->
	<!--Make me an Offer &trade; Start-->
	<div class="side-content breadcrumb-widget">
		
		<h4 class="inner-gray-bg-head"><span>Forgot Password</span></h4>
		<!--Gray Fade Box Start-->
		<div class="gray-fade-bg-box padding white-bg-box">
			<!--Form Left Widget Start-->
			<div class="form-left-widget">
				<!--Form Widget Start-->
				<!--div class="errorlogin_msg" id="jsErrors">
					<?php //echo $this->element('errors'); ?>
				</div-->				
				<?php echo $form->create('User',array('action'=>'forgotpassword','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
				<div class="form-widget">
					<p>Please enter the email address that you registered with in the box below. Your password will be emailed to you.</p>
					<ul>
						<li>
							<label class="normal-width">Email:</label>
							<div class="form-field-widget">
							<?php 
							if(!empty($errors['emailaddress'])){
								$emailaddress='form-textfield error_message_box';
							}else{
								$emailaddress='form-textfield';
							}
							echo $form->input('User.emailaddress',array('size'=>'30','class'=>$emailaddress,'label'=>false,'div'=>false,'error'=>false));?>
								<div class="margin-top" id="cnclBtn">
								<?php echo $form->button('',array('type'=>'submit','class'=>'forgotpassword-sub','div'=>false,'id'=>'clickOnce'));?></div>
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
					<p>By Signing in you are agreeing to our <?php echo $html->link("Conditions of Use","/pages/view/conditions-of-use",array('escape'=>false));?> and <?php echo $html->link("Privacy Notice","/pages/view/privacy-policy",array('escape'=>false));?></p>
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
<?php
//echo $validation->rules(array('User'),array('formId'=>'frmUser'));
?>