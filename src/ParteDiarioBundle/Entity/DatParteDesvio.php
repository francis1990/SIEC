<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomRuta;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomUnidadmedida;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatParteDesvio
 *
 * @ORM\Table(name="dat_parte_desvio")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteDesvioRepository")
 * @UniqueEntity(fields= {"fecha", "ueb", "producto","destino"}, message="Existe un elemento con fecha, UEB y entidad seleccionada.")
 */
class DatParteDesvio extends DatParte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="destino", type="integer",  nullable=true)
     */
    private $destino;

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
     * @var integer
     *
     * @ORM\Column(name="tipo", type="integer",  nullable=true)
     */
    private $tipo;

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
     * @ORM\Column(name="cantidad", type="float",  nullable=true)
     */
    private $cantidad;



    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return DatParteDesvio
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
     * @return DatParteDesvio
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
     * @return DatParteDesvio
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
     * Set destino
     *
     * @param integer $destino
     * @return DatParteDesvio
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;

        return $this;
    }

    /**
     * Get destino
     *
     * @return integer
     */
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return DatParteDesvio
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }


}
