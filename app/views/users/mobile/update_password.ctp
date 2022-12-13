
<?php echo $form->create('User',array('action'=>'update_password','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
<h2 class="font13">Change Password
		<?php $options=array(
			"url"=>"/users/update_password","before"=>"",
			"update"=>"changepassword",
			"indicator"=>"plsLoaderID",
			'loading'=>"showloading()",
			"complete"=>"hideloading()",
			"class" =>"blkgradbtn style='font-size:13px;'",
			"type"=>"Submit",
			"id"=>"myPassword",
		);?>
		<a href="javascript void(0);"><?php echo $ajax->submit('Change',$options);?></a>
</h2>


<ul class="change">
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
<?php if ($session->check('Message.flash')){ ?>
<li>
	<div>
		<div class="messageBlock" style="margin:5px 0px;"><?php echo $session->flash();?></div>
	</div>
</li>
<?php } ?>
			<?php 
				if(!empty($errors['oldpassword'])){
					$erroroldpassword ='txtfld error_message_box';
				}else{
					$erroroldpassword = 'txtfld';
				}
				if(!empty($errors['newpassword'])){
					$errornewpassword ='txtfld error_message_box';
				}else{
					$errornewpassword = 'txtfld';
				}
				if(!empty($errors['newchangeconfpassword'])){
					$errornewchangeconfpassword ='txtfld error_message_box';
				}else{
					$errornewchangeconfpassword ='txtfld';
				}
			?>


	<li>
		<label>Old Password: </label>
		<p>
			<?php echo $form->input('User.oldpassword',array('type'=>'password','class'=>$erroroldpassword,'label'=>false,'div'=>false,'error'=>false));?>
		</p>
	</li>
	<li>
		<label>New Password: </label>
		<p>
			<?php echo $form->input('User.newpassword',array('type'=>'password','class'=>$errornewpassword,'label'=>false,'div'=>false,'error'=>false));?>
		</p>
	</li>
	<li>
		<label>Confirm Password: </label>
		<p>
			<?php echo $form->input('User.newchangeconfpassword',array('type'=>'password','class'=>$errornewchangeconfpassword,'label'=>false,'div'=>false,'error'=>false));?>
		</p>
	</li>
	
	
</ul>
<?php echo $form->end();?>
