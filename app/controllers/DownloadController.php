<?php

class DownloadController extends AppController {

	function index() {
		
		// Validate access here
		$user = new User();
		
		// Show file to the user here
		if($user->check() && isset($_GET['param1']))
		{
			$file = PUBLIC_FOLDER.'/images/'.$_GET['param1'];
			
			if(file_exists($file)){
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: '.filesize($file));
				
				$handle = fopen($file, "rb");
				while (!feof($handle)) {
					echo fread($handle, 1000);
				}
				fclose($handle);
				exit();
			}
		}
		$this->redirect('');
		
	}
	
}
