<?php echo $javascript->link(array('jquery-1.3.2.min', 'lib/prototype'), false); ?>
<style type="text/css">
.my-account-form-widget .form-textfield {
width:75%;
}
</style>

<script language="JavaScript">	
// function to provide the state dropdown

function displayState(){
	//alert('Hello');
	var countryId = jQuery("#AddressCountryId").val();
	var stateFieldName = jQuery("#userStateFieldName").val();
	var selectedStateValue = jQuery("#AddressSelectedState").val();
	if(countryId == ''){
		countryId = '0';
	}
        if(selectedStateValue == ''){
		selectedStateValue = '1';
	}
	//var selectclassName = 'select';
	//var textclassName = 'form-textfield error-right';
	
	var selectclassName = jQuery('#errorselect').val();
	var textclassName = jQuery('#errortextfied').val();;
	
            var url = SITE_URL+'totalajax/DisplayStateBox/'+countryId+'/'+stateFieldName+'/'+selectedStateValue+'/'+selectclassName+'/'+textclassName;
	//	alert(url);
          //  jQuery('#plsLoaderID').show();
	    //jQuery('#userStateTextSelect_div').html("<img src='/img/loading.gif'/>");
		jQuery('#plsLoaderID').show();
		jQuery('#fancybox-overlay-header').show();
            jQuery.ajax({
                    cache:false,
                    async:false,
                    type: "GET",
                    url:url,
                    success: function(msg){
                            jQuery('#userStateTextSelect_div').html(msg);
                           // jQuery('#plsLoaderID').hide();
				jQuery('#plsLoaderID').hide();
				jQuery('#fancybox-overlay-header').hide();
                    }
            });
	
}
</script>

