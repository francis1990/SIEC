<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomTipoNorma;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DatParteDiarioConsAseg
 *
 * @ORM\Table(name="dat_parte_consaseg")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteDiarioConsAsegRepository")
 * @UniqueEntity(fields= {"fecha", "ueb", "producto","tiponorma"}, message="Existe un elemento con fecha, UEB, tipo norma y producto seleccionado.")
 */
class DatParteDiarioConsAseg extends DatParte
{

    /**
     * @var NomProducto
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto", referencedColumnName="idproducto")
     * })
     */
    private $producto;

    /**
     * @ORM\OneToMany(targetEntity="DatConsumoAseguramiento", mappedBy="parte", cascade={"persist"})
     */
    private $consumos;

    /**
     * @var float
     * @Assert\GreaterThan(
     *     value="0",
     *     message="El valor debe ser mayor que 0."
     * )
     * @ORM\Column(name="nivelact", type="float",  nullable=true)
     */
    private $nivelact;

    /**
     * @var float
     * @Assert\GreaterThan(
     *     value="0",
     *     message="El valor debe ser mayor que 0."
     * )
     * @ORM\Column(name="cantidadxnc", type="float",  nullable=true)
     */
    private $cantidadxnc;

    /**
     * @var NomTipoNorma
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\NomTipoNorma")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiponorma", referencedColumnName="id")
     * })
     */
    private $tiponorma;

    /**
     * @var float
     * @ORM\Column(name="grasa", type="float",  nullable=true)
     */
    private $grasa;

    /**
     * @var float
     * @ORM\Column(name="sng", type="float",  nullable=true)
     */
    private $sng;


    /**
     * Add consumos
     *
     * @param \ParteDiarioBundle\Entity\DatConsumoAseguramiento $consumos
     * @return DatParteDiarioConsAseg
     */
    public function addConsumo(\ParteDiarioBundle\Entity\DatConsumoAseguramiento $consumos)
    {
        $consumos->setParte($this);
        $this->consumos[] = $consumos;

        return $this;
    }

    /**
     * Remove consumos
     *
     * @param \ParteDiarioBundle\Entity\DatConsumoAseguramiento $consumos
     */
    public function removeConsumo(\ParteDiarioBundle\Entity\DatConsumoAseguramiento $consumos)
    {
        $this->consumos->removeElement($consumos);
    }

    /**
     * Get consumos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConsumos()
    {
        return $this->consumos;
    }

    /**
     * Set nivelact
     *
     * @param float $nivelact
     * @return DatParteDiarioConsAseg
     */
    public function setNivelact($nivelact)
    {
        $this->nivelact = $nivelact;

        return $this;
    }

    /**
     * Get nivelact
     *
     * @return float
     */
    public function getNivelact()
    {
        return $this->nivelact;
    }

    public function getNombreEntidad()
    {
        return ' el parte de consumo de materia prima: Producto: ' . $this->getProducto()->getNombre();
    }

    /**
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return DatParteDiarioConsAseg
     */
    public function setProducto(\NomencladorBundle\Entity\NomProducto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \NomencladorBundle\Entity\NomProducto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set cantidadxnc
     *
     * @param float $cantidadxnc
     * @return DatParteDiarioConsAseg
     */
    public function setCantidadxnc($cantidadxnc)
    {
        $this->cantidadxnc = $cantidadxnc;

        return $this;
    }

    /**
     * Get cantidadxnc
     *
     * @return float
     */
    public function getCantidadxnc()
    {
        return $this->cantidadxnc;
    }

    /**
     * Set tiponorma
     *
     * @param \NomencladorBundle\Entity\NomTipoNorma $tiponorma
     * @return DatParteDiarioConsAseg
     */
    public function setTiponorma(\NomencladorBundle\Entity\NomTipoNorma $tiponorma = null)
    {
        $this->tiponorma = $tiponorma;

        return $this;
    }

    /**
     * Get tiponorma
     *
     * @return \NomencladorBundle\Entity\NomTipoNorma
     */
    public function getTiponorma()
    {
        return $this->tiponorma;
    }

    /**
     * Set grasa
     *
     * @param float $grasa
     * @return DatParteDiarioConsAseg
     */
    public function setGrasa($grasa)
    {
        $this->grasa = $grasa;
    
        return $this;
    }

    /**
     * Get grasa
     *
     * @return float 
     */
    public function getGrasa()
    {
        return $this->grasa;
    }

    /**
     * Set sng
     *
     * @param float $sng
     * @return DatParteDiarioConsAseg
     */
    public function setSng($sng)
    {
        $this->sng = $sng;
    
        return $this;
    }

    /**
     * Get sng
     *
     * @return float 
     */
    public function getSng()
    {
        return $this->sng;
    }
}
