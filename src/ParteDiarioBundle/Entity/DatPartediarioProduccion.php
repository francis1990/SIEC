<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use NomencladorBundle\Entity\NomUeb;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomMonedadestino;
use ParteDiarioBundle\Entity\DatConsumoProduccion;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatPartediarioProduccion
 *
 * @ORM\Table(name="dat_parte_produccion")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteProduccionRepository")
 * @UniqueEntity(fields= {"fecha", "ueb", "producto","moneda","almacen"}, message="Existe un elemento con fecha, UEB, producto, moneda y almacén seleccionado.")
 */
class DatPartediarioProduccion extends DatParte
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
     * @var float
     *
     * @ORM\Column(name="cantidadmn", type="float", precision=10, scale=0, nullable=false)
     */
    private $cantidad;


    /**
     * @var float
     *
     * @ORM\Column(name="cantidadproceso", type="float", precision=10, scale=0, nullable=true)
     * @Assert\Range(
     *      min = "0",
     *      minMessage = "El valor mínimo del campo es 0.")
     *
     */
    private $cantproceso;

    /**
     * @var float
     *
     * @ORM\Column(name="cantempaque", type="float", precision=10, scale=3, nullable=true)
     * @Assert\Range(
     *      min = "0",
     *      minMessage = "El valor mínimo del campo es 0.")
     *
     */
    private $cantempaque;


    /**
     * @var NomMonedadestino
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomMonedadestino")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idmoneda", referencedColumnName="id")
     * })
     */
    private $moneda;

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
     * @var integer
     * @ORM\Column(name="entrega", type="integer")
     * @Assert\Range(
     *      min = "1",
     *      minMessage = "El valor mínimo del campo es 1.")
     *
     */
    private $entrega;


    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return DatPartediarioProduccion
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
     * Set cantproceso
     *
     * @param float $cantproceso
     * @return DatPartediarioProduccion
     */
    public function setCantproceso($cantproceso)
    {
        $this->cantproceso = $cantproceso;
    
        return $this;
    }

    /**
     * Get cantproceso
     *
     * @return float 
     */
    public function getCantproceso()
    {
        return $this->cantproceso;
    }

    /**
     * Set cantempaque
     *
     * @param float $cantempaque
     * @return DatPartediarioProduccion
     */
    public function setCantempaque($cantempaque)
    {
        $this->cantempaque = $cantempaque;

        return $this;
    }

    /**
     * Get cantempaque
     *
     * @return float
     */
    public function getCantempaque()
    {
        return $this->cantempaque;
    }

    /**
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return DatPartediarioProduccion
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
     * @return DatPartediarioProduccion
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
     * Set moneda
     *
     * @param \NomencladorBundle\Entity\NomMonedadestino $moneda
     * @return DatPartediarioProduccion
     */
    public function setMoneda(\NomencladorBundle\Entity\NomMonedadestino $moneda = null)
    {
        $this->moneda = $moneda;
    
        return $this;
    }

    /**
     * Get moneda
     *
     * @return \NomencladorBundle\Entity\NomMonedadestino 
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set almacen
     *
     * @param \NomencladorBundle\Entity\NomAlmacen $almacen
     * @return DatPartediarioProduccion
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
     * Set entrega
     *
     * @param integer $entrega
     * @return DatPartediarioProduccion
     */
    public function setEntrega($entrega)
    {
        $this->entrega = $entrega;

        return $this;
    }

    /**
     * Get entrega
     *
     * @return integer
     */
    public function getEntrega()
    {
        return $this->entrega;
    }

    public function getNombreEntidad()
    {
        return ' el parte de nivel de actividad: Producto: ' . $this->getProducto()->getNombre();
    }
}
