<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 2/14/2018
 * Time: 6:53 PM
 */

namespace EnumsBundle\Entity;

use ReporteBundle\Util\Util;

class EnumAreas
{
    private $areas = array(1 => 'Acopio', 'Aseguramiento', 'Economía', 'Energético', 'Portadores', 'Producción', 'Transporte', 'Venta');


    public function getAreas()
    {
        return $this->areas;
    }

    public function getAreaById($idarea)
    {
        $area = $this->areas[$idarea];
        return $area;
    }

    public function obtenerIdsAreasDadoName($name)
    {
        $ids = "";
        $idsAux = array();
        foreach ($this->areas as $index => $value) {
            $pos = strpos(strtolower($value), strtolower($name));
            if ($pos !== false) {
                $idsAux[] = $index;
            }
        }
        $ids = implode(",", $idsAux);
        return $ids;
    }

}