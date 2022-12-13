<?php echo $javascript->link('lib/prototype',true); ?>
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
<ul class="signinlist">
	<li><h4 class="red-color-text">Sign in to your Choiceful.com account</h4></li>
	<?php
	
	 if(!empty($errors)){?>
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
	<?php if ($session->check('Message.flash')){ ?>
			<li><div class="messageBlock">
				<?php echo $session->flash();?>
			</div></li>
	<?php } ?>
	<li class="margin-tp">
		<!--Left Form Widget Start-->
		<div class="left-form-widget">
			<ul>
				<li><label>Email address:</label></li>
				<li>
					<?php 
					if(($form->error('User.emailaddress'))){
				  		$errorClass='textfield small-width-input error_message_box';
					}else{
						$errorClass='textfield small-width-input';
					}
				echo $form->input('User.emailaddress',array('size'=>'30','label'=>false,'class'=>$errorClass,'div'=>false,'error'=>false)); ?>
				</li>
				<li><label>Password:</label></li>
				<li>
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
	</li>
	<li class="margin-top"><label>&nbsp;</label>
		<div class="left-form-widget" style="width:545px;">
		<ul>
		<li><label> </label>
		<!--<input type="button" value="Sign In" class="signinbtnwhyt" />-->
		<?php //echo $form->button('',array('type'=>'submit','class'=>'sign-in-btn','style'=>'float:left;margin-right:5px','div'=>false));?>
		
		<?php /*$options=array(
			"url"=>"/users/sign_in"+$updateDivId,"before"=>"",
			"update"=>$updateDivId,
			'frequency' => 0.2,
			"indicator"=>"plsLoaderID",
			'loading'=>"Element.show('plsLoaderID')",
			"complete"=>"Element.hide('plsLoaderID')",
			"class" =>"signinbtnwhyt",
			"type"=>"Submit",
			"id"=>"addreview",
			"div"=>"false",*/
		
			$options=array(
			"url"=>"/users/sign_in/tab4",
			"before"=>"",
			"update"=>'tab4',
			'frequency' => 0.2,
			"indicator"=>"plsLoaderID",
			'loading'=>"Element.show('plsLoaderID')",
			"complete"=>"Element.hide('plsLoaderID')",
			"class" =>"signinbtnwhyt",
			"type"=>"Submit",
			"id"=>"addreview",
			"div"=>"false",
		);?>
		<?php echo $ajax->submit('Sign In',$options);?>
		
		<?php //echo '<span style="line-height:24px">'.$form->error('User.emailaddress').'<span>';?>
		</li>
		</ul></div>
	</li>	
</ul>



<?php
echo $form->end();
?>