<!--mid Content Start-->
<div class="mid-content">
	<!-- <?php // echo $this->element('useraccount/user_settings_breadcrumb');?>-->
	<!--Setting Tabs Widget Start-->
	<div class="row breadcrumb-widget">
		<?php echo $this->element('useraccount/tab');?> 
		<!--Tabs Content Start-->
		<div class="tabs-content">
			<div class="form-widget" id="myAccount">
				<style type="text/css">
				.dimmer{
				position:absolute;
				left:45%;
				top:55%;
				}
				</style>
				<div id="plsLoaderID" style="display:none" class="dimmer"><?php echo $html->image("loading.gif" ,array('alt'=>"Loading" ));?></div>
				<?php echo $form->create('User',array('action'=>'add_address','method'=>'POST','name'=>'frmAddress','id'=>'frmAddress'));?>
				<ul>
					<li style="padding-bottom: 13px;">Further mailing labels will appear exactly as you enter them below. This change will not effect orders currently being processed.</li>
					<?php if ($session->check('Message.flash')){ ?>
					<li>
						<div>
							<div class="messageBlock" style="margin:5px 0px;"><?php echo $session->flash();?></div>
						</div>
					</li>
					<?php } ?>
					<?php
					if(!empty($errors)){
						$error_meaasge="Please add some information in the mandatory fields highlighted red below.";
					?>		
						<div class="error_msg_box"> 
							<?php echo $error_meaasge;?>
						</div>
					<?php }?>
					<li>
						<label style="width:110px">Full Name:</label>
						<div class="form-field-widget">
							<?php
							if(!empty($errors['add_name'])){
								$errorName='form-textfield error-right error_message_box';
							}else{
								$errorName='form-textfield error-right';
							}
							echo $form->input('Address.add_name',array('class'=>$errorName,'size'=>'100','maxlength'=>'100','label'=>false,'div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<label style="width:110px">Address Line1:</label>
						<div class="form-field-widget">
							<?php
							if(!empty($errors['add_address1'])){
								$errorAddAddress1='form-textfield error-right error_message_box';
							}else{
								$errorAddAddress1='form-textfield error-right';
							}
							echo $form->input('Address.add_address1',array('class'=>$errorAddAddress1,'size'=>'100','maxlength'=>'100','label'=>false,'div'=>false,'error'=>false));?>
							<span class="instructions-line line-break">Company name/House name number and Street, PO Box, C/O</span>
						</div>
					</li>
					<li>
						<label style="width:110px">Address Line2:</label>
						<div class="form-field-widget">
							<?php echo $form->input('Address.add_address2',array('class'=>'form-textfield','size'=>'100','maxlength'=>'100','label'=>false,'div'=>false));?>
							<span class="instructions-line line-break">Optional</span>
						</div>
					</li>
					<li>
						<label style="width:110px">Town/City:</label>
						<div class="form-field-widget">
							<?php
							if(!empty($errors['add_city'])){
								$errorAddCity='form-textfield error-right error_message_box';
							}else{
								$errorAddCity='form-textfield error-right';
							}
							echo $form->input('Address.add_city',array('class'=>$errorAddCity,'size'=>'100','maxlength'=>'100','label'=>false,'div'=>false,'error'=>false));?>
						</div>
					</li>
					<li>
						<label style="width:110px">Postcode:</label>
						<div class="form-field-widget">
							<?php
							if(!empty($errors['add_postcode'])){
								$errorAddPostcode='form-textfield error-right error_message_box';
							}else{
								$errorAddPostcode='form-textfield error-right';
							}
							echo $form->input('Address.add_postcode',array('class'=>$errorAddPostcode,'size'=>'100','maxlength'=>'100','label'=>false,'div'=>false,'error'=>false,'style'=>'width : 100px'));?>
							<?php echo $form->hidden('Address.id',array('size'=>'30','class'=>'form-textfield','maxlength'=>'30','label'=>false,'div'=>false,'style'=>'width : 100px'));?>
						</div>
					</li>
					<li>
						<label style="width:110px">Country</label>
						<div class="form-field-widget">
							<?php
							if(!empty($errors['country_id'])){
								$errorCountryId='select error-right error_message_box';
							}else{
								$errorCountryId='select error-right';
							}
							echo $form->select('Address.country_id',$countries,null,array('onchange'=>'displayState();','type'=>'select','class'=>$errorCountryId,'label'=>false,'div'=>false,'size'=>1),'-- Select --'); ?>
							<?php //echo $form->error('Address.country_id'); ?>
						</div>
					</li>
					<li>
						<label style="width:110px">State/county</label>
						<input type="hidden" name="userStateFieldName" id="userStateFieldName" value="Address.add_state">
						
						<?php if(!empty($errors['add_state'])){
								$errorstate='select error-right error_message_box';
							}else{
								$errorstate='select error-right';
							}
							echo $form->hidden('Address.select_error', array('value'=>$errorstate, 'id'=>'errorselect'));
						?>
						<?php if(!empty($errors['add_state'])){
								$errorstatetext='form-textfield error-right error_message_box';
							}else{
								$errorstatetext='form-textfield error-right';
							}
							echo $form->hidden('Address.text_error', array('value'=>$errorstatetext, 'id'=>'errortextfied'));
						?>
						
						<?php
						if(isset($this->data['Address']['add_state'])){
							$this->data['Address']['add_state'] = $this->data['Address']['add_state'];
						}else{
							$this->data['Address']['add_state'] = '';
						}
						echo $form->hidden('Address.selected_state', array('value'=>$this->data['Address']['add_state'] ));?>
						
						<div class="form-field-widget" >
							<span id="userStateTextSelect_div">
							<?php echo $form->input('Address.add_state',array('size'=>'30', 'maxlength'=>'100', 'class'=>$errorstatetext,'label'=>false,'error'=>false,'div'=>false,'style'=>'padding-top:0px'));?>
							</span>
							<?php  //echo $form->error('Address.add_state');?>
						</div>
					</li>
					
					<li>
						<label style="width:110px">Phone Number:</label>
						<div class="form-field-widget">
							<?php
							if(!empty($errors['add_phone'])){
								$errorAddPhone='form-textfield error-right error_message_box';
							}else{
								$errorAddPhone='form-textfield error-right';
							}
							echo $form->input('Address.add_phone',array('size'=>'30','class'=>$errorAddPhone,'maxlength'=>'30','label'=>false,'div'=>false,'error'=>false,'style'=>'width : 120px'));?>
						</div>
					</li>
					<li id="passwordDisplay">
						<label style="width:110px">&nbsp;</label>
						<div class="form-field-widget">
							<span class="gray-btn-widget">
							<?php echo $form->button('Save',array('type'=>'submit','class'=>'gray-button','div'=>false));?></span>
						</div>
					</li>
				</ul>
				<?php echo $form->end();?>
			</div>
			<!--Form Widget Closed-->
		</div><!--
		</div>-->
		<!--Tabs Content Closed-->
	</div>
	<!--Setting Tabs Widget Closed-->
</div>
<!-- <div class="footer_line"></div> -->
<!--mid Content Closed-->

<script type="text/javascript" language="javascript">
var countryId = jQuery("#AddressCountryId").val();
if(countryId >0){
 displayState();
}
</script>