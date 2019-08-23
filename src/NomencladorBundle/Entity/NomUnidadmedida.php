<?php

namespace NomencladorBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use EnumsBundle\Entity\EnumTipoUnidadMedida;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomEntidad
 *
 * @ORM\Table(name="nom_unidadmedida")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 * @UniqueEntity(fields= {"abreviatura"},message="Existe un elemento con la misma abreviatura.")
 */
class NomUnidadmedida
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idunidadmedida", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idunidadmedida;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=false, unique=true)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="abreviatura", type="string", nullable=true, length=10)
     */
    private $abreviatura;

    /**
     * @var integer
     *
     * @ORM\Column(name="idtipoum", type="integer")
     */
    private $idtipoum;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * @var integer
     *
     * @ORM\Column(name="cantdecimal", type="integer", nullable=true)
     */
    private $cantdecimal;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idunidadmedida = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idunidadmedida
     *
     * @return integer
     */
    public function getIdunidadmedida()
    {
        return $this->idunidadmedida;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return integer
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomUnidadmedida
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

    /**
     * Set abreviatura
     *
     * @param string $abreviatura
     * @return NomUnidadmedida
     */
    public function setAbreviatura($abreviatura)
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }

    /**
     * Get abreviatura
     *
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomUnidadmedida
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

    public function setIdTipoUM($um)
    {
        $this->idtipoum = $um;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdTipoUM()
    {
        return $this->idtipoum;
    }

    /**
     * @return string
     */
    public function getStrTiposUnidadMedida()
    {
        $tiposUM = new EnumTipoUnidadMedida();
        return $tiposUM->tiposUmToString($this->idtipoum);
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomUnidadmedida
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
        return ' la unidad de medida: ' . $this->nombre;
    }


    /**
     * Set cantdecimal
     *
     * @param integer $cantdecimal
     * @return NomUnidadmedida
     */
    public function setCantdecimal($cantdecimal)
    {
        $this->cantdecimal = $cantdecimal;
    
        return $this;
    }

    /**
     * Get cantdecimal
     *
     * @return integer 
     */
    public function getCantdecimal()
    {
        return $this->cantdecimal;
    }
}
