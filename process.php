<?php
$data['result']='error';

// функция для проверки длины строки
function validStringLength($string,$min,$max) {
  $length = mb_strlen($string,'UTF-8');
  if (($length<$min) || ($length>$max)) {
    return false;
  }
  else {
    return true;
  }
}

// если данные были отправлены методом POST, то...
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // устанавливаем результат, равный success
    $data['result']='success';
    //получить имя, которое ввёл пользователь
    if (isset($_POST['sender'])) {
      $name = $_POST['sender'];
      if (!validStringLength($name,2,30)) {
        $data['message']='Поля имя содержит недопустимое количество символов.';   
        $data['result']='error';     
      }
    } else {
      $data['result']='error';
	  $data['message']='Поле отправитель не заполнено ';
    } 
    //получить email, которое ввёл пользователь
    if (isset($_POST['email'])) {
      $email = $_POST['email'];	  
      if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $data['message']='Поле email отправителя введено неправильно';
        $data['result']='error';
      }
    } else {
      $data['result']='error';
	  $data['message']='Не заполнен email отправителя';
    }
	 //получить email, которое ввёл пользователь
    if (isset($_POST['email_to'])) {
      $email = $_POST['email_to'];
      if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $data['message']='Поле email получателя введено неправильно';
        $data['result']='error';
      }
    } else {
      $data['result']='error';
	  $data['message']= 'Не заполнен email получателя';
    }
     //получить сообщение, которое ввёл пользователь
    if (isset($_POST['message'])) {
      $message = $_POST['message'];
      if (!validStringLength($message,20,500)) {
        $data['message']='Поле сообщение содержит недопустимое количество символов.';     
        $data['result']='error';   
      }      
    } else {
      $data['result']='error';
	  $data['message']='Поле сообщение не заполнено';
    } 
	
	// если не существует ни одной ошибки, то прододжаем... 
	$files = array();
    if ($data['result']=='success') {
		require_once dirname(__FILE__) . '/lib/uploader.class.php';
		$file = new uploader("/upload",1);
		$err = $file->upload($_FILES['file']);
		if ($err['error']!=0) {		
			$data['message']='При загрузке файла 1 выявлена ошбика №'.$err;     
			$data['result']='error';   
		} else {
			$files[] = $err['filename'];
		}
		
		$err = $file->upload($_FILES['file2']);
		if ($err['error']!=0) {
			$data['message']='При загрузке файла 2 выявлена ошбика №'.$err;     
			$data['result']='error';   
		} else {
			$files[] = $err['filename'];
		}
    }
	
    // если не существует ни одной ошибки, то прододжаем... 
    if ($data['result']!='success') {
		$data['result']='error';
	} else {
		// Отправляем на почту
		require_once dirname(__FILE__) . '/lib/PHPMailerAutoload.php';
		//формируем тело письма
		$output = "<p>Дата: " . date("d-m-Y H:i") . "</p>";
		$output .= "<p>Имя пользователя: " . $_POST['sender'] . "</p>";
		$output .= "<p>Адрес email: " . $_POST['email'] . "\n";
		$output .= "<p>Сообщение: " . "<br/>" . $_POST['message'] . "</p>";		

		// создаём экземпляр класса PHPMailer
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8'; 
		$mail->From      = $_POST['email'];
		$mail->FromName  = $_POST['sender'];
		$mail->Subject   = 'Сообщение с тестовой формы';
		$mail->IsHTML(true);
		$mail->Body      = $output;
		$mail->AddAddress($_POST['email_to']);
		foreach ($files as $f) {
			$mail->AddAttachment($f['url'],$f['name']);			
			unset($f['url']);
		}
		// отправляем письмо
		if ($mail->Send()) {			
			$data['result']='success';
		} else {
			$data['message']='Ошибка отправки письма';
			$data['result']='error';
		}      
	}
  // формируем ответ, который отправим клиенту
  header('Content-type: application/json');
  $code = ($data['result']=="error") ? "400" : "200";
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit();  
}
?>