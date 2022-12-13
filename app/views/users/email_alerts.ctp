<?php ?>
<!--mid Content Start-->
<div class="mid-content">
	<!---<?php //echo $this->element('useraccount/user_settings_breadcrumb');?> --->
	
	<!--Setting Tabs Widget Start-->
	<div class="row breadcrumb-widget">
		<?php echo $this->element('useraccount/tab');?>
		<!--Tabs Content Start-->
		<div class="tabs-content">
			<?php echo $form->create('User',array('action'=>'email_alerts','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
			<div class="form-widget">
				<?php echo $form->create('User',array('action'=>'email_alerts','method'=>'POST','name'=>'frmEmaialerts','id'=>'frmEmaialerts'));?>
				<ul>
					<li>We want to stay in touch, but only in ways that you find helpful. Select from the options below and then click save when you are finished. Your changes will take immediate effect.</li>
					<li>Send me notifications for the following categories, you will still receive transactional email messages related to your activities on Choiceful.com including orders, listings, sales, reports and Make Me An Offer<sup style="font-size:8px;">TM</sup> notices.</li>
					<li>If you would like to change the types of email you receive from us you can make your changes below or you may unsubscribe completely.</li>
					
					<?php if ($session->check('Message.flash')){ ?>
					<li>
						<div>
							<div class="messageBlock"><?php echo $session->flash();?></div>
						</div>
					</li>
					<?php } ?>
					<?php if(!empty($departments)){
						foreach($departments as $department_id=>$department){
							if(!empty($user_departments)){
								foreach($user_departments as $uDept){
									
									if($uDept == $department_id){
										$checked = true;
										break;
									} else{
										$checked = false;
									}
								}
							}else{
								$checked = false;
							}
						?>
						<li>
							<?php echo $form->checkbox('email_alerts.'.$department_id,array('id'=>'select1','value'=>$department_id,"class"=>"checkbox","label"=>false,"div"=>false,'checked'=>$checked)); ?>
							<strong><?php echo $department;?></strong>
						</li>
						<?php }
					}?>
					<li>~or~</li>
					<?php if(empty($user_departments)){
						$dont_check = true;
					}else{
						$dont_check = false;
					}?>
					<li>
						<div class="form-field-widget" style="float:none"><?php echo $form->checkbox("User.donot_email",array("class"=>"checkbox","label"=>false,"div"=>false,'checked'=>$dont_check,'onClick'=>'return GetAction(this.checked,"this.form.data[pageListing][email_alerts]",this.form.select1)')); ?>
						<strong>Do not send me e-mail</strong></div>
						<span class="">Check this box to stop receiving all Choiceful.com communications(except transactional e-mails)</span>
					</li>
					<li>
						<div class="form-field-widget">
							<span class="yellow-btn-widget">
							<?php echo $form->button('Submit',array('type'=>'submit','class'=>'yellow-button','style'=>"",'div'=>false));?></span>
							<!--<span class=" yellow-btn-widget">
							<input type="submit" name="button2" class=" yellow-button" value="Edit" />
							</span-->
						</div>
					</li>
				</ul>
			</div>
			<!--Form Widget Closed-->
			<?php echo $form->end();?>
		</div>
		<!--Tabs Content Closed-->
	</div>
	<!--Setting Tabs Widget Closed-->
</div>
<!--- <div class="footer_line"></div> -->
<!--mid Content Closed-->
<?php
echo $validation->rules(array('User'),array('formId'=>'frmUser'));
?>
<script type="text/javascript">
function GetAction(val,field,id){
	
 	if(val==true)
 		uncheckAll(id);
 	else
 		checkAll(id);
}

function checkAll(field){
	if(field.value!=undefined){
		field.checked=true;
	}else{
		for (i = 0; i < field.length; i++)
			field[i].checked = true ;
	}
}

function uncheckAll(field){
	if(field.value!=undefined){
		field.checked=false
	}else{
		for (i = 0; i < field.length; i++)
			field[i].checked = false ;
	}
}

$(document).ready(function(){
	$(':checkbox').click(function(){
		var totalcount = $(":checkbox[id='select1']").length;
		var checkedcount = $(":checkbox[id='select1']:checked").length;
		if(checkedcount>0){
			$(":checkbox[id='UserDonotEmail']").attr('checked', false);
		}else{
			$(":checkbox[id='UserDonotEmail']").attr('checked', true);
		}
    });
});
</script>