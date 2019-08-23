<?php

namespace EnumsBundle\Entity;


use NomencladorBundle\Util\Util;

class EnumTipoUnidadMedida
{
    private $tiposUM = array(
        1 => "Longitud", "Superficie", "Volumen", "Masa", "Tiempo", "Cuenta", "Energía","Pesos","Energético","Líquido","Cantidad"
    );

    public function getVolumen()
    {
        return $this->tiposUM;
    }

    public function tiposUmToString($tiposUM)
    {
        return $this->tiposUM[$tiposUM];
    }

    public function obtenerIdUMDadoName($name)
    {
        $alias = Util::getSlug($name);
        $ids = "";
        $idsAux = array();
        foreach ($this->tiposUM as $index => $value) {
            $pos = strpos(strtolower($value), strtolower($alias));
            if ($pos !== false) {
                $idsAux[] = $index;
            }
        }
        $ids = implode(",", $idsAux);
        return $ids;
    }
}
