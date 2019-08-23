<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 08/12/2016
 * Time: 0:03
 */

namespace ParteDiarioBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomEntidad;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomProducto;
use ParteDiarioBundle\Entity\DatParteAcopio;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DatEntidadAcopio
 *
 * @ORM\Table(name="dat_parteacopio_entidad")
 * @ORM\Entity
 */
class DatEntidadAcopio {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var DatParteAcopio
     *
     * @ORM\ManyToOne(targetEntity="\ParteDiarioBundle\Entity\DatParteAcopio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parte", referencedColumnName="idparte", onDelete="cascade")
     * })
     */
    private $parte;

    /**
     * @var NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entidad", referencedColumnName="identidad")
     * })
     */
    private $entidad;

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
     * @ORM\Column(name="cantidad", type="float",  nullable=true)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @ORM\Column(name="acidez", type="float", nullable=true)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $acidez;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return DatEntidadAcopio
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
        return !is_null($this->cantidad) ? $this->cantidad : 0;
    }

    /**
     * Set parte
     *
     * @param \ParteDiarioBundle\Entity\DatParteAcopio $parte
     * @return DatEntidadAcopio
     */
    public function setParte(\ParteDiarioBundle\Entity\DatParteAcopio $parte = null)
    {
        $this->parte = $parte;

        return $this;
    }

    /**
     * Get parte
     *
     * @return \ParteDiarioBundle\Entity\DatParteAcopio 
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * Set entidad
     *
     * @param \NomencladorBundle\Entity\NomEntidad $entidad
     * @return DatEntidadAcopio
     */
    public function setEntidad(\NomencladorBundle\Entity\NomEntidad $entidad = null)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad
     *
     * @return \NomencladorBundle\Entity\NomEntidad 
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set um
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $um
     * @return DatEntidadAcopio
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
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return DatEntidadAcopio
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
     * Set acidez
     *
     * @param float $acidez
     * @return DatEntidadAcopio
     */
    public function setAcidez($acidez)
    {
        $this->acidez = $acidez;
    
        return $this;
    }

    /**
     * Get acidez
     *
     * @return float 
     */
    public function getAcidez()
    {
        return !is_null($this->acidez) ? $this->acidez : 0;
    }
}
