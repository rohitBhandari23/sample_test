<?php echo $javascript->link(array('jquery')); ?>
<script type="text/javascript">
jQuery(document).ready(function()  {
	var err_msg_alert = '<?php echo $errors ?>';
	if((err_msg_alert != '') && (jQuery("#fancybox-content",parent.document).height() == 160))
	{
		jQuery("#fancybox-content",parent.document).height(jQuery("#fancybox-content",parent.document).height()+40);
	}
});
</script>                                  

<?php ?>

<style>
.flashError {
background:none;
color:#E11C09!important;                                      
font-size:10px;
line-height:12px;
}
</style>
<?php  echo $form->create('User',array('action'=>'sign_in','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));
?>
<ul class="pop-con-list">
	<li><h4 class="red-color-text">Sign in to your Choiceful.com account</h4></li>
	<?php if(!empty($errors)){?>
	<li>
		<div class="error_msg_box"> 
			Please enter your email to sign-in.
		</div>
	</li>
	<?php }?>
	<?php if($session->check('Message.flash')){?>
	<li>
		<div class="error_msg_box"> 
			The email address or password is not correct, please check and try again.
		</div>
	</li>
	<?php }?>
	<li class="margin-tp">
		<!--Left Form Widget Start-->
		<div class="left-form-widget">
			<ul>
				<li><label>Email address:</label>
					<?php 
					if(($form->error('User.emailaddress'))){
				  		$errorClass='textfield small-width-input error_message_box';
					}else{
						$errorClass='textfield small-width-input';
					}
					echo $form->input('User.emailaddress',array('size'=>'30','maxlength'=>'50','label'=>false,'class'=>$errorClass,'div'=>false,'error'=>false)); ?>
				</li>
				<li><label>Password:</label>
					<?php 
					if(($form->error('User.emailaddress') || $session->check('Message.flash'))){
				  		$errorClass='textfield small-width-input error_message_box';
					}else{
						$errorClass='textfield small-width-input';
					}
					echo $form->input('User.password1',array('size'=>'30','label'=>false,'class'=>$errorClass,'type'=>'password','div'=>false)); ?>
				</li>
			</ul>
		</div>
		<!--Left Form Widget Closed-->
		<!--Right Content Widget Start-->
		<div class="right-alert-content">
			<ul class="alert-widget">
				<li class="alert-img margin-top"><?php echo $html->image('secure-icon.png',array('width'=>'24','height'=>'25','alt'=>"" ));?></li>
				<li class="alert-con">
				<p><strong>You are in a secure environment.</strong></p>
				<p>By signing in you are agreeing to our <?php echo $html->link('Conditions of Use','javascript:void(0)',array('escape'=>false,'onClick'=>'golink(\'/pages/view/conditions-of-use\');'));?> and <?php echo $html->link('Privacy Notice','javascript:void(0)',array('escape'=>false,'onClick'=>'golink(\'/pages/view/privacy-policy\');'));?></p>
				</li>
			</ul>
		</div>
	</li>
	<li class="margin-top"><label>&nbsp;</label>
		<div class="left-form-widget" style="width:545px;">
		<ul><li><label> </label><?php echo $form->button('',array('type'=>'submit','class'=>'sign-in-btn','style'=>'float:left;margin-right:5px','div'=>false));?>
		<?php //echo '<span style="line-height:24px">'.$form->error('User.emailaddress').'<span>';?><?php /*if ($session->check('Message.flash')){ ?>
			<div class="messageBlock">
				<?php echo $session->flash();?>
			</div>
		<?php } */?>
		</li>
		</ul></div>
	</li>
	<li>
		<!--Right Content Widget Closed-->
		<div class="clear"></div>
		<p class="forgot-password"><?php echo $html->link('Forgot your password?','javascript:void(0)',array('escape'=>false,'onClick'=>'golink(\'/users/forgotpassword\');'));?> <span>|</span> <?php echo $html->link('Create an account','javascript:void(0)',array('escape'=>false,'onClick'=>'golink(\'/users/registration\');'));?></p>
	</li>
</ul>



<?php
echo $form->end();
?>