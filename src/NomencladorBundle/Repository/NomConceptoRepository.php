<?php

namespace NomencladorBundle\Repository;

use Doctrine\ORM\EntityRepository;
use EnumsBundle\Entity\EnumAreas;
use NomencladorBundle\Util\Util;

/**
 * NomConceptoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NomConceptoRepository extends EntityRepository
{
    public function listarDelExportarConcepto($codigo, $nombre)
    {
        $em = $this->getEntityManager();
        $datas = Util::whereFiltrar($em, 'NomencladorBundle:NomConcepto', $codigo, $nombre);
        $data = [];

        foreach ($datas as $n) {
            $data[] = array(
                'codigo' => $n->getCodigo(),
                'nombre' => $n->getNombre(),
                'entrada' => !$n->getTipo() ? 'Si' : 'No',
                'salida' => $n->getTipo() ? 'Si' : 'No',
                'activo' => $n->getActivo() == true ? 'Si' : 'No');
        }

        return $data;

    }
}
