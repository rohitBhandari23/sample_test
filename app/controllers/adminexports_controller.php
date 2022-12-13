<?php

/**
 * Homes Controller class
 * PHP versions 5.1.4
 * @date 
 * @Purpose:This controller handles all the functionalities regarding dashboard of admin.
 * @filesource
 * @author     Ramanpreet Pal Kaur
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
     * @Date: Nov 12, 2011
     * @Method : beforeFilter
     * Created By: Nakul Kumar
     * @Purpose: This function is used to validate admin user permissions
     * @Param: none
     * @Return: none 
     * */
    function beforeFilter() {
        // parent::beforeFilter();
        // $cont = $this->params['controller'];
        // $acti = $this->params['action'];

        // if (@$_REQUEST['fullsite']) {
        //     $fullSite = $_REQUEST['fullsite'];
        //     $this->Session->write('fullSite', $fullSite);
        //     $fullSiteNew = $this->Session->read('fullSite');
        // }
        // if ($cont == 'homes' && $acti == 'index') {
        //     $check = SITE_URL . "css/slider1/theme-metallic.css";
        //     if (@$fullSiteNew != 'go' && (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != $check)) {
        //         $this->Session->delete('fullSite');
        //     }
        // }
        // $this->detectMobileBrowser();


        //For Mobile dection function
        //For Mobile dection function
        /* if(@$_REQUEST['fullsite']){
          $fullSite = $_REQUEST['fullsite'];
          $this->Session->write('fullSite',$fullSite);
          $fullSite = $this->Session->read('fullSite');
          }
          if(@$fullSite!='go'){
          $this->Session->delete('fullSite');
          $this->detectMobileBrowser();
          } */
    }

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
		//echo "<pre>"; print_r($BackupSourceFileData); exit;
	}

	function admin_generatefilebackup() {
		$this->checkSessionAdmin();
		$name = "Choiceful_FilebackUP-".date("d-m-Y-H-i-s").".tar.gz";
		
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
		//echo exec($cmd . " > /dev/null 2>/dev/null &");	
		$this->admin_startBackup($lastid,$name);
		$this->redirect(array("controller"=>"adminexports","action"=>"admin_file_export","admin"=>true));
	}

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
		
		//tar cvpzf /var/www/vhosts/goodwinsdirect/bkup/FilebackUP-222222.tar.gz /var/www/vhosts/goodwinsdirect/ --exclude='/var/www/vhosts/goodwinsdirect/bkup/*'
		$output = shell_exec("tar cvpzf $fileName $path --exclude=$exclude --exclude=$exclude1 --exclude=$exclude2 --exclude=$exclude3");

		$this->loadModel('BackupSourceFile');
		$csvData = array();
		$csvData['BackupSourceFile']['id'] = $lastid;
		$csvData['BackupSourceFile']['completed'] = 1;
		
		$this->BackupSourceFile->save($csvData);
		return true;
		die('sucess');
		//echo "<pre>"; print_r($csvData); exit;
	}

	
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
		//echo "<pre>"; print_r($this->ProductCsvDownload->schema()); exit;
		$this->ProductCsvDownload->expects(array('Department'));
		$csvDownloadData = $this->ProductCsvDownload->find("all",array('order'=>'ProductCsvDownload.created DESC'));		
		$this->set('csvDownloadData',$csvDownloadData);

		//echo "<pre>s"; print_r($csvDownloadData); exit;
		$this->set('productDesc',$productDesc);
	}

	function admin_generateCSV($lastid,$department,$nameSheet,$selectedNodesString) {
		set_time_limit(0);
		//ini_set('memory_limit','999999M');
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '-1');
		
        // output headers so that the file is downloaded rather than displayed
        $this->loadModel('ProductCsvDownload');
        //header('HTTP/1.1 420 Enhance Your Calm'); 
        $selectedNodes = explode(',', $selectedNodesString); 

        foreach ($selectedNodes as $key => $value) {
			if($value=='Product.created'){
				$selectedNodes[$key] = 'Product.created as Pcreated';
			}
			if($value=='Product.modified'){
				$selectedNodes[$key] = 'Product.modified as Pmodified';
			}
		}

       	//header('Content-type: text/csv');
        //header('Content-Disposition: attachment; filename='.$nameSheet);
        //echo "<pre>"; print_r($nodesData); exit;
        // do not cache the file
        //header('Pragma: no-cache');
        //header('Expires: 0');

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
			//$productDesc = preg_replace("/[\/\&%#\$]/", " ", $productDesc);

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
        	//fputcsv($file, $row['ProductDetail']);
        }
        fclose($file);

        
		$csvData = array();
		$csvData['ProductCsvDownload']['id'] = $lastid;
		$csvData['ProductCsvDownload']['completed'] = 1;
		$this->ProductCsvDownload->save($csvData);
		//echo "<pre>"; print_r($csvData); exit;
        die('SUCCESS');
    }

	function admin_export_product_data() {
		//Configure::write('debug', 2);
		$this->layout = 'layout_admin';

		$fields = $this->data['fields'];
		$department = $this->data['category'];
		$fileName = "Choiceful_Product_dump-".date("d-m-Y-H-i-s").".csv";

		$this->loadModel('ProductCsvDownload');


		$allCSVFiles = $this->ProductCsvDownload->find('all',array('order'=>array('ProductCsvDownload.id DESC')));

		$i = 1;
		foreach ($allCSVFiles as $key => $value) {
			if($i > 4) {
				$file = $value['ProductCsvDownload']['filename'];
				$path = APP . 'webroot' . DS . 'product_csv' . DS.$file; 
				unlink($path);
				$this->ProductCsvDownload->delete($value['ProductCsvDownload']['id']);	
			}
			$i++;
		}

		$csvData = array();
		$csvData['ProductCsvDownload']['filename'] = $fileName;
		$csvData['ProductCsvDownload']['data'] = serialize($fields);
		$csvData['ProductCsvDownload']['department_id'] = $department;
		$this->ProductCsvDownload->create();
		$this->ProductCsvDownload->save($csvData,false);
		$lastid = $this->ProductCsvDownload->getLastInsertId();

		$fieldsString = implode(",", $fields);
		
		$cmd = "php cron_dispatcher.php /admin/adminexports/generateCSV/$lastid/$department/$fileName/$fieldsString";
		echo exec($cmd . " > /dev/null 2>/dev/null &");	
		//$this->admin_generateCSV($lastid,$department,$fileName,$fieldsString);
		//echo "<pre>"; print_r($csvData); exit;
		$this->redirect(array("controller"=>"adminexports","action"=>"admin_product_export","admin"=>true));
	}



	function admin_viewdown($filename=null) {
		$this->checkSessionAdmin();
    	$name=explode('.',$fileName);
      	$this->viewClass = 'Media';
     
     	$filenameParts = explode('.', $filename);
		$filenameExt = array_pop($filenameParts);
		$filenameBase = implode('.', $filenameParts);

		if($filenameExt == 'csv') {
			$ext = strtolower($filenameExt);
			$path = APP . 'webroot' . DS . 'product_csv' . DS;
		} else {
			$ext = 'gz';
			$path = ROOT.DS.'bkup'.DS;
		}

		$this->view = 'Media';
		$params = array(
			'id' => $filename,
			'name' => $filenameBase,
			'download' => true,
			'extension' => $ext,
			'path' => $path,  // don't forget terminal 'DS'
			'cache' => true,
			);
		//echo "<pre>"; print_r($params); exit;
		$this->set($params);
    }

    function admin_stock_export() {
    	$this->layout = 'layout_admin';

    	//Configure::write('debug', 2);
		$this->loadModel('Product');
		
		$this->Product->bindModel(array("hasOne" => array("GdStock","ProductDetail")));
		$productDesc = $this->Product->find("first");
		//echo "<pre>s"; print_r($productDesc); exit;

		$this->loadModel('ProductCsvDownload');
		$csvDownloadData = $this->ProductCsvDownload->find("all",array('order'=>'ProductCsvDownload.created DESC'));		
		$this->set('csvDownloadData',$csvDownloadData);
		
		$this->set('productDesc',$productDesc);
	}

	function admin_export_stock_data() {
		//Configure::write('debug', 2);
		$this->layout = 'layout_admin';

		$fields = $this->data['fields'];
		$fileName = "Choiceful_Product_dump-".date("d-m-Y-H-i-s").".csv";
		//echo "<pre>sd"; print_r($fields); exit;

		$this->loadModel('ProductCsvDownload');
		$csvData = array();
		$csvData['ProductCsvDownload']['filename'] = $fileName;
		$csvData['ProductCsvDownload']['data'] = serialize($fields);
		$this->ProductCsvDownload->create();
		$this->ProductCsvDownload->save($csvData,false);

		$this->loadModel('Product');
		$this->Product->expects(array('ProductDetail'));
		//$this->Product->bindModel(array("hasOne" => array("GdStock","ProductDetail")));

		$joins = array(
						array('table' => 'db_goodwinsdirect.gd_stocks',
							'type' => 'INNER',
							'alias' => 'GdStock',
							'conditions' => array (
								'GdStock.product_id= Product.id'
							)
						)
					);
		$productDesc = $this->Product->find ( "first", array (
			'joins'=>$joins,
			'fields'=>$fields
		) );

		

		//$productDesc = $this->Product->find("count");
		//echo "<pre>sd"; print_r($productDesc); exit;

		$this->generateCSV($productDesc,$fileName,$fields);
		//echo "<pre>"; print_r($productDesc); exit;
		$this->redirect(array("controller"=>"gd_adminexports","action"=>"admin_stock_export","admin"=>true));
	}

	function admin_deleteBackup($id=null) {
		$id = base64_decode($id);
		$this->loadModel('BackupSourceFile');
		$bakupData = $this->BackupSourceFile->findById($id);
		$file = $bakupData['BackupSourceFile']['filename'];
		$path = ROOT.DS.'bkup'.DS.$file;
		unlink($path);
		$this->BackupSourceFile->delete($id);
		$this->redirect(array("controller"=>"adminexports","action"=>"admin_file_export","admin"=>true));
	}

	function admin_deleteCSV($id=null) {
		$id = base64_decode($id);
		$this->loadModel('ProductCsvDownload');
		$bakupData = $this->ProductCsvDownload->findById($id);
		$file = $bakupData['ProductCsvDownload']['filename'];
		$path = APP . 'webroot' . DS . 'product_csv' . DS.$file; 
		unlink($path);
		$this->ProductCsvDownload->delete($id);
		$this->redirect(array("controller"=>"adminexports","action"=>"admin_product_export","admin"=>true));
	}

}

?>
