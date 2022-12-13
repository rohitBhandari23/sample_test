
<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0" valign="top">
<tr>
	<td colspan="2">
		<table width="100%" cellspacing="0" class="adminBox" cellpadding="0" align="center" border="0">
		<tr class="adminBoxHeading">
			<td height="25" class="reportListingHeading">Product Dump</td>
		</tr>

		<tr>
			<td>
				<table width="100%" cellspacing="1" cellpadding="2" class="adminBox" align="center" border="0">
					<tr>
						<td>
							<?php echo $form->create('Adminexports',array('action'=>'export_product_data','method'=>'POST', "id"=>"frmSearchColor", "name"=>"frmSearchColor"));?>

							<table width="100%" cellspacing="1" cellpadding="1" align="center" border="0" class="keywordtbl_search">
								<tr>
									<td align="left" width="60%">
										<div class="keyword_widget">
											<label style="margin-bottom:10px;font-size:11px;  margin-top:0;">Department:</label>
											<div class="field_widget">
												<p class="pdrt2">
													<select name="data[category]" class="select" size="1" id="SearchSearchin">
														<option value="all">--All Departments--</option>
														<?php foreach($departmentDetail as $departmentDetailVal) { ?> } 
															<option value="<?php echo $departmentDetailVal['Department']['id']; ?>"><?php echo $departmentDetailVal['Department']['name']; ?></option>
														<?php } ?>
													</select>   
												</p>
											</div>
										</div>
									</td>
								</tr>
								<tr>
								   <td><p style="background:#f1f1f1;padding:5px 8px; border:1px #dfdfdf solid; font-size:11px; font-weight:bold; margin-top:10px;">Table Product:<span style="float:right;font-size:11px; font-weight:bold;"><input type="checkbox" class="selectallP"/> <span class="selectallPName" style="vertical-align: top;">Select All</span></span></p></td>
								</tr>
								<tr>
									<td align="left" width="10%">
										<div class="keyword_widget">
											<div class="field_widget">
												<?php 
												foreach($productDesc['Product'] as $pkey=>$products) { 
							                        if($pkey!='api_id' && $pkey!='feed_id') {
							                        ?> 
													<div class="productlist" style="width:210px;">
							                        	<input name="data[fields][]" value="<?php echo 'Product.'.$pkey; ?>" class="individualP string login-input" type="checkbox">
														<span><?php echo ucwords(str_replace("_", " ", $pkey)); ?></span></div>    
							                        <?php  
							                    	}
							                    } 
							                    ?>
											</div>
										</div>
									</td>
								</tr>
<tr>
								   <td><p style="background:#f1f1f1;padding:5px 8px; border:1px #dfdfdf solid; font-size:11px; font-weight:bold; margin-top:10px;">Table Product Detail:<span style="float:right;font-size:11px; font-weight:bold;"><input type="checkbox" class="selectallPD"/> <span class="selectallPDName" style="vertical-align: top;">Select All</span></span></p></td>
								</tr>
								<tr>
									<td align="left" width="10%">
										<div class="keyword_widget">
											
											<div class="field_widget">
												<?php 
												foreach($productDesc['ProductDetail'] as $pdkey=>$products) { 
						                        	if($pdkey!='id' && $pdkey!='product_id') {
						                        ?> 
												<div class="productlist" style="width:210px;">
							                         	<input name="data[fields][]" value="<?php echo 'ProductDetail.'.$pdkey; ?>" class="individualPD string login-input" type="checkbox">
														<span><?php echo ucwords(str_replace("_", " ", $pdkey)); ?></span></div>                  
						                        <?php  } } ?>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td align="right" width="10%"><?php echo $form->button('Download CSV',array('type'=>'submit','class'=>'button','div'=>false,'id'=>'checkDownload')); ?></td>
								</tr>
							</table>
							<?php echo $form->end(); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
			
		</table>
	</td>
</tr>
	

</table>


    <!--Search Start-->
