<?php
echo $javascript->link(array('jquery-1.3.2.min'), false);
echo $html->css('dhtmlgoodies_calendar.css');
echo $javascript->link('dhtmlgoodies_calendar.js');
?>
<script type="text/javascript" language="javascript">	
jQuery(document).ready(function(){
	 displayState();
	 HideUnhideSuspendBox();
	
});

jQuery(document).change( function(){
	 HideUnhideSuspendBox();
	});

// function to show a or hide the suspend display box
function HideUnhideSuspendBox(){
	var suspendStatus = jQuery("#UserSuspend").val();
	if(suspendStatus == '1' ){
		jQuery('#suspendCalenderRow_id').show();
	}else{
		jQuery('#suspendCalenderRow_id').hide();
	}
	
}


// function to provide the state dropdown
function displayState(){
	var countryId = jQuery("#countrySelect").val();
	var stateFieldName = jQuery("#stateFieldName").val();
	var selectedStateValue = jQuery("#AddressSelectedState").val();
	if(countryId == ''){
		countryId = '0'; // 0 for test value
	}
	if(selectedStateValue == ''){
		selectedStateValue = '1'; // 1 for test value
	}
	var selectclassName = 'textbox-m';
	var textclassName = 'textbox-m';
	
		var url = SITE_URL+'totalajax/DisplayStateBox/'+countryId+'/'+stateFieldName+'/'+selectedStateValue+'/'+selectclassName+'/'+textclassName;
		jQuery('#preloader').show();
		jQuery.ajax({
			cache:false,
			async:false,
			type: "GET",
			url:url,
			success: function(msg){
				jQuery('#stateTextSelect').html(msg);
				jQuery('#preloader').hide();
			}
		});
	//}
}
</script>

