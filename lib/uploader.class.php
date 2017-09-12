<?php
class uploader {	
	public $max_size;		// макс размер в МБ
	public $path; 			// Путь до места где будет храниться	
	private $allow_types;	// разрешенные типы файлов
	function __construct($path, $max_size) {			
		$this->path				= $path;
		$this->allow_types 		= array('application/pdf', 'image/png', 'image/jpeg','application/msword');
		$this->max_size 		= $max_size*1024*1024;		
	}
			
	// загрузка одного файла
	public function upload($element) {		
		$filename = "";
		$outs = -1;
		if (!empty($element['name']) && $element['size']!=0 && $element['error']==0) {			
			if (in_array($element['type'],($this->allow_types))) {				
				if ($element['size']<=$this->max_size) {					
					$filename = $_SERVER['DOCUMENT_ROOT']."{$this->path}/{$element['name']}"; 							
					if (move_uploaded_file($element['tmp_name'],$filename)) {									
						chmod($filename,0644);
						$outs = 0; // Все ок
					}
				} else {
					$outs = 1; // Размер большой
				}
			} else {
				$outs = 2; // Тип не разрешен
			}
		} 
	 return array("error"=>$outs,"filename"=>array("url"=>$filename,"name"=>$element['name']));
	}	
}
?>