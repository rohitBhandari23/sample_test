<?php

/**
 * Adminexports Controller class
 * PHP versions X
 * @date 
 * @Purpose:This controller handles all the functionalities regarding dashboard of admin.
 * @filesource
 * @revision
 * @copyright  Copyright � 2009 smartData
 * @version 0.0.1 
 * */
App::import('Sanitize');

class AdminexportsController extends AppController {
    var $name = "Adminexports";

    /**
     * Specifies helpers classes used in the view pages
     * @access public
     */
    var $helpers = array('Html', 'Form', 'Javascript', 'Session', 'Validation', 'Format', 'Ajax', 'Common');

    /**
     * Specifies components classes used
     * @access public
     */
    var $components = array('RequestHandler', 'Email', 'File', 'Common');
    var $paginate = array();
    var $uses = array();
    var $permission_id = 9;  

    /**
     * @Date: X
     * @Method : beforeFilter
     * Created By: X
     * @Purpose: This function is used to validate admin user permissions
     * @Param: none
     * @Return: none 
     * */

    public function admin_file_export() {
    	$this->checkSessionAdmin();
		$user_id = $this->Session->read('SESSION_ADMIN.id');
		if(!empty($user_id)) {
           // $this->redirect('/admin/homes/dashboard');
           // $this->set('referred_url' , $referred_url);
        }

		$this->layout = 'layout_admin';
		$this->loadModel('BackupSourceFile');
		$BackupSourceFileData = $this->BackupSourceFile->find("all");		
		$this->set('BackupSourceFileData',$BackupSourceFileData);
	}

	/**
     * @Date: X
     * @Method : beforeFilter
     * Created By: X
     * @Purpose: This function is used to validate admin user permissions
     * @Param: none
     * @Return: none 
     * */
	function admin_generatefilebackup() {
		$this->checkSessionAdmin();
		$name = "ABCD-".date("d-m-Y-H-i-s").".tar.gz";
		
		$this->loadModel('BackupSourceFile');

		$allFiles = $this->BackupSourceFile->find('all');
		foreach ($allFiles as $key => $value) {
			$file = $value['BackupSourceFile']['filename'];
			$path = ROOT.DS.'bkup'.DS.$file;
			unlink($path);
			$this->BackupSourceFile->delete($value['BackupSourceFile']['id']);
		}

		$csvData = array();
		$csvData['BackupSourceFile']['filename'] = $name;
		$this->BackupSourceFile->create();
		$this->BackupSourceFile->save($csvData,false);
		$lastid = $this->BackupSourceFile->getLastInsertId();

		$cmd = "php cron_dispatcher.php /admin/adminexports/startbackup/$lastid/$name";
		$this->admin_startBackup($lastid,$name);
		$this->redirect(array("controller"=>"adminexports","action"=>"admin_file_export","admin"=>true));
	}

	/**
     * @Date: X
     * @Method : beforeFilter
     * Created By: X
     * @Purpose: This function is used to validate admin user permissions
     * @Param: none
     * @Return: none 
     * */
	function admin_startbackup($lastid=null,$name=null) {
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '-1');
		$fileName = ROOT.DS.'bkup'.DS.$name;
		$path = ROOT.DS;
		$exclude = ROOT.DS.'bkup'.DS.'*';
		$exclude1 = ROOT.DS.'app'.DS.'webroot'.DS.'*';
		$exclude2 = ROOT.DS.'app'.DS.'tmp'.DS.'*';
		$exclude3 = ROOT.DS.'database_backup'.DS.'*';
		
		$output = shell_exec("tar cvpzf $fileName $path --exclude=$exclude --exclude=$exclude1 --exclude=$exclude2 --exclude=$exclude3");

		$this->loadModel('BackupSourceFile');
		$csvData = array();
		$csvData['BackupSourceFile']['id'] = $lastid;
		$csvData['BackupSourceFile']['completed'] = 1;
		
