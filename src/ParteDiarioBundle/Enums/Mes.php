<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 05/10/2016
 * Time: 12:15 PM
 */

namespace ParteDiarioBundle\Enums;

class Mes
{
    private $meses = array(
        1 => "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    );

    private $celdas = array(
        1 => "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M"
    );

    public function getMeses()
    {
        return $this->meses;
    }

    public function mesToString($mes)
    {
        return $this->meses[$mes];
    }

    public function getCelda($index)
    {
        return $this->celdas[$index];
    }

    private $enero = 31;
    private $febrero = 28;
    private $marzo = 31;
    private $abril = 30;
    private $mayo = 31;
    private $junio = 30;
    private $julio = 31;
    private $agosto = 31;
    private $septiembre = 30;
    private $octubre = 31;
    private $noviembre = 30;
    private $diciembre = 31;

    public function getAbril()
    {
        return $this->abril;
    }

    public function getAgosto()
    {
        return $this->agosto;
    }

    public function getEnero()
    {
        return $this->enero;
    }

    public function getFebrero()
    {
        return $this->febrero;
    }

    public function getDiciembre()
    {
        return $this->diciembre;
    }

    public function getMarzo()
    {
        return $this->marzo;
    }

    public function getMayo()
    {
        return $this->mayo;
    }

    public function getJulio()
    {
        return $this->julio;
    }

    public function getJunio()
    {
        return $this->junio;
    }

    public function getSeptiembre()
    {
        return $this->septiembre;
    }

    public function getOctubre()
    {
        return $this->octubre;
    }

    public function getNoviembre()
    {
        return $this->noviembre;
    }
}