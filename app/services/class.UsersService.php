<?php

class UsersService extends Service {

    private $userHelper;
    private $userModel;

    public function __construct() {
      $this->userHelper = new UserHelper();
      $userModel = User::Instance();
      if ($userModel instanceof User) {
        $this->userModel = $userModel;
      }
    }

    public function register(User $user, $params) {

       $validationResponse = $this->userHelper->validateRegistration($user, $params);

       // validate params
       if (!$validationResponse == Errors::OK) {
          return $validationResponse;
       }

       // user with this email exists
       if($user->exists()) {

          // if exists and not activated, send email with new token, and reset token in db
          if((int)$user->active !== 1) {

             $this->sendRegistrationEmail($user);
             return Errors::EMAIL_EXISTS_NOT_ACTIVATED;
          }
          return Errors::EMAIL_EXISTS;
       }

       // insert user into db
       $user->insert($user);

       $this->sendRegistrationEmail($user);

       return Errors::OK;
   }

   public function activate(User $user) {

      $user->loadByActivationToken();

      if(!$this->userHelper->activationTokenExists($user)) {
         return Errors::ACTIVATION_TOKEN_DOESNT_EXIST;
      }

      if($this->userHelper->active($user)) {
         return Errors::USER_ALREADY_ACTIVATED;
      }

      if($this->userHelper->activationTokenExpired($user)) {
         return Errors::ACTIVATION_TOKEN_EXPIRED;
      }

      $user->activate($user->activationToken);

      return Errors::OK;
   }

   // send reset password email
   public function sendResetPassword(User $user) {

      if(!@exists($user->email)) {
         return Errors::EMAIL_REQUIRED;
      }

      if(!Util::validateEmail($user->email)) {
         return Errors::INVALID_EMAIL;
      }

      $user->loadByEmail();

      if (!$user->id) {
         return Errors::EMAIL_DOESNT_EXIST;
      }

      if(!$user->active) {
         return Errors::USER_NOT_ACTIVATED;
      }

      $user->setResetPasswordToken($user);

      $this->sendResetPasswordEmail($user);

      return Errors::OK;
   }

   public function resetPassword(User $user, $params) {

      $validationParamsResponse = $this->userHelper->validateResetPasswordParams($user);

      // checking entered params
      if (!$validationParamsResponse == Errors::OK) {
         return $validationParamsResponse;
      }

      // checking db data
      $user->loadByResetPasswordToken();

      $validationResponse = $this->userHelper->validateResetPassword($user, $params);

      if (!$validationResponse == Errors::OK) {
         return $validationResponse;
      }

      $user->resetPassword($params['reset_password_token'], $params['password']);

      return Errors::OK;
   }

   public function setNewPassword(User $user, $params) {

      $result = $this->userHelper->validatePasswordChange($user, $params);

      if ($result == Errors::OK) {
          $newPassword = $params['new_password'];
          $user->setNewPassword($user->id, $newPassword);
          return Errors::OK;
      }
      else return $result;
  }

    /**************** EMAILS ****************/

    public function sendRegistrationEmail(User $user) {

        $data = $user->loadByEmail();

        $emailsService = new EmailsService();
        $emailData = $emailsService->accountActivationData($data);

        $mailer = new Mailer();
        $mailer->sendMail($emailData);
    }

    public function sendResetPasswordEmail(User $user) {

        $data = $user->loadByEmail();

        $emailsService = new EmailsService();
        $emailData = $emailsService->resetPasswordData($data);

        $mailer = new Mailer();
        $mailer->sendMail($emailData);
    }
}
?>