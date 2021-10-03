<?php

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require Conf::get('root') . '/vendor/autoload.php';


/***************************************************************************************
 *
 * $mailFromAddress - from which address email has been sent(office@site.com)
 *
 *  $mailFromName - name of the sender(site.com)
 *
 *  $mailToAddress - address to send email(client@gmail.com)
 *
 * $mailSubject - email subject
 *
 *  $mailHtmlBody - email html
 *
 *  $mailAltBody - if html is not working, send alt body(plain text)
 *
 *  $mailAttachment - email attachment
 *
 *  $uploadedAttachment - if == true, attachment is uploaded tru form, else, it is generated file
 *
 *  $mailCC - email cc
 *
 *  $mailBCC - email bcc
 *
 *  $mailReplyToAddress - email reply address
 *
 *  $mailRecipientName  - email recipient name
 *
 ***************************************************************************************/
class Mailer {


  public function sendMail($data) {

    if (is_object($data)) $data = (array)$data;

    $mailFromAddress = isset($data['form_address']) ? $data['form_address'] : Conf::get('mail_from_address');
    $mailFromName = isset($data['form_name']) ? $data['form_name'] : Conf::get('mail_from_name');
    $mailToAddress = isset($data['to_address']) ? $data['to_address'] : Conf::get('mail_to_address');
    $mailSubject = $data['subject'];
    $mailHtmlBody = $data['html_body'];
    $mailAltBody = $data['alt_body'];
    $mailAttachment = isset($data['attachment']) ? $data['attachment'] : false;
    $uploadedAttachment = isset($data['uploaded_attachment']) ? $data['uploaded_attachment'] : false;
    $mailCC = isset($data['cc']) ? $data['cc'] : false;
    $mailBCC = isset($data['bcc']) ? $data['bcc'] : false;
    $mailReplyToAddress = isset($data['reply_to_address']) ? $data['reply_to_address'] : false;
    $mailRecipientName = isset($data['recipient_name']) ? $data['recipient_name'] : false;


    if (!$mailReplyToAddress) $mailReplyToAddress = $mailFromAddress;

    //PHPMailer Object
    $mail = new PHPMailer;

    //From email address and name
    $mail->From = $mailFromAddress;
    $mail->FromName = $mailFromName;
    $mail->CharSet = "UTF-8";

    //To address and name
    if ($mailRecipientName) {
      $mail->addAddress($mailToAddress, $mailRecipientName); //Recipient name is optional
    }
    else {
      $mail->addAddress($mailToAddress);
    }

    //Address to which recipient will reply
    $mail->addReplyTo($mailReplyToAddress, "Reply");

    //CC
    if ($mailCC) {
      $mail->addCC($mailCC);
    }

    //BCC
    if ($mailBCC) {
      $mail->addBCC($mailBCC);
    }

    if ($mailAttachment) {

      if ($uploadedAttachment && $mailAttachment['error'] == UPLOAD_ERR_OK) {

        $mail->addAttachment($mailAttachment['tmp_name'], $mailAttachment['name']);
      }
      else {
        $mail->addStringAttachment($mailAttachment['file_string'], $mailAttachment['name']);
      }
    }


    //Send HTML or Plain Text email
    $mail->isHTML(true);

    $mail->Subject = $mailSubject;
    $mail->Body = $mailHtmlBody;
    $mail->AltBody = $mailAltBody;

    try {
      $mail->send();
      return true;
    }
    catch (Exception $e) {
      Logger::put('Mailer Error: ' . $mail->ErrorInfo);
      return false;
    }
  }
	
}

?>