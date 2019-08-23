<?php

namespace EnumsBundle\Entity;


class EnumMeses
{
    private $meses = array(
        "01" => 'Enero',
        "02" => 'Febrero',
        "03" => 'Marzo',
        "04" => 'Abril',
        "05" => 'Mayo',
        "06" => 'Junio',
        "07" => 'Julio',
        "08" => 'Agosto',
        "09" => 'Septiembre',
        "10" => 'Octubre',
        "11" => 'Noviembre',
        "12" => 'Diciembre'
    );

    public function getMeses()
    {
        return $this->meses;
    }

    public function MesToString($meses)
    {
        return $this->meses[$meses];
    }

    public function obtenerMesDadoIndice($indice)
    {
        $nombre = $this->meses[$indice];
        return $nombre;
    }

    public function obtenerIdMesDadoNombre($name)
    {
        $ids = "";
        $ids = array_keys($this->meses, $name);
        return $ids;
    }

    static public function diasdelMes($fecha)
    {
        /*OJOOOOO ---- Cuando se pasa en 8 dias de la fecha actual no devuelve los valores (dia,mes,año) incorrectamente*/
        $fechaformato = self::convertfecha($fecha);
        $month = $fechaformato['m'];
        $year = $fechaformato['a'];
        return date("t", mktime(0, 0, 0, $month, 1, $year));
    }

    static public function convertfecha($fecha)
    {
        if (stripos($fecha, '-') != false) {

            $fechaDiv = explode('-', $fecha);
            if ($fechaDiv[0][0] == "0") {
                $a = $fechaDiv[0][1];
            } else {
                $a = $fechaDiv[0];
            }

            if ($fechaDiv[1][0] == "0") {
                $m = $fechaDiv[1][1];
            } else {
                $m = $fechaDiv[1];
            }

            $d = $fechaDiv[2];

        } else if (stripos($fecha, '/') != false) {
            $fechaDiv = explode('/', $fecha);
            if ($fechaDiv[0][0] == "0") {
                $d = $fechaDiv[0][1];
            } else {
                $d = $fechaDiv[0];
            }

            if ($fechaDiv[1][0] == "0") {
                $m = $fechaDiv[1][1];
            } else {
                $m = $fechaDiv[1];
            }

            $a = $fechaDiv[2];
        }
        if ($m < 10) {
            $m = '0' . $m;
        }
        return array('m' => $m, 'd' => $d, 'a' => $a);
    }

    static public function FormatoFecha($fecha, $formato)
    {
        $fecha = date_format($fecha, $formato);
        return $fecha;
    }

    static public function convertirFechaParaBD($fecha)
    {
        if ($fecha == 0 || $fecha == "") {
            $result = date_format(date('Y-m-d'), 'Y-m-d');
        } else {
            if (is_string($fecha)) {
                $result = date_format(date_create_from_format('d/m/Y', $fecha), 'Y-m-d');
            } else {
                $date = new \DateTime($fecha);
                $result = $date->format('Y-m-d');
            }
        }
        return $result;
    }

    static public function convertirFechaParaBDConTime($fecha)
    {
        if ($fecha == 0) {
            $result = date_create_from_format('Y-m-d', date('Y-m-d'));
        } else {
            $result = date_create_from_format('d/m/Y', $fecha);
        }
        return $result;
    }

    static public function primerDiaAno($fecha)
    {
        $fechaEx = explode('-', $fecha);
        $fechaInicAno = $fechaEx[0] . '-01-01';
        return $fechaInicAno;
    }

    static public function primerDiaMes($fecha)
    {
        $fechaEx = explode('-', $fecha);
        $fechaInicMes = $fechaEx[0] . '-' . $fechaEx[1] . '01';
        return $fechaInicMes;
    }

    static public function intervaloMes($fecha, $mes =null)
    {
        $dia = self::diasdelMes($fecha);
        $fec = substr($fecha, 0, 8);
        $fechafinMes = $fec . $dia;
        $fechainiMes = $fec . '01';
        return array('inicio' => $fechainiMes, 'fin' => $fechafinMes);
    }

    static public function mesFecha($fecha)
    {
        $fechaformato = self::convertfecha($fecha);
        $month = $fechaformato['m'];
        $year = $fechaformato['a'];
        return date("m", mktime(0, 0, 0, $month, 1, $year));
    }
}
