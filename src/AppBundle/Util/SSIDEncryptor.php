<?php
/**
 * Created by PhpStorm.
 * User: Lahiru
 * Date: 08/01/2016
 * Time: 12:23 AM
 */

namespace AppBundle\Util;

class SSIDEncryptor
{
    public static function encode($ssid) {
        $upper=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
        $lower=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
        $symbols=array("*","#","$","-","@");   // Not allowed , " : ; \ & % + ' < > ?
        $numbers=array(0,1,2,3,4,5,6,7,8,9);


        $encode = $ssid;

        while ( strlen( $encode ) < 10 ) {
            $encode =$encode. $ssid;
        }
        $encode = substr( $encode, 0, 10 );

        $encoded_str = '';

        $val = ord( $encode[0] );
        $encoded_str=$encoded_str. $lower[$val%26];

        $val = ord( $encode[1] );
        $encoded_str=$encoded_str. $numbers[$val%10];

        $val = ord( $encode[2] );
        $encoded_str=$encoded_str. $numbers[$val%10];

        $val = ord( $encode[3] );
        $encoded_str= $encoded_str.$symbols[$val%5];

        $val = ord( $encode[4] );
        $encoded_str=$encoded_str. $lower[$val%26];

        $val = ord( $encode[5] );
        $encoded_str=$encoded_str. $upper[$val%26];

        $val = ord( $encode[6] );
        $encoded_str=$encoded_str.$lower[$val%26];

        $val = ord( $encode[7] );
        $encoded_str=$encoded_str. $upper[$val%26];

        $val = ord( $encode[8] );
        $encoded_str=$encoded_str. $numbers[$val%10];


        $val = ord( $encode[9] );
        $encoded_str= $encoded_str.$symbols[$val%5];

        return $encoded_str;


    }
}