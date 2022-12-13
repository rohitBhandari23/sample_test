<!--Right Start-->        
<section class="admin_content">
    <h5 class="head"><?php echo 'Product Export';//echo $listTitle;?></h5>
	
    <?php 
	echo $form->create('GdAdminexports',array('action'=>'export_stock_data','method'=>'POST','name'=>'frmSetting','id'=>'frmSetting'));?>
	<section class="search_widget">
            	
            	<section class="frame border_top0">
                	
                	<ul class="search_fields">
                	    <h5>GDStock:</h5>
                        <?php foreach($productDesc['GdStock'] as $gkey=>$GdStockVal) { 
                        		if($gkey!='id' && $gkey!='product_id') {
                        	?> 
                        	<li style="width: 150px; ">
	                         	<input name="data[fields][]" value="<?php echo 'GdStock.'.$gkey; ?>" class="string login-input" type="checkbox"><?php echo ucwords(str_replace("_", " ", $gkey)); ?>&nbsp;&nbsp;
	                        </li>
                        <?php } } ?>
                    </ul>

                	<ul class="search_fields">
                	    <h5>Product:</h5>
                        <?php foreach($productDesc['Product'] as $pkey=>$products) { 
                        	if($pkey!='id') {
                        ?> 
                        	<li style="width: 150px; ">
	                         	<input name="data[fields][]" value="<?php echo 'Product.'.$pkey; ?>" class="string login-input" type="checkbox"><?php echo ucwords(str_replace("_", " ", $pkey)); ?>&nbsp;&nbsp;                  
	                        </li>
                        <?php  } } ?>
                    </ul>

                    <ul class="search_fields">
                	    <h5>Product detail:</h5>
                        <?php foreach($productDesc['ProductDetail'] as $pdkey=>$products) { 
                        	if($pdkey!='id' && $pdkey!='product_id') {
                        ?> 
                        	<li style="width: 150px;">
	                         	<input name="data[fields][]" value="<?php echo 'ProductDetail.'.$pdkey; ?>" class="string login-input" type="checkbox"><?php echo ucwords(str_replace("_", " ", $pdkey)); ?>&nbsp;&nbsp;                  
	                        </li>
                        <?php } } ?>
                    </ul>

                </section>
                <?php echo $form->button('Download CSV',array('type'=>'submit','class'=>'button','div'=>false)); ?>
	</section>

	<?php echo $form->end(); ?>

            <!--Search Start-->
	<section class="grid_widget">
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="grid">
			
			<tr>
	            <th>File Name</th>
	            <th>Last Export</th>
				<th>Status</th>
				<th>Action</th>
		    </tr>
          	<?php foreach ($csvDownloadData as $key => $csvDownloadDataVal) { ?>
          		<tr>
	          		<td><?php echo $csvDownloadDataVal['ProductCsvDownload']['filename']; ?></td>
	          		<td><?php echo $csvDownloadDataVal['ProductCsvDownload']['created']; ?></td>
	          		<td>
	          		<?php
	          		$filename =  APP . 'webroot' . DS . 'product_csv' . DS .$csvDownloadDataVal['ProductCsvDownload']['filename']; 
	          		if (file_exists($filename)) {
						echo "Completed";
					} else {
						echo "Processing";
					}
					?>
					</td>
	          		<td>
	          		<?php 
	          		if (file_exists($filename)) {
						echo $this->Html->link('Download', array('admin' => true,'controller' => 'GdAdminexports', 'action' => 'viewdown', $csvDownloadDataVal['ProductCsvDownload']['filename']));
					}
	          		?>
	          			
	          		</td>
	          	</tr>	
          	<?php } ?>
          	
		</table>
	</section>
	<!--Search Closed-->

	
    
</section>
<!--Right Closed-->
