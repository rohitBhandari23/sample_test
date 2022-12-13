<?php echo $javascript->link(array('jquery-1.3.2.min','lib/prototype'), false); ?>

<script type="text/javascript" language="javascript">	
jQuery(document).ready(function(){
	setCookie('user_registration', 'yes');
});

function setCookie(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
}
</script>

<style type="text/css">
	.dimmer{
	position:absolute;
	left:45%;
	top:55%;
	}
</style>
<!--mid Content Start-->
<div class="mid-content">
	<!--- <?php //echo $this->element('useraccount/user_settings_breadcrumb');?> --->
	<!--Setting Tabs Widget Start-->
	<div class="row breadcrumb-widget">
		<?php echo $this->element('useraccount/tab');?>
		<!--Tabs Content Start-->
		<div class="tabs-content">
			<div class="form-widget" id="myAccount">
				<div id="plsLoaderID" style="display:none" class="dimmer"><?php echo $html->image("loading.gif" ,array('alt'=>"Loading" ));?></div>
				<ul>
					<li>If you want to change the name, e-mail address, or password associated with your Choiceful.com customer account, you may do so below. Please click the corresponding button to make changes.</li>
					<!--<li>
						<div>
						<?php 
							/*if ($session->check('Message.flash')){*/ ?>
								<div class="messageBlock" style="margin:5px 0px;"><?php //echo $session->flash();?></div>
								
							<?php //}
						?>
						</div>
					</li>-->
					<span id="name">
						<?php echo $this->element('useraccount/name');?>
					</span>
					<span id="email">
						<?php echo $this->element('useraccount/email');?>
					</span>
					<span id = "changepassword" >
						<?php echo $this->element('useraccount/change_password');?>
					</li>
				</ul>
			</div>
			<!--Form Widget Closed-->
		</div>                                     
		<!--Tabs Content Closed-->                
	</div>
	<!--Setting Tabs Widget Closed-->
</div>

<!--mid Content Closed-->
<?php
echo $validation->rules(array('User'),array('formId'=>'frmUser'));
?><!--<form accept-charset="utf-8" action="/users/save_account/1" id="frmUser2" name="frmUser2" method="post">-->
<script type="text/javascript">
function validate_password(){
	if(jQuery('#UserOldpassword').val() == ''){
		alert('Please enter old password.');
		return false;
	}
	if(jQuery('#UserNewpassword').val() == ''){
		alert('Please enter new password.');
		return false;
	}
	if(jQuery('#UserConfirmpassword').val() == ''){
		alert('Please enter confirm password.');
		return false;
	}
	if(jQuery('#UserConfirmpassword').val() != jQuery('#UserNewpassword').val()){
		alert('New password and confirm password don\'t match.');
		return false;
	}
}
</script>