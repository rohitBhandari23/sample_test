<?php
$states = $this->Common->getStates();
//pr($states);
?>
<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top">
<table align="center" width="98%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>
<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
<tr class="adminBoxHeading">
	<td class="adminGridHeading heading" height="25px" align="left"><?php echo $list_title; ?>
	</td>
	<td class="adminGridHeading" height="25px" align="right">
	<?php echo $html->link('Back',array('controller'=>'users','action'=>'index'));    ?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<table class="adminBox" border="0" cellpadding="2" cellspacing="2" width="100%">
			<tr height="5">
				<td width="1%"></td>
				<td width="20%" align="right"></td>
				<td width="3%" align="left"></td>
				<td align="left"></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Title</td>
				<td align="left" valign="top">:</td>
				<td align="left"> <?php echo ucfirst($this->data['User']['title']);?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Name</td>
				<td align="left" valign="top">:</td>
				<td align="left">
				<?php
				echo ucfirst($this->data['User']['firstname']).' '.
				ucfirst($this->data['User']['lastname']);
				?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Email</td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php echo $this->data['User']['email'];   ?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Address 1</td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php echo $this->data['User']['address1'];   ?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Address 2</td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php echo $this->data['User']['address2'];   ?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">City </td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php echo $this->data['User']['city'];   ?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">State/County </td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php
				if(isset($states[$this->data['User']['state']]) ){
					echo $states[$this->data['User']['state']];
				}else{
					echo $this->data['User']['state'];
				}?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Postcode </td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php echo $this->data['User']['postcode'];   ?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Country </td>
				<td align="left" valign="top">:</td>
				<td align="left">
				<?php if(!empty($this->data['Country']['country_name'])){
					echo $this->data['Country']['country_name'];
				}else{
					echo '-';
				}
				?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Phone</td>
				<td align="left" valign="top">:</td>
				<td align="left">
				<?php echo $this->data['User']['phone']; ?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Contact By Phone </td>
				<td align="left" valign="top">:</td>
				<td align="left">
				<?php 
					if( $this->data['User']['contact_by_phone'] == '1'){ // 1 for yes 0 for no 
						echo 'Yes' ;
					} else {
						echo 'No';
					} ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Contact By Partner. </td>
				<td align="left" valign="top">:</td>
				<td align="left">
					<?php 
					if($this->data['User']['contact_by_partner']  == '1'){
						echo 'Yes' ;
					} else {
						echo 'No';
					} ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Status  </td>
				<td align="left" valign="top">:</td>
				<td align="left">
					<?php 
					echo ( $this->data['User']['status']  == '1') ?('Active'):('In Active');
					?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Created on</td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php echo $format->date_format($this->data['User']['created']);
				?></td>
			</tr>
			<tr>
				<td></td>
				<td align="right" valign="top">Modified on</td>
				<td align="left" valign="top">:</td>
				<td align="left"><?php echo $format->date_format($this->data['User']['modified']);   ?></td>
			</tr>
			<tr class="adminGridHeading heading">
				<td colspan="4"><strong>Wants email alerts for following departments:</strong></td>
			</tr>
			<?php
			if(!empty($departments)){ ?>
				<?php foreach($departments as $dept_id => $department) {
					$display = $html->image('negative-icon.png',array('border'=>0,'alt'=>'No'));
					if(!empty($this->data['UserDepartment'])){
						foreach ($this->data['UserDepartment'] as $user_department){
							if($user_department['department_id']==$dept_id){
								$display = $html->image('positive-icon.png',array('border'=>0,'alt'=>'Yes'));
								break;
							}else
								$display = $html->image('negative-icon.png',array('border'=>0,'alt'=>'No'));
						}?>
					<?php } ?>
					<tr>
						<td></td>
						<td align="right" valign="top"><?php echo $department;?></td>
						<td align="left" valign="top">:</td>
						<td align="left"><?php echo $display;?></td>
					</tr>
				<?php }?>
			<?php }?>
		</table>
	</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>