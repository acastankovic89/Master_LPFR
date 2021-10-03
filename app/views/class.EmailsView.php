<?php


class EmailsView extends MainView {

   private $br = "\r\n";

   public function __construct() {

      parent::__construct();

      Trans::initTranslations();
   }


   public function userActivationTemplate($data) {

      $htmlBody  = '<div style="width:100%;height:100%;background:#123764;color:#404040;font-family:Arial,serif;">';
         $htmlBody .= '<div style="width:80%;height:100%;margin: auto;background:#ffffff;text-align:center;">';
            $htmlBody .= '<h2>Aktivacija korisničkog naloga</h2>';
            $htmlBody .= '<p>Za aktivaciju Vašeg korisničkog naloga na e-mail adresi ' . $data->email . ' kliknite na link:</p>';
            $htmlBody .= '<div><a href="' . Conf::get('url') . '/users/activate/' . $data->activation_token. '" style="display:inline-block;margin:6px 0;background:#fd0;color:#123764;padding:10px;text-decoration:none;">Aktivacija korisničkog naloga</a></div>';
            $htmlBody .= '<hr>';
            $htmlBody .= '<div>';
               $htmlBody .= '<p>Ne radi dugme? Prekopirajte sledeći link u vaš pretraživač:</p>';
               $htmlBody .= '<div><a href="' . Conf::get('url') . '/users/activate/' . $data->activation_token. '">' . Conf::get('url') . '/users/activate/' . $data->activation_token. '</a></div>';
            $htmlBody .= '</div>';
            $htmlBody .= '<p style="margin-top:20px">Srdačan pozdrav</p>';
         $htmlBody .= '</div>';
      $htmlBody .= '</div>';

      $altBody  = 'Aktivacija korisničkog naloga ' ."\r\n";
      $altBody .= 'Za aktivaciju Vašeg korisničkog naloga na e-mail adresi ' . $data->email . ' kliknite ili prekopirajte link u vaš pretraživač: ' . "\r\n";
      $altBody .=  Conf::get('url') . '/users/activate/' . $data->activation_token . " \r\n";
      $altBody .= 'Srdačan pozdrav';

      return array('html_body' => $htmlBody, 'alt_body' => $altBody);
   }


   public function resetPasswordTemplate($data) {

      $htmlBody  = '<div style="width:100%;height:100%;background:#123764;color:#404040;font-family:Arial,serif;">';
         $htmlBody .= '<div style="width:80%;height:100%;margin: auto;background:#ffffff;text-align:center;">';
            $htmlBody .= '<h2>Promena šifre</h2>';
            $htmlBody .= '<p>Za promenu šifre Vašeg korisničkog naloga na e-mail adresi ' . $data->email . ' kliknite na link:</p>';
            $htmlBody .= '<div><a href="' . Conf::get('url') . '/promena-sifre/' . $data->reset_password_token. '" style="display:inline-block;margin:6px 0;background:#fd0;color:#123764;padding:10px;text-decoration:none;">Promena šifre</a></div>';
            $htmlBody .= '<hr>';
            $htmlBody .= '<div>';
            $htmlBody .= '<p>Ne radi dugme? Prekopirajte sledeći link u vaš pretraživač:</p>';
            $htmlBody .= '<div><a href="' . Conf::get('url') . '/promena-sifre/' . $data->reset_password_token. '">' . Conf::get('url') . '/promena-sifre/' . $data->reset_password_token. '</a></div>';
            $htmlBody .= '</div>';
            $htmlBody .= '<p style="margin-top:20px">Srdačan pozdrav</p>';
         $htmlBody .= '</div>';
      $htmlBody .= '</div>';

      $altBody  = 'Promena šifre ' ."\r\n";
      $altBody .= 'Za promenu šifre Vašeg korisničkog naloga na e-mail adresi ' . $data->email . ' kliknite ili prekopirajte link u vaš pretraživač: ' . "\r\n";
      $altBody .=  Conf::get('url') . '/promena-sifre/' . $data->reset_password_token . " \r\n";
      $altBody .= 'Srdačan pozdrav';

      return array('html_body' => $htmlBody, 'alt_body' => $altBody);
   }


