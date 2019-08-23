<?php

namespace EnumsBundle\Entity;


class EnumLetras
{
    private $letras = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'
    , 'X', 'Y', 'Z');


    public function getLetras()
    {
        return $this->letras;
    }

    public function letraToString($letras)
    {
        return $this->letras[$letras];
    }

    public function obtenerLetraDadoIndice($indice)
    {
        $ids = "";
        $ids = array_key_exists($indice, $this->letras);
        return $ids;
    }

    public function obtenerIdLetraDadoNombre($name)
    {
        $ids = "";
        $ids = array_keys($this->letras, $name);
        return $ids;
    }

    public function letraExcel($index, $resp = '')
    {
        $cociente = intval($index / 26);
        $resto = $index % 26;
        if ($index <= 25 && $resp == '')
            return $this->letras[$resto];
        if ($resto != 0) {
            $resto--;
        }
        if ($cociente == 0) {
            return $this->letras[$resto] . $resp;
        } else {

            $resp = $resp . $this->letras[$resto];
            return self::letraExcel($cociente, $resp);
        }
    }

}
