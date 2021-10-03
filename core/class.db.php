<?php

//////////////////////////////////////////////////
// 
// Project: Norma Core
// Company: Normasoft
// Author: Milos Pavlovic
// Email: milos.pavlovic@normasoft.net 
// Date: Spt 22, 2015
// Db connections
//
//////////////////////////////////////////////////


class DB {

  private static $host;
  private static $user;
  private static $pass;
  private static $dbname;

  private static $dbh = null;

  private static $instance;

  private function __construct() {

  }

  //singleton construct
  public static function Instance() {

    if (!isset(self::$instance)) {
      $c = __CLASS__;
      self::$instance = new $c;
    }

    return self::$instance;
  }

  public static function Connect() {

    if (!isset($_SESSION)) {
      session_start();
    }

    self::$host = Conf::get('db_hostname');
    self::$user = Conf::get('db_username');
    self::$pass = Conf::get('db_password');
    self::$dbname = Conf::get('db_name');

    if (!isset(self::$dbh)) {
      try {
        //connect
        $dbh = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$dbname, self::$user, self::$pass);
        //set error mode
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //set connection coding page
        $dbh->exec('set names "utf8"');
        self::$dbh = $dbh;
        return $dbh;
      }
      catch (PDOException $e) {
        // log error
        Logger::putError($e);
        return null;
      }
    } else return self::$dbh;
  }

  public static function reconnect() {

    self::$host = Conf::get('db_hostname');
    self::$user = Conf::get('db_username');
    self::$pass = Conf::get('db_password');
    self::$dbname = Conf::get('db_name');

    try {
      //connect
      $dbh = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$dbname, self::$user, self::$pass);
      //set error mode
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      //set connection coding page
      $dbh->exec('set names "utf8"');
      self::$dbh = $dbh;
      return $dbh;
    } catch (PDOException $e) {
      //log error
      Logger::putError($e);
      return null;
    }
  }

  public static function closeConnection() {
    self::$dbh = null;
  }

  public function GetDBH() {
    return self::$dbh;
  }

}
?>