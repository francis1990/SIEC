<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 30/03/2016
 * Time: 11:48
 */

namespace AdminBundle\Services;

use NomBundle\Entity\Entidad;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;

class EntidadService
{
    private $em;

    /**
     * @param $config_path: direccion del archivo de configuracion
     */
    public function __construct(EntityManager $em){
        $this->em = $em;
    }


    /**
     * Genera un arbol en formato JSON con las entidades
     * @param Entidad $entidad
     */
    public function createJSONTreePruebas(Entidad $entidad){
        $data[] = $this->buildBranch($entidad);
        return json_encode($data);
    }

    /**
     * Construye una rama del árbol que empieza por la entidad(raíz)
     * pasada por parametro.
     *
     * @param Entidad $entidad
     * @param  Doctrine\ORM\PersistentCollection $entidadesHijas
     * @return array
     */
    private function buildBranch(Entidad $entidad){
        $data = array(
            'id' => $entidad->getId(),
            'text' => $entidad->getDescEntidad(),
            'icon' => "fa fa-sitemap"
        );

        $entidadesHijas = $entidad->getEntidadesHijas();
        foreach($entidadesHijas as $entidadHija){
            $data['children'][] = $this->buildBranch($entidadHija);
        }

        return $data;
    }
}