		$this->BackupSourceFile->save($csvData);
		return true;
		die('sucess');
	}

	/**
     * @Date: X
     * @Method : beforeFilter
     * Created By: X
     * @Purpose: This function is used to validate admin user permissions
     * @Param: none
     * @Return: none 
     * */	
	function admin_product_export() {
		$this->checkSessionAdmin();
		$this->layout = 'layout_admin';

		$this->loadModel('Department');
		$departmentDetail = $this->Department->find("all");
		$this->set('departmentDetail',$departmentDetail);

		$this->loadModel('Product');
		$this->Product->expects(array('ProductDetail'));

		$productDesc = $this->Product->find("first");

		$this->loadModel('ProductCsvDownload');
		$this->ProductCsvDownload->expects(array('Department'));
		$csvDownloadData = $this->ProductCsvDownload->find("all",
			array('order'=>'ProductCsvDownload.created DESC'));		
		$this->set('csvDownloadData',$csvDownloadData);
		$this->set('productDesc',$productDesc);
	}

	/**
     * @Date: X
     * @Method : beforeFilter
     * Created By: X
     * @Purpose: This function is used to validate admin user permissions
     * @Param: none
     * @Return: none 
     * */
	function admin_generateCSV($lastid,$department,$nameSheet,$selectedNodesString) {
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '-1');
		
        $this->loadModel('ProductCsvDownload');
        $selectedNodes = explode(',', $selectedNodesString); 

        foreach ($selectedNodes as $key => $value) {
			if($value=='Product.created'){
				$selectedNodes[$key] = 'Product.created as Pcreated';
			}
			if($value=='Product.modified'){
				$selectedNodes[$key] = 'Product.modified as Pmodified';
			}
		}

        $this->loadModel('Department');        
		$departmentList = $this->Department->find("list",array('fields'=>array('id','name')));

        $this->loadModel('Brand');        
		$brandList = $this->Brand->find("list",array('fields'=>array('id','name')));

        $this->loadModel('Product');

		$this->Product->expects(array('ProductDetail','ProductBarcode'));
		if($department=='all'){
			$productDesc = $this->Product->find("all",array('fields'=>$selectedNodes,'limit'=>200000,'order'=>'Product.id ASC'));
		} else {
			$productDesc = $this->Product->find("all",array('conditions'=>array('Product.department_id'=>$department),'fields'=>$selectedNodes,'limit'=>200000,'order'=>'Product.id ASC'));	
		}
        // create a file pointer connected to the output stream
        //$file = fopen('php://output', 'w');
        $path = ROOT."/app/webroot/product_csv".DS.$nameSheet;
        $file = fopen($path, 'w');
        //$output = shell_exec("chmod 777 $file");
        // send the column headers
        foreach ($selectedNodes as $key => $value) {
			$newVal = explode('.', $value); 
			$selectedNodes[$key] = ucwords(str_replace("_", " ", $newVal[1]));
		}
        fputcsv($file, $selectedNodes);   
        
        // output each row of the data
        foreach ($productDesc as $row) {
        	$merged = array();
        	$merged = array_merge($merged,$row['Product'],$row['ProductDetail']);

        	$merged['brand_id'] = $brandList[$merged['brand_id']];
        	$merged['department_id'] = $departmentList[$merged['department_id']];
        	
        	$productDesc = html_entity_decode(preg_replace( '/\s+/', ' ',strip_tags(nl2br($merged['description']))));
			$productDesc = strip_tags($productDesc);
			$productDesc =  str_replace('&#039;', "'", $productDesc);
			$productDesc =  str_replace('&amp;', '', $productDesc);
			
			$productFeatures =  html_entity_decode(preg_replace( '/\s+/', ' ',strip_tags(nl2br($merged['product_features']))));
			$productFeatures = strip_tags($productFeatures);
			$productFeatures =  str_replace('&#039;', "'", $productFeatures);
			$productFeatures =  str_replace('&amp;', '', $productFeatures);

			$productSearchtag =  html_entity_decode(preg_replace( '/\s+/', ' ',strip_tags(nl2br($merged['product_searchtag']))));
			$$productSearchtag = strip_tags($productSearchtag);
			$productSearchtag =  str_replace('&#039;', "'", $productSearchtag);
			$productSearchtag =  str_replace('&amp;', '', $productSearchtag);

			$merged['description'] = trim(str_replace('\n',' ',$productDesc));
        	$merged['product_features'] = trim(str_replace('\n',' ',$productFeatures));
        	$merged['product_searchtag'] = trim(str_replace('\n',' ',$productSearchtag));
        	
        	$barcode = array();
        	foreach ($row['ProductBarcode'] as $key => $value) {
        		$barcode[] = $value['barcode']; 
        	}
        	$merged['barcode'] = implode(",", $barcode);
        	fputcsv($file, $merged);
        }
        fclose($file);      
		$csvData = array();
		$csvData['ProductCsvDownload']['id'] = $lastid;
		$csvData['ProductCsvDownload']['completed'] = 1;
		$this->ProductCsvDownload->save($csvData);
		die('SUCCESS');
    }
}
?>
