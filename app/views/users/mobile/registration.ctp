<?php echo $javascript->link(array('jquery-1.3.2.min'), false);
?>
<style>
.xprsrgstrson label {
    width: 72px;
}
</style>
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
		
	<section class="prdctboxdetal">
		<?php if(!empty($errors)){
		foreach($errors as $keys=>$errors){
			if($keys=='terms_conditions'){
				$error_meaasge="Please confirm that you have read and accept out terms and conditions";
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
		
		
		<h4 class="orng-clr">Welcome to Express Registration</h4>
		<!---->
		<p class="lgtgray applprdct">Register on Choiceful.com Mobile and use your new account anywhere including your desktop PC.</p>
		<!---->
		<!--Form Widget Start-->
		<?php echo $form->create('User',array('action'=>'registration/'.$form_product.'/'.$from_giftcertificate,'method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
		<ul class="signinlist xprsrgstrson">
		<li><label>Title:</label>
			<div class="field">
			<p class="pad-rt12">
				<?php if(($form->error('User.title'))){
						$errorTitle='select error_message_box';
					}else{
						$errorTitle='select';
					}
				echo $form->select('User.title',$title,null,array('type'=>'select','class'=>$errorTitle,'label'=>false,'error'=>false,'div'=>false,'size'=>1),'Select');
				?>
			</p>
			</div></li>
		<li><label>First name:</label>
			<div class="field">
			<p class="pad-rt12">
				<?php if(($form->error('User.firstname'))){
				  	$errorfirstname='form-textfield error-right error_message_box';
					}else{
					$errorfirstname='form-textfield error-right';
					}
					echo $form->input('User.firstname',array('size'=>'30','class'=>$errorfirstname,'maxlength'=>'30','label'=>false,'div'=>false,'error'=>false));
				?>
			</p></div>
		</li>
		<li><label>Last name:</label>
			<div class="field">
			<p class="pad-rt12">
			<?php 
				if(($form->error('User.lastname'))){
						$errorlastname='form-textfield error-right error_message_box';
					}else{
						$errorlastname='form-textfield error-right';
					}
				
				echo $form->input('User.lastname',array('size'=>'30','class'=>$errorlastname,'label'=>false,'maxlength'=>'30','div'=>false,'error'=>false));?>
			</p>
			</div>
		</li>
		<li><label>Email:</label>
			<div class="field">
			<p class="pad-rt12">
				<?php 
					if(($form->error('User.email'))){
				  	$erroremail='form-textfield error-right error_message_box';
					}else{
					$erroremail='form-textfield error-right';
					}
					echo $form->input('User.email',array('size'=>'30','class'=>$erroremail,'label'=>false,'maxlength'=>'50','div'=>false,'error'=>false));
				?>
			</p></div>
		</li>
		<li><label>Password:</label>
			<div class="field">
			<p class="pad-rt12">
				<?php 
					if(($form->error('User.newpassword'))){
				  	$errornewpassword='form-textfield error-right error_message_box';
					}else{
					$errornewpassword='form-textfield error-right';
					}
					echo $form->input('User.newpassword',array('size'=>'30','class'=>$errornewpassword,'type'=>'password','label'=>false,'maxlength'=>'30','div'=>false,'error'=>false));?>
			</p></div>
		</li>
		<!---<li><label>Type it again :</label>
			<div class="field">
			<p class="pad-rt12">
			<?php /* 
			if(($form->error('User.newconfirmpassword'))){
					$errornewconfirmpassword='form-textfield error-right error_message_box';
				}else{
					$errornewconfirmpassword='form-textfield error-right';
				}
			echo $form->input('User.newconfirmpassword',array('size'=>'30','class'=>$errornewconfirmpassword,'type'=>'password','maxlength'=>'30','label'=>false,'div'=>false,'error'=>false)); */ ?>
			</p></div>
		</li>--->
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