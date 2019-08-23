<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomAseguramiento
 *
 * @ORM\Table(name="nom_aseguramiento")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"nombre"}, message="Existe un elemento con ese nombre")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con este código")
 * @UniqueEntity(fields= {"ordenmpb"}, message="Existe un elemento con este orden MPB")
 */
class NomAseguramiento
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idaseguramiento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idaseguramiento;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;

    /**
     * @var \NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idunidadmedida", referencedColumnName="idunidadmedida")
     * })
     */
    private $idunidadmedida;
    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer", nullable=false)
     *
     */
    private $idpadre;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_cuc", type="float", nullable=true)
     *
     */
    private $precio_cuc;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_cup", type="float", nullable=true)
     *
     */
    private $precio_cup;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=18, nullable=true)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     *
     */
    private $codigo;

    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;

    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer", nullable=false)
     */
    private $nivel;

    /**
     * @var bool
     *
     * @ORM\Column(name="mpb", type="boolean", nullable=true)
     */
    private $mpb;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordenmpb", type="integer", nullable=true)
     */
    private $ordenmpb;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Get idaseguramiento
     *
     * @return integer
     */
    public function getIdaseguramiento()
    {
        return $this->idaseguramiento;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomAseguramiento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->alias = Util::getSlug($nombre);
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getNom()
    {
        return $this->generarNombre();
    }

    /**
     * Set idunidadmedida
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida
     * @return NomAseguramiento
     */
    public function setIdunidadmedida(\NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida = null)
    {
        $this->idunidadmedida = $idunidadmedida;

        return $this;
    }

    /**
     * Get idunidadmedida
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getIdunidadmedida()
    {
        return $this->idunidadmedida;
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomAseguramiento
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set idpadre
     *
     * @param integer $idpadre
     * @return NomAseguramiento
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
     * Set nivel
     *
     * @param integer $nivel
     * @return NomAseguramiento
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }


    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return NomAseguramiento
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set hoja
     *
     * @param boolean $hoja
     * @return NomAseguramiento
     */
    public function setHoja($hoja)
    {
        $this->hoja = $hoja;

        return $this;
    }

    /**
     * Get hoja
     *
     * @return boolean 
     */
    public function getHoja()
    {
        return $this->hoja;
    }

    /**
     * Set mpb
     *
     * @param boolean $mpb
     * @return NomAseguramiento
     */
    public function setMpb($mpb)
    {
        $this->mpb = $mpb;
    
        return $this;
    }

    /**
     * Get mpb
     *
     * @return boolean 
     */
    public function getMpb()
    {
        return $this->mpb;
    }

    /**
     * Set ordenmpb
     *
     * @param integer $ordenmpb
     * @return NomAseguramiento
     */
    public function setOrdenmpb($ordenmpb)
    {
        $this->ordenmpb = $ordenmpb;
    
        return $this;
    }

    /**
     * Get ordenmpb
     *
     * @return integer 
     */
    public function getOrdenmpb()
    {
        return $this->ordenmpb;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomAseguramiento
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    
        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function getNombreEntidad()
    {
        return ' el aseguramiento: ' . $this->nombre;
    }


    /**
     * Set precio_cuc
     *
     * @param float $precioCuc
     * @return NomAseguramiento
     */
    public function setPrecioCuc($precioCuc)
    {
        $this->precio_cuc = $precioCuc;
    
        return $this;
    }

    /**
     * Get precio_cuc
     *
     * @return float 
     */
    public function getPrecioCuc()
    {
        return $this->precio_cuc;
    }

    /**
     * Set precio_cup
     *
     * @param float $precioCup
     * @return NomAseguramiento
     */
    public function setPrecioCup($precioCup)
    {
        $this->precio_cup = $precioCup;
    
        return $this;
    }

    /**
     * Get precio_cup
     *
     * @return float 
     */
    public function getPrecioCup()
    {
        return $this->precio_cup;
    }
}
