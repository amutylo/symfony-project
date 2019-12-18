<?php

namespace App\Security;


class TokenGenerator
{

  private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

  public function geRandomSecureToken(int $length = 30): string
  {
    /** Generated token to return */
    $token = '';

    /**
     * Maximum number for random integer.
     */
    $maxNumber = strlen(self::ALPHABET);

    for ($i = 0; $i < $length; $i++) {
      $token .= self::ALPHABET[random_int(0, $maxNumber - 1)];
    }

    return $token;
  }
}