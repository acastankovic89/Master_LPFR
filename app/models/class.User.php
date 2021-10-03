<?php

final class UserRoles {
  const ADMIN = 'admin'; // administration access
  const USER = 'user'; // registered user access
}

final class UserRoleIds {
  const ADMIN = 1; // administration access
  const USER = 2; // registered user access
}

class User extends Model {

  public $id;
  public $firstName;
  public $lastName;
  public $password;
  public $repeatedPassword;
  public $email;
  public $address;
  public $phone;
  public $image;
  public $scope;
  public $active;
  public $activationToken;
  public $activationTokenTime;
  public $resetPasswordToken;
  public $resetPasswordTokenTime;

  public function __construct() {
    parent::__construct();
    $this->setTable('users');
  }

  public function map($data) {
    if (is_array($data)) $data = (object)$data;
    if (@exists($data->id)) $this->id = (int)$data->id;
    if (@exists($data->first_name)) $this->firstName = (string)$data->first_name;
    if (@exists($data->last_name)) $this->lastName = (string)$data->last_name;
    if (@exists($data->password)) $this->password = (string)$data->password;
    if (@exists($data->repeated_password)) $this->repeatedPassword = (string)$data->repeated_password;
    if (@exists($data->email)) $this->email = (string)$data->email;
    if (@exists($data->address)) $this->address = (string)$data->address;
    if (@exists($data->phone)) $this->phone = (string)$data->phone;
    if (@exists($data->image)) $this->image = (string)$data->image;
    if (@exists($data->scope)) $this->scope = $data->scope;
    if (@exists($data->active)) $this->active = (bool)$data->active;
    if (@exists($data->activation_token)) $this->activationToken = (string)$data->activation_token;
    if (@exists($data->activation_token_time)) $this->activationTokenTime = $data->activation_token_time;
    if (@exists($data->reset_password_token)) $this->resetPasswordToken = (string)$data->reset_password_token;
    if (@exists($data->reset_password_token_time)) $this->resetPasswordTokenTime = $data->reset_password_token_time;
  }

  public function save() {
    if ($this->id == null) $this->insert($this);
    else $this->update($this);
  }

  public function exists() {

    $result = $this->loadByEmail();
    return @exists($result) && $result != false;
  }

  public function loadAndMap($id) {

    $sql = "SELECT * FROM `users` WHERE `id` = :id;";
    $data = $this->exafe($sql, array("id" => $id));
    $this->map($data);
    return $data;
  }

  public function loadByEmail() {

    $sql = "SELECT * FROM `users` WHERE `email` = :email;";
    $data = $this->exafe($sql, array("email" => $this->email));
    $this->map($data);
    return $data;
  }

  public function loadByActivationToken() {

    $sql = "SELECT * FROM `users` WHERE `activation_token` = :token;";
    $data = $this->exafe($sql, array("token" => $this->activationToken));
    $this->map($data);
    return $data;
  }

  public function loadByResetPasswordToken() {

    $sql = "SELECT * FROM `users` WHERE `reset_password_token` = :token;";
    $data = $this->exafe($sql, array("token" => $this->resetPasswordToken));
    $this->map($data);
    return $data;
  }

  public function loadByAccessToken($token) {

    $sql = "SELECT * FROM `users` u LEFT JOIN `oauth_access_tokens` t ON u.`username` = t.`user_id` WHERE t.`access_token` = :token;";
    $data = $this->exafe($sql, array("token" => $token));
    $this->map($data);
    return $data;
  }


  /*********************************** ACTIONS ***********************************/

  public function insert($user) {
    $user->first_name = $this->firstName;
    $user->last_name = $this->lastName;
    $user->username = $this->email;
    $user->active = 0;
    $user->activation_token = Encryption::generateStampWithString($this->email, 8);
    $user->activation_token_time = date("Y-m-d H:i:s");
    $user->password = Encryption::encode($this->password);
    $user->scope = @exists($this->scope) ? $this->scope : 'user';
    parent::insert($user);
  }

  public function activate($token) {

    $sql = "UPDATE `users` SET `active` = 1, `activation_token` = NULL, `activation_token_time` = NULL  WHERE `activation_token` = :token;";
    return $this->execute($sql, array("token" => $token));
  }

  public function setResetPasswordToken($user) {

    $user->reset_password_token = Encryption::generateStampWithString($user->email, 8);
    $user->reset_password_token_time = date("Y-m-d H:i:s");
    parent::update($user);
  }

  public function resetPassword($token, $password) {

    $password = Encryption::encode($password);

    $sql = "UPDATE `users` SET `password` = :password, `reset_password_token` = NULL, `reset_password_token_time` = NULL WHERE `reset_password_token` = :token;";
    return $this->execute($sql, array("token" => $token, "password" => $password));
  }

  public function setNewPassword($id, $newPassword) {

    $password = Encryption::encode($newPassword);
    $sql = 'UPDATE `users` SET `password` = :password WHERE `id` = :id;';
    $this->execute($sql, array('password' => $password, 'id' => $id));
 }

  public function hasScope($scope) {
    if (!isset($this->scope)) return false;
    $scopes = explode(' ', $this->scope);
    foreach ($scopes as $sc) {
      if (strcmp($sc, $scope) === 0) return true;
    }
    return false;
  }

  public function scopeExist($scope) {
    $scopes = explode(" ", $this->scope);
    foreach ($scopes as $sc) {
      if (strcmp($scope, $sc) == 0) return true;
    }
    return false;
  }

}
?>