<section class="grid_widget">
	<table width="100%" cellpadding="2" cellspacing="1"  border="0"  class="borderTable">
		
		<tr>
			<th><b>File Name</b></th>
			<th><b>Department</b></th>
            <th><b>Last Export</b></th>
			<th><b>Status</b></th>
			<th><b>Action</b></th>
	    </tr>
      	<?php $i=0; 
      	if($csvDownloadData){
      	foreach ($csvDownloadData as $key => $csvDownloadDataVal) { $i++; ?>
      		<tr <?php if($i%2==0){?>  class="rowClassEven" <?php } else {?> class="rowClassOdd" <?php }?>>
          		<td><?php echo $csvDownloadDataVal['ProductCsvDownload']['filename']; ?></td>
          		<td style="text-align: center;"><?php if(isset($csvDownloadDataVal['Department']['name'])) {echo $csvDownloadDataVal['Department']['name']; } else { echo "All"; } ?></td>
          		<td style="text-align: center;"><?php echo date("d-m-Y",strtotime($csvDownloadDataVal['ProductCsvDownload']['created'])); ?></td>
          		<td style="text-align: center;">
          		<?php
          		$filename =  APP . 'webroot' . DS . 'product_csv' . DS .$csvDownloadDataVal['ProductCsvDownload']['filename']; 
          		if ($csvDownloadDataVal['ProductCsvDownload']['completed']==1) {
					echo "Completed";
				} else {
					echo "Processing";
				}
				?>
				</td>
          		<td style="text-align: center;">
          		<?php 
          		if ($csvDownloadDataVal['ProductCsvDownload']['completed']==1) {
					echo $this->Html->link($html->image('/img/images_new/tred_down.png',array('border'=>0,'alt'=>'')),array('admin' => true,'controller' => 'Adminexports', 'action' => 'viewdown', $csvDownloadDataVal['ProductCsvDownload']['filename']),array('escape'=>false,'title'=>'Download'), false, false);
					echo '&nbsp;';
					echo $this->Html->link($html->image('/img/b_drop.png',array('border'=>0,'alt'=>'')), array('admin' => true,'controller' => 'Adminexports', 'action' => 'deleteCSV', base64_encode($csvDownloadDataVal['ProductCsvDownload']['id'])),array('escape'=>false,'title'=>'Delete'), __('Are you sure you want to delete file?', $csvDownloadDataVal['ProductCsvDownload']['filename']));
				}
          		?>
          			
          		</td>
          	</tr>	
      	<?php } } else { ?>
      		<tr>
		              <td colspan="5" style="text-align:center; background:#ffffff;">No Records Found.</td>
		            </tr>

      	<?php }?>
      	
	</table>

	<section class="guide" style="margin-top:10px; margin-bottom:10px;">
		<p class="view_sec"><strong>Legends:</strong>
			<img src="/img/images_new/tred_down.png" alt="Download" title="Download" border="0"> Download
			<img src="/img/b_drop.png" alt="Delete" title="Delete" border="0"> Delete
		</p>
	</section>

</section>

<style>
.productlist{ float:left; margin-right:20px; margin-top:10px; min-width:150px;}
.productlist span{ margin-left:3px; float:left;}
.productlist input{ float:left;}
.keyword_widget label{     width: 96px;font-weight: bold;font-size: 13px;margin-top: 10px;}
.keyword_widget { margin-top:10px;}
.button{width: 150px;height: 30px;border-top-left-radius: 8px;border-bottom-left-radius: 8px;}
</style>

<script type="text/javascript">

jQuery(document).ready(function(){	
	jQuery(".selectallP").click(function(){	
		if(jQuery(this).is(":checked")){
			jQuery(".individualP").prop("checked","checked");		
			jQuery(".selectallPName").html("De-Select All");
		}else{
			jQuery(".individualP").removeAttr("checked");		
			jQuery(".selectallPName").html("Select All");
		}
		
	});    
	
	jQuery(".individualP").click(function(){
			
			var parentflag = 0;
			var totalchild = jQuery(".individualP").length;
			
			jQuery(".individualP").each(function(){
					if(jQuery(this).is(":checked")){
						parentflag++;
					}
			});
			if(totalchild==parentflag){
				jQuery(".selectallP").prop("checked","checked");		
				jQuery(".selectallPName").html("De-Select All");
			}else{
				jQuery(".selectallP").removeAttr("checked");
				jQuery(".selectallPName").html("Select All");		
			}
				
	});
	
	
	jQuery(".selectallPD").click(function(){	
		if(jQuery(this).is(":checked")){
			jQuery(".individualPD").prop("checked","checked");		
			jQuery(".selectallPDName").html("De-Select All");
		}else{
			jQuery(".individualPD").removeAttr("checked");	
			jQuery(".selectallPDName").html("Select All");	
		}
		
	});    
	
	jQuery(".individualPD").click(function(){
			
			var parentflag = 0;
			var totalchild = jQuery(".individualPD").length;
			
			jQuery(".individualPD").each(function(){
					if(jQuery(this).is(":checked")){
						parentflag++;
					}
			});
			if(totalchild==parentflag){
				jQuery(".selectallPD").prop("checked","checked");		
				jQuery(".selectallPDName").html("De-Select All");	
			}else{
				jQuery(".selectallPD").removeAttr("checked");		
				jQuery(".selectallPDName").html("Select All");	
			}
				
	});
	
});

var individualP = []; 
jQuery( "#frmSearchColor" ).submit(function( event ) {

  jQuery(".individualP").each(function(){
  	if(jQuery(this).prop("checked")==true){
  		individualP.push(jQuery(this).val());	
  	}
  });
  jQuery(".individualPD").each(function(){
  	if(jQuery(this).prop("checked")==true){
  		individualP.push(jQuery(this).val());	
  	}
  });
  var department = jQuery('#SearchSearchin').val();
  if(individualP=='' || department==''){
  	alert("Please select any table column.");
  	event.preventDefault();	
  }
  
});
</script>