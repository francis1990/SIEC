<?php

namespace NomencladorBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * NomPrecio
 *
 * @ORM\Table(name="nom_precio")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 */
class NomPrecio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="preciomn", type="float", precision=10, scale=5, nullable=true)
     */
    private $preciomn;

    /**
     * @var float
     *
     * @ORM\Column(name="preciocuc", type="float", precision=10, scale=5, nullable=true)
     */
    private $preciocuc;

    /**
     * @var NomProducto
     *
     * @ORM\ManyToOne(targetEntity="NomProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto", referencedColumnName="idproducto")
     * })
     */
    private $producto;

    /**
     * @var array
     *
     * @ORM\Column(name="grupo", type="json_array", nullable=true)
     */
    private $grupo;

    /**
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="um", referencedColumnName="idunidadmedida")
     * })
     */
    private $um;

    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer", nullable=false)
     */
    private $idpadre;

    /**
     * @var float
     *
     * @ORM\Column(name="impuesto", type="float", precision=5, scale=3, nullable=true)
     */
    private $impuesto;

    /**
     * @var string
     *
     * @ORM\Column(name="resolucion", type="string", nullable=true,length=20)
     */
    private $resolucion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

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
     * Set preciomn
     *
     * @param float $preciomn
     * @return NomPrecio
     */
    public function setPreciomn($preciomn)
    {
        $this->preciomn = $preciomn;

        return $this;
    }

    /**
     * Get preciomn
     *
     * @return float
     */
    public function getPreciomn()
    {
        return $this->preciomn;
    }

    /**
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return NomPrecio
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
     * @return NomPrecio
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
     * Set idpadre
     *
     * @param integer $idpadre
     * @return NomPrecio
     */
    public function setIdpadre($idpadre)
    {
        $this->idpadre = $idpadre;

        return $this;
    }

    /**
     * Get idpadre
     *
     * @return integer
     */
    public function getIdpadre()
    {
        return $this->idpadre;
    }

    /**
     * Get impuesto
     *
     * @return float
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set impuesto
     *
     * @param float $impuesto
     * @return NomPrecio
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Set grupo
     *
     * @param array $grupo
     * @return NomPrecio
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return array
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set preciocuc
     *
     * @param float $preciocuc
     * @return NomPrecio
     */
    public function setPreciocuc($preciocuc)
    {
        $this->preciocuc = $preciocuc;
    
        return $this;
    }

    /**
     * Get preciocuc
     *
     * @return float 
     */
    public function getPreciocuc()
    {
        return $this->preciocuc;
    }

    public function getNombreEntidad()
    {
        return ' el precio: ' . $this->producto->getNombre()."-CUP: ".$this->preciomn."-CUC: ".$this->preciocuc;
    }


    /**
     * Set resolucion
     *
     * @param string $resolucion
     * @return NomPrecio
     */
    public function setResolucion($resolucion)
    {
        $this->resolucion = $resolucion;
    
        return $this;
    }

    /**
     * Get resolucion
     *
     * @return string 
     */
    public function getResolucion()
    {
        return $this->resolucion;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return NomPrecio
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }
}
