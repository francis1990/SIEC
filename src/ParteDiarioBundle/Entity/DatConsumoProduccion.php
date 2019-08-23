<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 03/02/2017
 * Time: 12:28
 */

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use ParteDiarioBundle\Entity\DatPartediarioProduccion;
use NomencladorBundle\Entity\NomNorma;
use NomencladorBundle\Entity\NomAseguramiento;
use NomencladorBundle\Entity\NomUnidadmedida;

/**
 * DatConsumoProduccion
 *
 * @ORM\Table(name="dat_parteproduccion_consumo")
 * @ORM\Entity
 */
class DatConsumoProduccion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idconsumo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idconsumo;

    /**
     * @var DatPartediarioProduccion
     *
     * @ORM\ManyToOne(targetEntity="\ParteDiarioBundle\Entity\DatPartediarioProduccion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parte", referencedColumnName="idparte", onDelete="cascade")
     * })
     */
    private $parte;

    /**
     * @var NomNorma
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomNorma")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="norma", referencedColumnName="idnorma")
     * })
     */
    private $norma;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float",  nullable=true)
     */
    private $cantidad;

    /**
     * Get idconsumo
     *
     * @return integer
     */
    public function getIdconsumo()
    {
        return $this->idconsumo;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return DatConsumoProduccion
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set parte
     *
     * @param \ParteDiarioBundle\Entity\DatPartediarioProduccion $parte
     * @return DatConsumoProduccion
     */
    public function setParte(\ParteDiarioBundle\Entity\DatPartediarioProduccion $parte = null)
    {
        $this->parte = $parte;
        return $this;
    }

    /**
     * Get parte
     *
     * @return \ParteDiarioBundle\Entity\DatPartediarioProduccion
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * Set norma
     *
     * @param \NomencladorBundle\Entity\NomNorma $norma
     * @return DatConsumoProduccion
     */
    public function setNorma(\NomencladorBundle\Entity\NomNorma $norma = null)
    {
        $this->norma = $norma;

        return $this;
    }

    /**
     * Get norma
     *
     * @return \NomencladorBundle\Entity\NomNorma
     */
    public function getNorma()
    {
        return $this->norma;
    }
}
