<?php //echo $javascript->link(array('jquery-1.3.2.min', 'lib/prototype'), false); ?>
<?php //pr($this->data);?>
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
	var selectclassName = jQuery("#textclassSelectId").val();
	var textclassName = jQuery("#textclassNameId").val();
           var url = SITE_URL+'totalajax/DisplayStateBox/'+countryId+'/'+stateFieldName+'/'+selectedStateValue+'/'+selectclassName+'/'+textclassName;
	//	alert(url);
          //  jQuery('#plsLoaderID').show();
	    jQuery('#userStateTextSelect_div').html("<img src='/img/loading.gif'/>");
            jQuery.ajax({
                    cache:false,
                    async:false,
                    type: "GET",
                    url:url,
                    success: function(msg){
                            jQuery('#userStateTextSelect_div').html(msg);
                           // jQuery('#plsLoaderID').hide();
                    }
            });
	
}
</script>

<?php echo $form->create('User',array('action'=>'add_address','method'=>'POST','name'=>'frmAddress','id'=>'frmAddress'));?>
<h2 class="font14">My Address
		<?php $options=array(
			"url"=>"/users/add_address","before"=>"",
			"update"=>"addresschange",
			"indicator"=>"plsLoaderID",
			'loading'=>"Element.show('plsLoaderID')",
			"complete"=>"Element.hide('plsLoaderID')",
			"class" =>"blkgradbtn style='font-size:13px;'",
			"type"=>"Submit",
			"id"=>"myaddress",
			"div"=>"false",
		);?>
		<a href="javascript void(0);"><?php echo $ajax->submit('Change',$options);?></a>

<!--<a href="#" class="blkgradbtn">Change</a>--></h2>


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

	<li>
	<label>Full Name: </label>
	<p>
		<?php
			if(!empty($errors['add_name'])){
				$error_add_name='txtfld error_message_box';
			}else{
				$error_add_name='txtfld';
			}
		?>
		<?php echo $form->input('Address.add_name',array('class'=>$error_add_name,'maxlength'=>'100','label'=>false,'error'=>false,'div'=>false));?>
	</p>
</li>
<li>
	<label class="pad-tp">Address Line 1:</label>
	<p>(Company/House name number and street, PO Box)</p>
	<p class="margin-top">
		<?php
			if(!empty($errors['add_address1'])){
				$error_add_address1='txtfld error_message_box';
			}else{
				$error_add_address1='txtfld';
			}
		?>
	<?php echo $form->input('Address.add_address1',array('class'=>$error_add_address1,'maxlength'=>'100','label'=>false,'error'=>false,'div'=>false));?>
	
	
	<!--<input type="text" name="textfield2" value="Governance House" class="txtfld" />--></p>
</li>
<li>
	<label>Address Line 2:</label>
	<p>
		<?php
			if(!empty($errors['add_address2'])){
				$error_add_address2='txtfld error_message_box';
			}else{
				$error_add_address2='txtfld';
			}
		?>
		<?php echo $form->input('Address.add_address2',array('class'=>$error_add_address2,'maxlength'=>'100','label'=>false,'error'=>false,'div'=>false));?>
	</p>
</li>
<li>
	<label>Town/City:</label>
	<p>
		<?php
			if(!empty($errors['add_city'])){
				$error_add_city='txtfld error_message_box';
			}else{
				$error_add_city='txtfld';
			}
		?>
		<?php echo $form->input('Address.add_city',array('class'=>$error_add_city,'maxlength'=>'100','label'=>false,'error'=>false,'div'=>false));?>
	</p>
</li>
<li>
	<label>Country:</label>
	<p>
		<?php
			if(!empty($errors['country_id'])){
				$errorCountry='slct error_message_box';
			}else{
				$errorCountry='slct';
			}
		?>
	<?php echo $form->select('Address.country_id',$countries,null,array('onchange'=>'displayState();','type'=>'select','class'=>$errorCountry,'label'=>false,'div'=>false,'size'=>1),'-- Select --'); ?>
	</p>
</li>
<li>
	<label>State/County:</label>
	<p>
		<?php
			if(!empty($errors['add_state'])){
				$errorState='slct error_message_box';
			}else{
				$errorState='slct';
			}
			echo $form->hidden('stateclassSelect', array('value'=>$errorState,'id'=>'textclassSelectId'));
		?>
		<?php
			if(!empty($errors['add_state'])){
				$errorState='txtfld error_message_box';
			}else{
				$errorState='txtfld';
			}
			echo $form->hidden('stateclass', array('value'=>$errorState,'id'=>'textclassNameId'));
		?>
				
		<input type="hidden" name="userStateFieldName" id="userStateFieldName" value="Address.add_state">
		<?php
		if(isset($this->data['Address']['add_state'])){
			$this->data['Address']['add_state'] = $this->data['Address']['add_state'];
		}else{
			$this->data['Address']['add_state'] = '';
		}
		echo $form->hidden('Address.selected_state', array('value'=>$this->data['Address']['add_state'] ));?>
		<span id="userStateTextSelect_div">
		<?php echo $form->input('Address.add_state',array('class'=>$errorState,'label'=>false,'error'=>false,'div'=>false,'style'=>'padding-top:0px'));?>
		</span>
	<!--<select name="select" class="slct">
	<option>Middlesex</option>
	</select>-->
	</p>
</li>
<li>
	<label>Postcode:</label>
	<p>
		<?php
			if(!empty($errors['add_postcode'])){
				$error_postcode='txtfld error_message_box';
			}else{
				$error_postcode='txtfld';
			}
			echo $form->hidden('stateclass', array('value'=>$error_postcode,'id'=>'textclassNameId'));
		?>
		<?php echo $form->input('Address.add_postcode',array('class'=>$error_postcode,'maxlength'=>'100','label'=>false,'error'=>false,'div'=>false,));?>
		<?php echo $form->hidden('Address.id',array('size'=>'30','class'=>'form-textfield','maxlength'=>'30','label'=>false,'div'=>false,'style'=>'width : 100px'));?>
		<?php echo $form->hidden('Address.add_phone',array('size'=>'30','class'=>'form-textfield error-right','maxlength'=>'30','label'=>false,'div'=>false,'style'=>'width : 120px'));?>
	</p>
</li>
</ul>
<?php echo $form->end();?>
<script type="text/javascript" language="javascript">
var countryId = jQuery("#AddressCountryId").val();
if(countryId >0){
 displayState();
}
</script>