<?php

namespace App\Pix;

class ValidationCodePix
{
    const ID_CRC16 = '63';

     /**
     * Method responsible for calculating the value of the validation hash of the pix code
     * @return string
     */
    public static function getCRC16($payload) {
        //ADD GENERAL DATA IN PAYLOAD
        $payload .= self::ID_CRC16.'04';

        //DATA DEFINED BY THE BACEN
        $polinomio = 0x1021;
        $resultado = 0xFFFF;

        //CHECKSUM
        if (($length = mb_strlen($payload)) > 0) {
            for ($offset = 0; $offset < $length; $offset++) {
                $resultado ^= (ord($payload[$offset]) << 8);
                for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                    if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                    $resultado &= 0xFFFF;
                }
            }
        }

        //RETURNS 4 CHARACTER CRC16 CODE
        return self::ID_CRC16.'04'.strtoupper(dechex($resultado));
    }
}