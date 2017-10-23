<?php
session_start();
if($_POST['client_name'] && $_POST['email'] && $_POST['website'] && $_POST['comments']) {
	if($_SESSION['nr'] == $_POST['verify']) {
      $to = 'catalinnita01@gmail.com';
			$headers = 'From: "'.$_POST['client_name'].'" <'.$_POST['email'].'>' . "\r\n";
			$headers .= 'Reply-To: no-reply' . "\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";	
					
			$subject = 'New message from WPFW clients';
			$message = '
			Website: <a href="'.$_POST['website'].'">'.$_POST['website'].'</a><br/><br/>
			'.$_POST['comments'].' ';			
											
			mail($to, $subject, $message, $headers);	    		
			echo '<div id="message" style="display: block;"><h3 class="text-color">Your message was successfully sent. You will get an answer within 24 hours.</h3></div>';
	}
	else {
		echo '<div id="message" style="display: block;"><div class="error_message">The verification code is invalid.</div></div>';
	}
}
else {
	echo '<div id="message" style="display: block;"><div class="error_message">Please fill in all fields.</div></div>';
}
?>