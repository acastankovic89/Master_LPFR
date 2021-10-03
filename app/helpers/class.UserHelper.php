<?php

class UserHelper extends Helper {

  private $registrationRequiredFields = array('email', 'password', 'repeated_password', 'first_name', 'last_name', 'phone', 'terms_accepted');

  public function __construct() {
    parent::__construct();
  }

  public function validateRegistration($user, $params) {

    if (!@exists($params)) {
      return Errors::EMPTY_FIELDS;
    }

    if (!Helper::validateRequiredFields($params, $this->registrationRequiredFields)) {
      return Errors::EMPTY_FIELDS;
    }

    if (!Util::validateEmail($user->email)) {
      return Errors::INVALID_EMAIL;
    }

    if ($user->password !== $user->repeatedPassword) {
      return Errors::NOT_MATCHING_PASSWORDS;
    }

    if (strlen($user->password) < 6) {
      return Errors::INVALID_PASSWORD;
    }

//      if(!$user->termsAccepted) {
//         return Errors::TERMS_NOT_ACCEPTED;
//      }

    //todo: validate phone

    return Errors::OK;
  }

  public function validatePassword($user) {

    if (strlen($user->password) < 6) {
      return Errors::INVALID_PASSWORD;
    }
    return Errors::OK;
  }

  public function activationTokenExists($user) {
    return @exists($user->id);
    // return @exists($user) && (bool)$user !== false;
  }

  public function active($user) {
    return (int)$user->active === 1;
  }

  public function activationTokenExpired($user) {

    $tokenTime = $user->activationTokenTime;
    $currentTime = date('Y-m-d H:i:s');

    $timeDiff = strtotime($currentTime) - strtotime($tokenTime);

    return $timeDiff > (int)Conf::get('activation_token_lifetime');
  }

  public function validateResetPasswordParams($user) {

    if (!@exists($user->resetPasswordToken)) {
      return Errors::RESET_PASSWORD_TOKEN_DOESNT_EXIST;
    }

    if (!@exists($user->password)) {
      return Errors::PASSWORD_AND_REPEATED_PASSWORD_REQUIRED;
    }

    if (!@exists($user->repeatedPassword)) {
      return Errors::PASSWORD_AND_REPEATED_PASSWORD_REQUIRED;
    }

    if ($user->password !== $user->repeatedPassword) {
      return Errors::NOT_MATCHING_PASSWORDS;
    }

    if (strlen($user->password) < 6) {
      return Errors::INVALID_PASSWORD;
    }

    return Errors::OK;
  }

  public function validateResetPassword($user, $params) {

    if (!@exists($user->id)) {
      return Errors::RESET_PASSWORD_TOKEN_DOESNT_EXIST;
    }

    if ($this->resetPasswordTokenExpired($user)) {
      return Errors::RESET_PASSWORD_TOKEN_EXPIRED;
    }

    if ($user->password === Encryption::encode($params['password'])) {
      return Errors::NEW_PASSWORD_SAME_AS_OLD;
    }

    return Errors::OK;
  }

  public function resetPasswordTokenExpired($user) {

    $tokenTime = $user->resetPasswordTokenTime;
    $currentTime = date('Y-m-d H:i:s');

    $timeDiff = strtotime($currentTime) - strtotime($tokenTime);

    return $timeDiff > (int)Conf::get('reset_password_token_lifetime');
  }

  public function validatePasswordChange($user, $params) {

    if(!@exists($params) || !@exists($params['current_password']) || !@exists($params['new_password']) || !@exists($params['repeat_new_password'])) {
       return Errors::EMPTY_FIELDS;
    }

    $password = $params['current_password'];
    $newPassword = $params['new_password'];
    $repeatedNewPassword = $params['repeat_new_password'];

    if((string)$user->password !== (string)Encryption::encode($password)) {
       return Errors::INVALID_CURRENT_PASSWORD;
    }

    if($newPassword !== $repeatedNewPassword) {
       return Errors::NOT_MATCHING_PASSWORDS;
    }

    if((string)$user->password === (string)Encryption::encode($newPassword)) {
       return Errors::NEW_PASSWORD_SAME_AS_OLD;
    }

    if(strlen($newPassword) < 6) {
       return Errors::INVALID_PASSWORD;
    }

    return Errors::OK;
 }

}

?>