<?php ?>
<ul>
	<?php 
		if ($session->check('Message.flash')){ ?>
			<li><div><div class="messageBlock" style="margin:0px"><?php echo $session->flash();?></div></div></li>
	<?php } ?>
<!---->
		<?php
		if(!empty($errors)){
			$error_meaasge="Please add some information in the mandatory fields highlighted red below.";
		?>
			<li>
				<div class="error_msg_box"> 
					<?php echo $error_meaasge;?>
				</div>
			</li>
		<?php }?>
	<li>
		<label>Name:</label>
		<p>
			<?php
				if(!empty($errors['firstname'])){
					$erroroffer_name='txtfld error_message_box';
				}else{
					$erroroffer_name='txtfld';
				}
			?>
			<?php echo $form->input('User.firstname',array('class'=>$erroroffer_name,'maxlength'=>'30','label'=>false,'error'=>false,'div'=>false));?>
		</p>
	</li>
	<li>
		<label>Email Address:</label>
		<p>
			<?php
				if(!empty($errors['email'])){
					$erroroffer_email='txtfld error_message_box';
				}else{
					$erroroffer_email='txtfld';
				}
			?>
			<?php echo $form->input('User.email',array('size'=>'30','class'=>$erroroffer_email,'label'=>false,'error'=>false,'maxlength'=>'50','div'=>false));?>			
		</p>
	</li> 
	<li>
		<label>Password:</label>
		<p>
			<?php
				if(!empty($errors['password'])){
					$erroroffer_password='txtfld error_message_box';
				}else{
					$erroroffer_password='txtfld';
				}
			?>
			<?php echo $form->input('User.password',array('type'=>'password','class'=>$erroroffer_password,'label'=>false,'error'=>false,'div'=>false));?>
		</p>
	</li>
	</ul>
<!---->
	<?php echo $form->end();?>
