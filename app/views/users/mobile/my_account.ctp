<?php echo $javascript->link(array('lib/prototype'), true); ?>
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
 <!--Tabs Start-->
<?php echo $this->element('mobile/orders/tab');?>
<!--Tbs Closed-->
<!--Tbs Cnt start-->
<section class="tab-content padding0">
<!--Account Settings Start-->
<section class="offers">                	
	<section class="gr_grd">
		<h4 class="orng-clr">Account Settings</h4>
	</section>
	<!--Row1 Start-->
	
	<div id="plsLoaderID" style="display:none" class="dimmer"><?php echo $html->image("loading.gif" ,array('alt'=>"Loading" ));?></div>
	
	<div class="row-full">
	<div class="settings">
		<?php 
		if ($session->check('Message.flash')){ ?>
			<div><div class="messageBlock" style="margin:0px"><?php echo $session->flash();?></div></div>
		<?php } ?>
		
		<?php echo $form->create('User',array('action'=>'update_profile','method'=>'POST','name'=>'frmUser','id'=>'frmUser'));?>
		<h2 class="font13">Personal Details 
		<?php $options=array(
			"url"=>"/users/update_profile","before"=>"",
			"update"=>"updateprofile",
			"indicator"=>"plsLoaderID",
			'loading'=>"Element.show('plsLoaderID')",
			"complete"=>"Element.hide('plsLoaderID')",
			"class" =>"blkgradbtn style='font-size:13px;'",
			"type"=>"Submit",
			"id"=>"updateprofile",
			"div"=>"false",
		);?>
		<a href="javascript void(0);"><?php echo $ajax->submit('Change',$options);?></a>
			
		<!--<a href="#" class="blkgradbtn">Change</a>--></h2>
		<ul id = "updateprofile" class="change">
		<li>
			<label>Name:</label>
			<p>
				<?php echo $form->input('User.firstname',array('class'=>'txtfld','maxlength'=>'30','label'=>false,'div'=>false));?>
			</p>
		</li>
		<li>
			<label>Email Address:</label>
			<p>
				<?php echo $form->input('User.email',array('size'=>'30','class'=>'txtfld','label'=>false,'maxlength'=>'50','div'=>false));?>
			</p>
		</li> 
		</ul>
		
	</div>
	<!---->
	<?php echo $form->end();?>
	<div class="settings">
	  <span id="changepassword"><?php //echo $this->element('useraccount/change_password');?></span>
	</div>
	
	<div class="settings">
	<div id="addresschange"></div>
	
	</div>
	</div>
	<!--Row1 Closed-->
</section>
<!--Account Settings Closed-->
</section>

<!--Tbs Cnt closed-->
<script type="text/javascript">
var SITE_URL = "<?php echo SITE_URL; ?>";
var address_id = "<?php echo $addresses_id; ?>";

 // function to like to dislike in giftcertificate
function update_address(){

	var postUrl = SITE_URL+'users/add_address/'+address_id;
	//alert(postUrl);
	jQuery('#plsLoaderID').show();
	jQuery.ajax({
		cache:false,
		async: false,
		type: "GET",
		url: postUrl,
		success: function(msg){
		/** Update the div**/
		jQuery('#addresschange').html(msg);
		jQuery('#plsLoaderID').hide();
	}
	});
}

function update_password(){

	var postUrl = SITE_URL+'users/update_password/';
	//alert(postUrl);
	jQuery('#plsLoaderID').show();
	jQuery.ajax({
		cache:false,
		async: false,
		type: "GET",
		url: postUrl,
		success: function(msg){
		/** Update the div**/
		jQuery('#changepassword').html(msg);
		jQuery('#plsLoaderID').hide();
	}
	});
}

window.onload=update_password(),update_address();
</script>
