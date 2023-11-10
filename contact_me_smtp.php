<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("php/Exception.php");
include("php/PHPMailer.php");
include("php/SMTP.php");

if($_POST)
{
    $to_Email       = "info@ecodashsrl.com"; // Replace with recipient email address
	$subject        = 'Sitio Web '.$_SERVER['SERVER_NAME']; //Subject line for emails
    
    $host           = "smtp.dreamhost.com"; // Your SMTP server. For example, smtp.gmail.com
    $username       = "info@ecodashsrl.com"; //For example, your.email@gmail.com
    $password       = "EcoDash.2023"; // Your password
    $SMTPSecure     = "ssl"; // For example, ssl
    $port           = 465; // For example, 465
    
    
    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    
        //exit script outputting json data
        $output = json_encode(
        array(
            'type'=>'error', 
            'text' => 'Request must come from Ajax'
        ));
        
        die($output);
    } 
    
    //check $_POST vars are set, exit if any missing
    if(!isset($_POST["userName"]) || !isset($_POST["userEmail"]) || !isset($_POST["userMessage"]))
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Los campos están vacíos!'));
        die($output);
    }

    //Sanitize input data using PHP filter_var().
    $user_Name        = filter_var($_POST["userName"], FILTER_SANITIZE_STRING);
    $user_Email       = filter_var($_POST["userEmail"], FILTER_SANITIZE_EMAIL);
    $user_Message     = filter_var($_POST["userMessage"], FILTER_SANITIZE_STRING);
    
    $user_Message = str_replace("\&#39;", "'", $user_Message);
    $user_Message = str_replace("&#39;", "'", $user_Message);
    
    //additional php validation
    if(strlen($user_Name)<4) // If length is less than 4 it will throw an HTTP error.
    {
        $output = json_encode(array('type'=>'error', 'text' => 'El nombre es muy corto o está vacío !'));
        die($output);
    }
    if(!filter_var($user_Email, FILTER_VALIDATE_EMAIL)) //email validation
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Por favor, ingrese un email válido !'));
        die($output);
    }
    if(strlen($user_Message)<5) //check emtpy message
    {
        $output = json_encode(array('type'=>'error', 'text' => 'Mensaje muy corto, ingrese algo.'));
        die($output);
    }
    

	$mail = new PHPMailer();

    $mail->IsSMTP(); 
	$mail->SMTPAuth = true;	
	$mail->Host = $host;
	$mail->Username = $username;
	$mail->Password = $password;
	$mail->SMTPSecure = $SMTPSecure;
	$mail->Port = $port;		 
	$mail->setFrom($username);
	$mail->addReplyTo($user_Email);	 
	$mail->AddAddress($to_Email);
	$mail->Subject = $subject;
	$mail->Body = $user_Message. "\r\n\n"  .'Name: '.$user_Name. "\r\n" .'Email: '.$user_Email;
	$mail->WordWrap = 200;
	$mail->IsHTML(false);

	if(!$mail->send()) {

		$output = json_encode(array('type'=>'error', 'text' => 'El mensaje no pudo ser enviado: ' . $mail->ErrorInfo));
		die($output);

	} else {
	    $output = json_encode(array('type'=>'message', 'text' => 'Hola '.$user_Name .'! Muchas gracias por ponerte en contacto con nosotros'));
		die($output);
	}
    
}
?>