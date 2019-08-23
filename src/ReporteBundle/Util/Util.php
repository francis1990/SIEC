<?php
/**
 * Created by PhpStorm.
 * User: edilio.escalona
 * Date: 12/22/2017
 * Time: 1:21 PM
 */

namespace ReporteBundle\Util;


class Util
{
    /**
     * Extraido de http://ecapy.com/reemplazar-la-n-acentos-espacios-y-caracteres-especiales-con-php-actualizada/
     * Reemplaza todos los acentos por sus equivalentes sin ellos
     *
     * @param $string
     *  string la cadena a sanear
     *
     * @return $string
     *  string saneada
     */
    static public function eliminar_simbolos($string)
    {

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        $string = str_replace(
            array("\\", "¨", "º", "-", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                ".", " "),
            ' ',
            $string
        );
        return $string;
    }

    public function convertfecha($fecha)
    {
        $m = date("n", strtotime(urldecode($fecha)));
        $d = date("j", strtotime(urldecode($fecha)));
        $a = date("Y", strtotime(urldecode($fecha)));
        return array('m' => $m, 'd' => $d, 'a' => $a);
    }

    static public function existePadre($a, $b)
    {
        $result = false;
        foreach ($b as $var) {
            if (in_array($var, $a)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

}