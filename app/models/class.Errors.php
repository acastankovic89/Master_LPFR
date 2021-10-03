<?php

final class Errors {

  const OK = 0;
  const INVALID_EMAIL = 100;
  const INVALID_PHONE = 101;
  const INVALID_PASSWORD = 102;
  const EMAIL_EXISTS = 103;
  const EMAIL_DOESNT_EXIST = 104;
  const INVALID_KEY = 105;
  const EMPTY_FIELDS = 106;
  const NOT_MATCHING_PASSWORDS = 107;
  const TERMS_NOT_ACCEPTED = 108;
  const ACTIVATION_TOKEN_DOESNT_EXIST = 109;
  const ACTIVATION_TOKEN_EXPIRED = 110;
  const INVALID_CURRENT_PASSWORD = 111;
  const NEW_PASSWORD_SAME_AS_OLD = 112;
  const EMAIL_REQUIRED = 113;
  const RESET_PASSWORD_TOKEN_DOESNT_EXIST = 114;
  const RESET_PASSWORD_TOKEN_EXPIRED = 115;
  const USER_NOT_ACTIVATED = 116;
  const INVALID_LOGIN = 117;
  const ALREADY_ACTIVATED = 118;
  const EMAIL_EXISTS_NOT_ACTIVATED = 119;
  const USER_ALREADY_ACTIVATED = 120;
  const PASSWORD_AND_REPEATED_PASSWORD_REQUIRED = 121;
  const ITEM_DOESNT_EXIST = 122;
  const TYPE_MISSING = 123;
  const EMAIL_NOT_SENT = 124;

  const MESSAGES = array(
    self::OK => 'Operacija je uspešno izvedena',
    self::INVALID_EMAIL => 'Email adresa nije u odgovarajućum formatu',
    self::INVALID_PHONE => 'Broj telefona nije u odgovarajućum formatu',
    self::INVALID_PASSWORD => 'Slaba lozinka (manje od 6 cifara)',
    self::EMAIL_EXISTS => 'Korisnik sa ovom email adresom već postoji',
    self::EMAIL_DOESNT_EXIST => 'Korisnik sa ovom email adresom ne postoji',
    self::INVALID_KEY => 'Token za resetovanje lozinke nije validan',
    self::EMPTY_FIELDS => 'Obavezna polja nisu popunjena',
    self::NOT_MATCHING_PASSWORDS => 'Lozinke nisu iste',
    self::TERMS_NOT_ACCEPTED => 'Morate prihvatiti uslove korišćenja',
    self::ACTIVATION_TOKEN_DOESNT_EXIST => 'Token za aktivaciju ne postoji',
    self::ACTIVATION_TOKEN_EXPIRED => 'Token za aktivaciju je istekao',
    self::EMAIL_REQUIRED => 'Email adresa je obavezna',
    self::INVALID_CURRENT_PASSWORD => 'Trenutna lozinka nije ispravna',
    self::NEW_PASSWORD_SAME_AS_OLD => 'Nova lozinka ne sme biti ista kao trenutna',
    self::USER_NOT_ACTIVATED => 'Niste kliknuli na aktivacioni link koji vam je poslat na mejl. Molimo vas proverite Inbox i Spam folder.',
    self::INVALID_LOGIN => 'Email ili šifra nisu ispravni',
    self::ALREADY_ACTIVATED => 'Profil sa ovom email adresom je već aktiviran',
    self::EMAIL_EXISTS_NOT_ACTIVATED => 'Korisnik sa ovom email adresom već postoji, ali profil nije aktiviran. Kod za aktivaciju profila će vam biti poslat na emal.',
    self::USER_ALREADY_ACTIVATED => 'Profil je već aktiviran.',
    self::RESET_PASSWORD_TOKEN_DOESNT_EXIST => 'Token za reset lozinke ne postoji',
    self::PASSWORD_AND_REPEATED_PASSWORD_REQUIRED => 'Nova i ponovljena lozinka su obavezne',
    self::RESET_PASSWORD_TOKEN_EXPIRED => 'Token za reset lozinke je istekao',
    self::ITEM_DOESNT_EXIST => 'Item doesn\'t exist',
    self::TYPE_MISSING => 'Type missing'
  );

  static function message($code) {

    if (isset(self::MESSAGES[$code])) return self::MESSAGES[$code];
    return 'Nepoznata greška, kod ' . $code;
  }

  static function getResponseStatus($code) {
        
    $response = new stdClass();
    $response->status = $code;
    $response->message = self::Message($code);
    return $response;
  }
}


?>
