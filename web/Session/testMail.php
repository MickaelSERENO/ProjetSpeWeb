<?php
/*$to       = 'meunierpaul@free.fr';
$subject  = 'Mail Testing';
$message  = 'OMG! I\'ve finally succeeding in sending email using sendmail on WAMP!';
$headers  = 'From: noReplyAlbaSensei@gmail.com' . "\r\n" .
            'Reply-To: noReplyAlbaSensei@gmail.com' . "\r\n" .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
if(mail($to, $subject, $message, $headers))
    echo "Email sent";
else
    echo "Email sending failed";*/
?>

<a href="verif_account.php?mail=meunierpaul@free.fr&amp;code=5bd52a04496af8f">Lien d'activation</a>

<?php
/*require 'class.phpmailer.php';
$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Mailer = 'smtp';
$mail->SMTPAuth = true;
$mail->Host = 'smtp.gmail.com'; // "ssl://smtp.gmail.com" didn't worked
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';
// or try these settings (worked on XAMPP and WAMP):
// $mail->Port = 587;
// $mail->SMTPSecure = 'tls';
 
 
$mail->Username = "your_gmail_user_name@gmail.com";
$mail->Password = "your_gmail_password";
 
$mail->IsHTML(true); // if you are going to send HTML formatted emails
$mail->SingleTo = true; // if you want to send a same email to multiple users. multiple emails will be sent one-by-one.
 
$mail->From = "your_gmail_user_name@gmail.com";
$mail->FromName = "Your Name";
 
$mail->addAddress("user.1@yahoo.com","User 1");
$mail->addAddress("user.2@gmail.com","User 2");
 
$mail->addCC("user.3@ymail.com","User 3");
$mail->addBCC("user.4@in.com","User 4");
 
$mail->Subject = "Testing PHPMailer with localhost";
$mail->Body = "Hi,<br /><br />This system is working perfectly.";
 
if(!$mail->Send())
    echo "Message was not sent <br />PHPMailer Error: " . $mail->ErrorInfo;
else
    echo "Message has been sent";
*/
?>