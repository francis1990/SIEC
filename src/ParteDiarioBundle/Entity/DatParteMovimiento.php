<?php

namespace ParteDiarioBundle\Entity;

use NomencladorBundle\Entity\NomConcepto;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomUeb;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomAlmacen;

/**
 * DatParteMovAlmacen
 *
 * @ORM\Table(name="dat_parte_movalm")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteMovimientoRepository")
 */
class DatParteMovimiento extends DatParte
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
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="um", referencedColumnName="idunidadmedida")
     * })
     */
    private $um;

    /**
     * @var NomAlmacen
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomAlmacen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="almacen", referencedColumnName="idalmacen")
     * })
     */
    private $almacen;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=false)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @ORM\Column(name="existencia", type="float", precision=10, scale=0, nullable=false)
     */
    private $existencia;

    /**
     * @var NomConcepto
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomConcepto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="concepto", referencedColumnName="idconcepto")
     * })
     */
    private $concepto;


    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return DatParteMovimiento
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
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return DatParteMovimiento
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
     * Set um
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $um
     * @return DatParteMovimiento
     */
    public function setUm(\NomencladorBundle\Entity\NomUnidadmedida $um = null)
    {
        $this->um = $um;

        return $this;
    }

    /**
     * Get um
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getUm()
    {
        return $this->um;
    }

    /**
     * Set almacen
     *
     * @param \NomencladorBundle\Entity\NomAlmacen $almacen
     * @return DatParteMovimiento
     */
    public function setAlmacen(\NomencladorBundle\Entity\NomAlmacen $almacen = null)
    {
        $this->almacen = $almacen;
        return $this;
    }

    /**
     * Get almacen
     *
     * @return \NomencladorBundle\Entity\NomAlmacen
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set existencia
     *
     * @param float $existencia
     * @return DatParteMovimiento
     */
    public function setExistencia($existencia)
    {
        $this->existencia = $existencia;

        return $this;
    }

    /**
     * Get existencia
     *
     * @return float
     */
    public function getExistencia()
    {
        return $this->existencia;
    }

    /**
     * Set concepto
     *
     * @param \NomencladorBundle\Entity\NomConcepto $concepto
     * @return DatParteMovimiento
     */
    public function setConcepto(\NomencladorBundle\Entity\NomConcepto $concepto = null)
    {
        $this->concepto = $concepto;
    
        return $this;
    }

    /**
     * Get concepto
     *
     * @return \NomencladorBundle\Entity\NomConcepto 
     */
    public function getConcepto()
    {
        return $this->concepto;
    }

    public function getNombreEntidad()
    {
        return ' el parte de movimiento de almacén: Producto: ' . $this->getProducto()->getNombre()." - Almacén: ".$this->getAlmacen()->getNombre();
    }
}