   public function contactFormTemplate($data) {

     $htmlBody = '';
     $altBody = '';

     if (@exists($data['name'])) {
       $htmlBody .= '<p>' . Trans::get('Ime') . ': ' . $data['name'] . '</p>';
       $altBody .= Trans::get('Ime') . ': ' . $data['name'] . "\r\n";
     }

     if (@exists($data['email'])) {
       $htmlBody .= '<p>' . Trans::get('Email') . ': ' . $data['email'] . '</p>';
       $altBody .= Trans::get('Email') . ': ' . $data['email'] . "\r\n";
     }

     if (@exists($data['phone'])) {
       $htmlBody .= '<p>' . Trans::get('Phone') . ': ' . $data['phone'] . '</p>';
       $altBody .= Trans::get('Phone') . ': ' . $data['phone'] . "\r\n";
     }

     if (@exists($data['message'])) {
       $htmlBody .= '<p>' . Trans::get('Message') . ': ' . $data['message'] . '</p>';
       $altBody .= Trans::get('Message') . ': ' . $data['message'] . "\r\n";
     }

     return array('html_body' => $htmlBody, 'alt_body' => $altBody);
   }


   public function commentFormTemplate($data) {

     $br = "\r\n";

     if ((int)$data['type_id'] === (int)Conf::get('comment_type_id')['article']) {
       $pageType = Trans::get('Article');
     }
     else if ((int)$data['type_id'] === (int)Conf::get('comment_type_id')['category']) {
       $pageType = Trans::get('Category');
     }

     $htmlBody  = '<h3>' . Trans::get('Comment added') . ':</h3>';
     $htmlBody .= '<p>' . Trans::get('Page type') . ': ' . $pageType . '</p>';
     $htmlBody .= '<p>' . Trans::get('Id') . ': ' . $data['item']->id . '</p>';
     $htmlBody .= '<p>' . Trans::get('Title') . ': ' . $data['item']->title . '</p>';
     $htmlBody .= '<p>' . Trans::get('Link') . ': ' . $data['item']->url . '</p>';

     $altBody  = Trans::get('Comment added') . ':' . "\r\n";
     $altBody .= Trans::get('Page type') . ': ' . $pageType . "\r\n";
     $altBody .= Trans::get('Id') . ': ' . $data['item']->id . "\r\n";
     $altBody .= Trans::get('Title') . ': ' . $data['item']->title . "\r\n";
     $altBody .= Trans::get('Link') . ': ' . $data['item']->url . "\r\n";

     if (@exists($data['name'])) {
       $htmlBody .= '<p>' . Trans::get('Ime') . ': ' . $data['name'] . '</p>';
       $altBody .= Trans::get('Ime') . ': ' . $data['name'] . "\r\n";
     }

     if (@exists($data['email'])) {
       $htmlBody .= '<p>' . Trans::get('Email') . ': ' . $data['email'] . '</p>';
       $altBody .= Trans::get('Email') . ': ' . $data['email'] . "\r\n";
     }

     if (@exists($data['message'])) {
       $htmlBody .= '<p>' . Trans::get('Message') . ': ' . $data['message'] . '</p>';
       $altBody .= Trans::get('Message') . ': ' . $data['message'] . "\r\n";
     }

     return array('html_body' => $htmlBody, 'alt_body' => $altBody);
   }


   public function newsletterCustomerTemplate() {

     $htmlBody = '<p>' . Trans::get('This e-mail is a confirmation of your subscription to the Newsletter.') . '</p>';
     $htmlBody .= '<p>' . Trans::get('Keep up with our promotions.') . '</p>';

     $altBody = Trans::get('This e-mail is a confirmation of your subscription to the Newsletter.') . "\r\n";
     $altBody .= Trans::get('Keep up with our promotions.') . "\r\n";

     return array('html_body' => $htmlBody, 'alt_body' => $altBody);
   }


   public function newsletterAdminTemplate($data) {

     $htmlBody = '<p>' . Trans::get('New newsletter sign up.') . '</p>';
     $htmlBody .= '<p>' . Trans::get('Sign up info') . ':</p>';

     $altBody = Trans::get('New newsletter sign up.') . "\r\n";
     $altBody .= Trans::get('Sign up info') . ': ' . "\r\n";

     if (@exists($data['email'])) {
       $htmlBody .= '<p>' . Trans::get('Email') . ': ' . $data['email'] . '</p>';
       $altBody .= Trans::get('Email') . ': ' . $data['email'] . "\r\n";
     }

     return array('html_body' => $htmlBody, 'alt_body' => $altBody);
   }
}
?>