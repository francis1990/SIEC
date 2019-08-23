<?php

namespace NomencladorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use NomencladorBundle\Util\Util;

/**
 * NomTipoNormaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NomTipoNormaRepository extends EntityRepository
{

    public function listarDelExportarTipoNorma($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomTipoNorma', $codigo, $nombre);
        $data = [];

        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'activo' => $n->getActivo() == true ? 'Si' : 'No');
        }

        return $data;

    }

}
