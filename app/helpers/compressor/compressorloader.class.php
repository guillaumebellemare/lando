<?php 
require_once('app/helpers/compressor/jsmin.class.php');
require_once('app/helpers/compressor/jsmin-plus.class.php');
require_once('app/helpers/compressor/cssmin.class.php');

class CompressorLoader {
	private $production_mode = 1;
	private $dir_root = 1;
	private $css;
	
	function __construct($production_mode = null, $dir_root = ''){
		if(isset($production_mode)) $this->production_mode = $production_mode;
		if(isset($dir_root)) $this->dir_root = $dir_root;
		else{
			$this->dir_root = dirname(dirname(__FILE__) . '../') . '/';
		} 	
		$this->css = new CSSmin();
	}
	
	function load($type = 'js', $to_load, $new_name = null, $onefile = true){
		$out = '';
		if($this->production_mode){
			if(is_array($to_load)){
				$to_load_min = $new_name;
				if(!file_exists($new_name) || !$onefile){
					$output = '';
					foreach($to_load as $arg => $file_to_load){
						if($onefile) $to_load_min = $this->minify($type, $file_to_load, null, $output, true);
						else{
							$to_load_min = $this->minify($type, $file_to_load);
							$out .= $this->write_tag($type, $to_load_min, $arg);
						}
					}
					if($onefile) {
						file_put_contents($new_name, $output);
					}
				}
				if($onefile && isset($new_name)){
					$out .= $this->write_tag($type, $new_name);
				}
			}else{
				$to_load_min = $this->minify($type, $to_load, $new_name);
				$out .= $this->write_tag($type, $to_load_min);
			}
		}else{
			if(!is_array($to_load))$to_load = array($to_load);
			foreach($to_load as $arg => $file_to_load){
				$out .= $this->write_tag($type, $file_to_load, $arg);
			}
			
		}
		return $out;
	}
	
	function minify($type = 'js', $to_load, $new_name = null, &$global_output = '', $force_load = false){
		//Determinate name
		if(isset($new_name)){
			$to_load_min = $new_name;
		}else{
			//add extension
			$aFileInfo = explode(".".$type, $to_load);
			$aFileInfo[0] .= '-min';
			$to_load_min = implode(".".$type, $aFileInfo);
			//add folder
			$aFileInfo = explode("/", $to_load_min);
			$aFileInfo[count($aFileInfo)-1] = 'min/'.$aFileInfo[count($aFileInfo)-1];
			$to_load_min = implode("/", $aFileInfo);

		}
		//Create min file if it doesn't exists
		if(!file_exists($to_load_min) || filemtime($to_load_min) < filemtime($to_load)){
			$file = file_get_contents($to_load);
			if($type == 'js') $output = JSMinPlus::minify($file); 
			else if($type == 'css') $output = $this->css->run($file); 
			file_put_contents($to_load_min, $output);
		}else if($force_load){
			$output = file_get_contents($to_load_min);
		}else $output = '';
		$global_output .= $output;
		return $to_load_min;
	}
	
	function write_tag($type = 'js', $file_to_load, $arg = ''){
		if($type == 'css'){
			$out = '<link rel="stylesheet" href="'.$this->dir_root.$file_to_load.''.(!is_numeric($arg) ? $arg : '').'" type="text/css" media="screen" />' . "\r\n";
		}else if($type == 'js'){
			$out = '<script src="'.$this->dir_root.$file_to_load.''.(!is_numeric($arg) ? $arg : '').'" type="text/javascript"></script>' . "\r\n";
		}
		return $out;
	}
	
}
?>