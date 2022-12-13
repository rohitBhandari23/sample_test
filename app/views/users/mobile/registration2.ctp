<?php echo $javascript->link(array('jquery-1.3.2.min'), false);
?>
<!--mid Content Start-->
<script type="text/javascript" language="javascript">

//jQuery(document).ready(function(){
	username=getCookie('user_registration');
	if(username == 'yes'){
		history.go(+1);
	}
	
//});

function setCookie(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
}

function getCookie(c_name)
{
if (document.cookie.length>0)
  {
	 c_start=document.cookie.indexOf(c_name + "=");
	if (c_start!=-1)
	  {
	      c_start=c_start + c_name.length+1;
	      c_end=document.cookie.indexOf(";",c_start);
	      if (c_end==-1) c_end=document.cookie.length;
	      return unescape(document.cookie.substring(c_start,c_end));
	   }
  }
	return "";
}

	
// function to provide the state dropdown

function displayState(){
	//alert('Hello');
	var countryId = jQuery("#UserCountryId").val();
	var stateFieldName = jQuery("#userStateFieldName").val();
	var selectedStateValue = jQuery("#AddressSelectedState").val();
	var errorstate = jQuery("#UserErrorstate").val();
	if(countryId == ''){
		countryId = '0';
	}
        if(selectedStateValue == ''){
		selectedStateValue = '1';
	}
	var selectclassName = 'select';
	var textclassName = 'form-textfield';
	
            var url = SITE_URL+'totalajax/DisplayStateBox/'+countryId+'/'+stateFieldName+'/'+selectedStateValue+'/'+selectclassName+'/'+textclassName+'/'+errorstate;
           // alert(url);
            jQuery('#plsLoaderID').show();
            jQuery.ajax({
                    cache:false,
                    async:false,
                    type: "GET",
                    url:url,
                    success: function(msg){
                            jQuery('#userStateTextSelect_div').html(msg);
                            jQuery('#plsLoaderID').hide();
                    }
            });
	
}
</script>
<!--Main Content Start--->
<section class="maincont nopadd">
		<?php if(!empty($errors)){
			pr($errors);
		foreach($errors as $keys=>$errors){
			if($keys=='terms_conditions'){
				$error_meaasge="Please confirm that you have read and accept our terms and conditions";
			}else{
				$error_meaasge="Please add some information in the mandatory fields highlighted red below.";
			}
		}
		?>
		<div class="error_msg_box"> 
			<?php echo $error_meaasge;?>
		</div>
		<?php }?>
		<?php
		if ($session->check('Message.flash')){ ?>
			<div  class="messageBlock">
				<?php echo $session->flash();?>
			</div>
		<?php } ?>
		
	<section class="prdctboxdetal">
		<h4 class="orng-clr">Welcome to Express Registration</h4>
		<!---->
		<p class="lgtgray applprdct">Please enter the address to which your payment (debit/credit) card is registered to below. Your order could be delayed if this address does not match to that which is held by your banking authority.</p>
		<!---->
		<!--Form Widget Start-->
		<?php echo $form->create('User',array('action'=>'registration2/'.$form_product.'/'.$from_giftcertificate,'method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
		<ul class="signinlist xprsrgstrson">
			<?php 
			$sessUserData = $this->Session->read('sessUserData');
			echo $form->hidden('User.title',array('size'=>'30','value'=>$sessUserData['User']['title'],'label'=>false,'div'=>false,'error'=>false));
			echo $form->hidden('User.firstname',array('size'=>'30','value'=>$sessUserData['User']['firstname'],'label'=>false,'div'=>false,'error'=>false));
			echo $form->hidden('User.lastname',array('size'=>'30','value'=>$sessUserData['User']['lastname'],'label'=>false,'div'=>false,'error'=>false));
			echo $form->hidden('User.email',array('size'=>'30','value'=>$sessUserData['User']['email'],'label'=>false,'div'=>false,'error'=>false));
			echo $form->hidden('User.newpassword',array('size'=>'30','value'=>$sessUserData['User']['newpassword'],'label'=>false,'div'=>false,'error'=>false));
			echo $form->hidden('User.newconfirmpassword',array('size'=>'30','value'=>$sessUserData['User']['newpassword'],'label'=>false,'div'=>false,'error'=>false));
			?>
		<li><label>Address Line 1:</label>
			<div class="field">
			<p class="pad-rt12">
				<?php 
				if(($form->error('User.address1'))){
						$erroraddress1='form-textfield error-right error_message_box';
					}else{
						$erroraddress1='form-textfield error-right';
					}
				echo $form->input('User.address1',array('size'=>'30','class'=>$erroraddress1,'maxlength'=>'30','label'=>false,'div'=>false,'error'=>false));?>
			</p></div>
		</li>
		<li><label style="color:black;">Address Line 2:</label>
			<div class="field">
			<p class="pad-rt12">
			<?php 
				if(($form->error('User.address2'))){
						$erroraddress2='form-textfield error-right error_message_box';
					}else{
						$erroraddress2='form-textfield error-right';
					}
				echo $form->input('User.address2',array('size'=>'30','maxlength'=>'30','class'=>$erroraddress2,'label'=>false,'div'=>false,'error'=>false));?>
				<span class="opsonal">Optional</span>
			</p>
			</div>
		</li>
		<li><label>Town/City:</label>
			<div class="field">
			<p class="pad-rt12">
				<?php 
				if(($form->error('User.city'))){
				  $errorcity='form-textfield error-right error_message_box';
				}else{
				 $errorcity='form-textfield error-right';
				}
				echo $form->input('User.city',array('size'=>'30','maxlength'=>'30','class'=>$errorcity,'label'=>false,'div'=>false,'error'=>false));?>
			</p></div>
		</li>
		<li><label>Country :</label>
			<div class="field">
			<p class="pad-rt12">
			<?php 
				if(($form->error('User.country_id'))){
						$errorcountry_id='select error_message_box';
					}else{
						$errorcountry_id='select';
					}
				echo $form->select('User.country_id',$countries,null,array('onchange'=>'displayState();','type'=>'select','class'=>$errorcountry_id,'label'=>false,'div'=>false,'size'=>1),'Select');
				//echo $form->error('User.country_id'); ?>
			</p></div>
		</li>
		<li><label>State/County:</label>
			<div class="field">
			<p class="pad-rt12">
				<input type="hidden" name="userStateFieldName" id="userStateFieldName" value="User.state">
				<?php if(isset($this->data['User']['state'])){
				$this->data['User']['state'] = $this->data['User']['state'];
				}else{
				$this->data['User']['state'] = '';
				}
				echo $form->hidden('Address.selected_state', array('value'=>$this->data['User']['state'] ));?>
				
				<div class="form-field-widget" ><span id="userStateTextSelect_div">
				
				<?php
				if(($form->error('User.state'))){
							$errorstate='form-textfield error-right error_message_box';
						}else{
							$errorstate='form-textfield error-right';
						}
				echo $form->input('User.state',array('size'=>'30','maxlength'=>'30','class'=>$errorstate,'label'=>false,'div'=>false,'error'=>false,'style'=>'padding-top:0px; width:867px;'));//echo $form->error('User.state'); ?>
				</span><?php if(!empty($errors['state'])){
				//echo '<div class="error-message">'.$errors['state'].'</div>'; 
					echo $form->hidden('errorstate', array('value'=>$errors['state']));
				}else{
					echo $form->hidden('errorstate', array('value'=>''));
				}?>
			</p></div>
		</li>
		<li><label>Postcode :</label>
			<div class="field">
			<p class="pad-rt12">
			<?php 
				
			if(($form->error('User.postcode'))){
					$errorpostcode='form-textfield error-right error_message_box';
				}else{
					$errorpostcode='form-textfield error-right';
				}
			echo $form->input('User.postcode',array('size'=>'30','maxlength'=>'30','class'=>$errorpostcode,'label'=>false,'div'=>false,'error'=>false));?>
			</p></div>
		</li>
		
		
		
		<li><label>Phone Number :</label>
			<div class="field">
			<p class="pad-rt12">
			<?php 
			if(($form->error('User.phone'))){
				$errorphone='form-textfield error-right error_message_box';
			}else{
				$errorphone='form-textfield error-right';
			}
			echo $form->input('User.phone',array('size'=>'30','maxlength'=>'30','class'=>$errorphone,'label'=>false, 'onkeyup'=>'javascript: if( isNaN(this.value) ){ this.value = "" }','div'=>false, 'error'=>false));?>
			</p></div>
		</li>
		
		<li><label>&nbsp;</label>
			<div class="check">
				<?php echo $form->checkbox("User.terms_conditions",array("class"=>"checkbox error-right","label"=>false,"div"=>false,"error"=>false)); ?>
			</div>
                       <div class="agree">
                       	Please confirm that you have read our <?php echo $html->link("terms and conditions",array("controller"=>"pages","action"=>"view",'conditions-of-use'),array('escape'=>false,'class'=>"underline-link"));?>
                       	</div>
                </li>
		<li><label>&nbsp;</label>
			<?php echo $form->button('Continue',array('type'=>'submit','class'=>'signinbtnwhyt cntnu','div'=>false));?>
		</li>
		</ul>
		<!--Form Widget Closed-->
		<?php echo $form->end();?>
	</section>
</section>
	<!--Main Content End--->
	<!--Navigation Starts-->
	<nav class="nav toppadd">
		<ul class="maincategory yellowlist padding5">
		<?php echo $this->element('mobile/nav_footer')?>
		</ul>
		</nav>              
	<!--Navigation End-->
<script type="text/javascript" language="javascript">
var countryId = jQuery("#UserCountryId").val();
if(countryId >0){
 displayState();
}
</script>