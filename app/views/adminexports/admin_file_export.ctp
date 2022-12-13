<!--Right Start-->        
<section class="admin_content">
        <h1 style="background:#f1f1f1; font-size:13px; padding:5px;"><?php echo 'Source code';?></h1>
        <br>
	<!--Search Start-->
	<section class="grid_widget">
	<span style="float:right; margin-right:20px;">
        	<?php echo $html->link("Generate Backup",array("controller"=>"adminexports","action"=>"admin_generatefilebackup"),array('class'=>'button', 'style'=>'padding: 7px;border-top-left-radius: 7px;border-bottom-left-radius: 7px;text-align: center;text-decoration: none;')); ?></span>
        	<br><br>
		<table width="100%" cellpadding="2" cellspacing="1"  border="0"  class="borderTable">
			 	<tr>
	                <th><b>File Name</b></th>
	                <th><b>Last Export</b></th>
					<th><b>Status</b></th>
					<th><b>Action</b></th>
              	</tr>              
	          <?php $i=0;
	          if($BackupSourceFileData){
	          foreach ($BackupSourceFileData as $key => $BackupSourceFileDataVal) { $i++;?>
          		<tr <?php if($i%2==0){?>  class="rowClassEven" <?php } else {?> class="rowClassOdd" <?php }?>>
	          		<td><?php echo $BackupSourceFileDataVal['BackupSourceFile']['filename']; ?></td>
	          		<td style="text-align: center;"><?php echo date("d-m-Y",strtotime($BackupSourceFileDataVal['BackupSourceFile']['created'])); ?></td>
	          		<td style="text-align: center;">
	          		<?php
	          		$filename = ROOT.DS.$BackupSourceFileDataVal['BackupSourceFile']['filename']; 
	          		if ($BackupSourceFileDataVal['BackupSourceFile']['completed']==1) {
						echo "Completed";
					} else {
						echo "Processing";
					}
					?>
					</td>
	          		<td style="text-align: center;">
	          		<?php 
	          		if ($BackupSourceFileDataVal['BackupSourceFile']['completed']==1) {
						echo $this->Html->link($html->image('/img/tred_down.png',array('border'=>0,'alt'=>'')), array('admin' => true,'controller' => 'Adminexports', 'action' => 'viewdown', $BackupSourceFileDataVal['BackupSourceFile']['filename']),array('escape'=>false,'title'=>'Download'), false, false);
						echo '&nbsp;';
						echo $this->Html->link($html->image('/img/b_drop.png',array('border'=>0,'alt'=>'')), array('admin' => true,'controller' => 'Adminexports', 'action' => 'deleteBackup', base64_encode($BackupSourceFileDataVal['BackupSourceFile']['id'])),array('escape'=>false,'title'=>'Delete'),  __('Are you sure you want to delete file?', $BackupSourceFileDataVal['BackupSourceFile']['filename']));
					}
					?></td>
	          	</tr>	
          	  <?php }} else { ?>
          	  		<tr>
		              <td colspan="5" style="text-align:center; background:#ffffff;">No Records Found.</td>
		            </tr>
          	  <?php } ?>

		</table>
	</section>
	<!--Search Closed-->
	<section class="guide" style="margin-top:10px; margin-bottom:10px;">
		<p class="view_sec"><strong>Legends:</strong>
			<img src="/img/images_new/tred_down.png" alt="Download" title="Download" border="0"> Download
			<img src="/img/b_drop.png" alt="Delete" title="Delete" border="0"> Delete
		</p>
	</section>
            
</section>
<!--Right Closed-->
