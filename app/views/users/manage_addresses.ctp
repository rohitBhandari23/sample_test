<?php ?>
<style type="text/css">
.pink-btn-widget a:hover{
text-decoration:none;
}
.gray-btn-widget a:hover{
text-decoration:none;
}
.form-widget a:hover{
text-decoration:underline;
}
</style>
<!--mid Content Start-->
<div class="mid-content">
	<!--- <?php //echo $this->element('useraccount/user_settings_breadcrumb');?> -->
	<!--Setting Tabs Widget Start-->
	<div class="row breadcrumb-widget">
		<?php echo $this->element('useraccount/tab');?>
		<!--Tabs Content Start-->
		<div class="tabs-content">
			<!--Form Widget Start-->
			<div class="form-widget">
				<ul>
					<li>Click the Change button next to any piece of information below that you'd like to adjust.</li>
					<li>When your changes are complete. <?php echo $html->link("Continue Shopping",'/homes',array('escape'=>false));?></li>
					<li>Click here to add a new address <?php echo $html->link("Enter a New Address",'/users/add_address',array('escape'=>false));?></li>
					<?php if ($session->check('Message.flash')){ ?>
					<li>
						<div>
							<div class="messageBlock"><?php echo $session->flash();?></div>
						</div>
					</li>
					<?php }
					//pr($addresses);
					if(!empty($addresses)){
						
						foreach($addresses as $address){
					?>
					<li>
						<p><strong><?php if(!empty($address['Address']['add_firstname'])){
							echo ucfirst($address['Address']['add_firstname']).' '.ucfirst($address['Address']['add_lastname']);
						} else { echo '-'; }?></strong></p>
						<p><?php if(!empty($address['Address']['add_address1'])){
							echo $address['Address']['add_address1'];
						} else {echo '-';}?></p>
						<?php if(!empty($address['Address']['add_address2'])){
							echo '<p>'.$address['Address']['add_address2'].'</p>';
						}?>
						<p><?php if(!empty($address['Address']['add_city'])){
							echo $address['Address']['add_city'];
						} else {echo '-';}?></p>
						<p><?php if(!empty($address['Address']['add_postcode'])){
							echo $address['Address']['add_postcode'];
						} else {echo '-';}?></p>
							<p><?php if(!empty($address['Address']['add_state'])){
							echo $address['Address']['add_state'];
						} else {echo '-';}?></p>
						<p><?php if(!empty($address['Country']['country_name'])){
							echo $address['Country']['country_name'];
						} else {echo '-';}?></p>
						<p>Ph: <?php if(!empty($address['Address']['add_phone'])){
							echo $address['Address']['add_phone'];
						} else {echo '-';}?></p>
						<p class="margin-top">
							<span class="pink-btn-widget">
							<?php echo $html->link("Change Details",'/users/add_address/'.$address['Address']['id'],array('class'=>'pink-button pink-btn-padd','style'=>'color:#FFFFFF;line-height:16px;padding-bottom:0;text-decoration:none;','escape'=>false));?>
							</span>
							<?php if(empty($address['Address']['primary_address'])){?>
							<span class="gray-btn-widget">
							<?php echo $html->link("Delete",'/users/delete_address/'.$address['Address']['id'],array('class'=>'gray-button','style'=>'color:#FFFFFF;line-height:19px;padding-bottom:0;padding-top:3px;text-decoration:none;','escape'=>false,'onclick'=>"return confirm('Are you sure you want to delete this address?');"));?>
							</span>
							<?php }?>
						</p>
					</li>
					<?php } }?>
				</ul>
			</div>
			<!--Form Widget Closed-->
		</div>
		<!--Tabs Content Closed-->
	</div>
	<!--Setting Tabs Widget Closed-->
</div>
<!--mid Content Closed-->