<?php echo $form->create('User',array('action'=>'add','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" valign="top">
	<tr>
		<td valign="top">
			<table align="center" width="98%" border="0" cellpadding="0" cellspacing="0">
				<tr class="adminBoxHeading reportListingHeadsfdsfsaing">
					<td class="adminGridHeading heading"><?php echo $listTitle; ?></td>
					<td class="adminGridHeading heading"  height="25px" align="right">
						
					</td>
				</tr>
				
				<tr>
					<td colspan="2">
						<table class="adminBox" border="0" cellpadding="2" cellspacing="2" width="100%">
							<tr height="2">
								<td width="" align="right"></td>
								<td width="" align="left"></td>
							</tr>
							<tr height="20px">
								<td class="error_msg" colspan="4" align="left">Fields marked with an asterisk (*) are required.</td>
							</tr>

							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Title :</td>
								<td>
									<?php echo $form->select('User.title', $title, $this->data['User']['title'], array('type'=>'select','class'=>'textbox-m','label'=>false,'div'=>false,'size'=>1), 'Select Title..');
									echo $form->error('User.title'); ?>
								</td>
							</tr>
							
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> First name :</td>
								<td>
									<?php echo $form->input('User.firstname',array('class'=>'textbox-m','label'=>false,'div'=>false));?>
								</td>
								
							</tr>
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Last name :</td>
								<td>
									<?php echo $form->input('User.lastname',array('class'=>'textbox-m','label'=>false,'div'=>false));?>
								</td>
							</tr>
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Email address :</td>
								<td>
									<?php echo $form->input('User.email',array('size'=>'30','maxlength'=>'60','class'=>'textbox-m','label'=>false,'div'=>false));?>
									<?php echo $form->hidden('User.id',array('size'=>'30','maxlength'=>'30','class'=>'textbox-m','label'=>false,'div'=>false));?>
								</td>
								
							</tr><?php if(empty($this->data['User']['id'])) { ?>
							<tr>
								<td align="right"><span class="error_msg">*</span> Password :</td>
								<td>
									<?php echo $form->input('User.password',array('class'=>'textbox-m','type'=>'password','label'=>false,'value'=>'','div'=>false));?>
								</td>
							</tr>
							<tr>
								<td align="right"><span class="error_msg">*</span> Try it again :</td>
								<td>
									<?php echo $form->input('User.confirmpassword',array('size'=>'30','maxlength'=>'40','class'=>'textbox-m','type'=>'password','label'=>false,'value'=>'','div'=>false)); ?>
								</td>
							</tr>
							<?php }?>
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Address Line 1 :</td>
								<td>
<?php echo $form->input('Address.add_address1',array('class'=>'textbox-m','label'=>false,'div'=>false));?>
<!--<br/><div style="padding-top:3px;position:absolute;width:100%;">Company name/House name number and Street, PO Box, C/O</div><br/>-->
								</td>
								
							</tr>
							<tr>
								<td align="right" valign="top">Address Line 2 :</td>
								<td>
<?php echo $form->input('Address.add_address2',array('class'=>'textbox-m','label'=>false,'div'=>false));?>
<!--<br/><div style="padding-top:3px;position:absolute;width:100%;">Optional</div><br/>-->
								</td>
								
							</tr>
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Town/City :</td>
								<td>
<?php echo $form->input('Address.add_city',array('class'=>'textbox-m','label'=>false,'div'=>false));?>
								</td>
								
							</tr>
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Postcode :</td>
								<td>
<?php echo $form->input('Address.add_postcode',array('class'=>'textbox-m','label'=>false,'div'=>false));?>
								</td>
								
							</tr>
							
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Country :</td>
								<td>
<?php echo $form->select('Address.country_id',$common->getcountries(),null,array('id'=>'countrySelect','onchange'=>'displayState()', 'escape'=>false, 'type'=>'select','class'=>'textbox-m','label'=>false,'div'=>false,'size'=>1) ,'Select Country..' ); 
								echo $form->error('Address.country_id'); ?>
								</td>
								
							</tr>
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> State :</td>
								<td >
								 <input type="hidden" name="stateFieldName" id="stateFieldName" value="Address.add_state">
								<div id="stateTextSelect">
<?php echo $form->input('Address.add_state',array('class'=>'textbox-m','label'=>false,'div'=>false));
								if(!empty($errors['add_state']))
									echo '<div class="error-message">'.$errors['add_state'].'</div>'; 
								?>
								</div>
								  <?php //echo $form->error('Address.add_state'); ?>
								</td>
								
							</tr>
							<tr>
								<td align="right" valign="top"><span class="error_msg">*</span> Phone Number :</td>
								<td>
<?php echo $form->input('Address.add_phone',array('class'=>'textbox-m','label'=>false,'div'=>false,'maxlength'=>20));?>
								</td>
							</tr>
							<tr>
								<td align="right" valign="top">Contact By Telephone :</td>
								<td>
<?php echo $form->checkbox('User.contact_by_phone',array("class"=>"textbox-m","label"=>false,"div"=>false)); ?>
								</td>
							</tr>
							<tr>
								<td align="right" valign="top">Contact By Partners :</td>
								<td>
<?php echo $form->checkbox('User.contact_by_partner',array("class"=>"textbox-m","label"=>false,"div"=>false)); ?>
								</td>
							</tr>
							
							<tr>
								<td align="right" valign="top">Status :</td>
								<td>
<?php echo $form->select('User.status', array('1'=>'Active','0'=>'Inactive'), null, array('type'=>'select','class'=>'textbox-s','label'=>false,'div'=>false,'size'=>1), 'Select Status'); 
									echo $form->error('User.status'); ?>
								</td>
							</tr>
							<tr>
								<td align="right" valign="top">Suspend  :</td>
								<td>
<?php echo $form->select('User.suspend', array('1'=>'Yes','0'=>'No'), null, array('type'=>'select','class'=>'textbox-s','label'=>false,'div'=>false,'size'=>1), 'Select '); 
									echo $form->error('User.suspend'); ?>
								</td>
							</tr>
							<tr id="suspendCalenderRow_id" style="display:none;">
								<td align="right" valign="top">Suspended Till :</td>
								<td>
<?php
	 echo $form->input('User.suspend_date',array('autocomplete'=>'off','type'=>'text','size'=>'15','label'=>false,'div'=>false,'class'=>'textbox-s','readonly'=>'readonly')); 									
	 echo $html->image('calendar/cal.gif',array('border'=>'0','alt'=>'','onClick'=>"displayCalendar(document.forms[0].UserSuspendDate,'dd/mm/yyyy',this)"));
	 ?>
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td>&nbsp;</td>
								<td align="left">
								<?php if(!empty($this->data['User']['id'])) {
									echo $form->button('Update',array('type'=>'submit','class'=>'btn_53','div'=>false));
								} else {
									echo $form->button('Add',array('type'=>'submit','class'=>'btn_53','div'=>false));
								}?>
								<?php echo $form->button('Cancel',array('type'=>'button','class'=>'btn_53','onClick'=>"goBack('/admin/users')"));?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php 
echo $form->input('User.id');
echo $form->input('Address.id');
echo $form->hidden('Address.user_id');
echo $form->hidden('Address.selected_state', array('value'=>$this->data['Address']['add_state']));
echo $form->hidden('Address.primary_address');

echo $form->end();
// echo $validation->rules(array('User'),array('formId'=>'frmUser'));
?>
<!--<script>
alert(jQuery("input[name='data[User][firstname]']").val() );
jQuery("input[name='data[User][firstname]']").val('raman');
</script>-->