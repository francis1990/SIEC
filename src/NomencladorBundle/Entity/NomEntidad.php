<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomEntidad
 *
 * @ORM\Table(name="nom_entidad")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomEntidad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="identidad", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $identidad;


    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=15, nullable=false)
     *
     */

    private $codigo;

    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer", nullable=false)
     *
     */
    private $idpadre;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=150, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var bool
     *
     * @ORM\Column(name="estatal", type="boolean")
     */
    private $estatal;

    /**
     * @var bool
     *
     * @ORM\Column(name="acopio", type="boolean")
     */
    private $acopio;

    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;
    /**
     * @var bool
     *
     * @ORM\Column(name="vinculo", type="boolean")
     */
    private $vinculo;

    /**
     * @var string
     *
     * @ORM\Column(name="siglas", type="string", length=255, nullable=true)
     */
    private $siglas;
    /**
     * @var \NomDpa
     *
     * @ORM\ManyToOne(targetEntity="NomDpa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iddpa", referencedColumnName="iddpa")
     * })
     */
    private $iddpa;

    /**
     * @var \NomTipoEntidad
     *
     * @ORM\ManyToOne(targetEntity="NomTipoEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtipoentidad", referencedColumnName="idtipoentidad")
     * })
     */
    private $idtipoentidad;

    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer", nullable=false)
     */
    private $nivel;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * @var bool
     *
     * @ORM\Column(name="receptor", type="boolean",nullable=true)
     */
    private $receptor;

    /**
     * @var integer
     *
     * @ORM\Column(name="diasvencidos", type="integer", nullable=true)
     *
     */
    private $diasvencidos;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->identidad = new \Doctrine\Common\Collections\ArrayCollection();
    }


    public function __toString()
    {
        return $this->getNombre();
    }


    /**
     * Get identidad
     *
     * @return integer
     */
    public function getIdentidad()
    {
        return $this->identidad;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomEntidad
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
     * Set idpadre
     *
     * @param integer $idpadre
     * @return NomEntidad
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
     * Set nombre
     *
     * @param string $nombre
     * @return NomEntidad
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
     * Set direccion
     *
     * @param string $direccion
     * @return NomEntidad
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set estatal
     *
     * @param boolean $estatal
     * @return NomEntidad
     */
    public function setEstatal($estatal)
    {
        $this->estatal = $estatal;

        return $this;
    }

    /**
     * Get estatal
     *
     * @return boolean
     */
    public function getEstatal()
    {
        return $this->estatal;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomEntidad
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
     * Set hoja
     *
     * @param boolean $hoja
     * @return NomEntidad
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
     * Set vinculo
     *
     * @param boolean $vinculo
     * @return NomEntidad
     */
    public function setVinculo($vinculo)
    {
        $this->vinculo = $vinculo;

        return $this;
    }

    /**
     * Get vinculo
     *
     * @return boolean
     */
    public function getVinculo()
    {
        return $this->vinculo;
    }

    /**
     * Set siglas
     *
     * @param string $siglas
     * @return NomEntidad
     */
    public function setSiglas($siglas)
    {
        $this->siglas = $siglas;

        return $this;
    }

    /**
     * Get siglas
     *
     * @return string
     */
    public function getSiglas()
    {
        return $this->siglas;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return NomEntidad
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
     * Set iddpa
     *
     * @param \NomencladorBundle\Entity\NomDpa $iddpa
     * @return NomEntidad
     */
    public function setIddpa(\NomencladorBundle\Entity\NomDpa $iddpa = null)
    {
        $this->iddpa = $iddpa;

        return $this;
    }

    /**
     * Get iddpa
     *
     * @return \NomencladorBundle\Entity\NomDpa
     */
    public function getIddpa()
    {
        return $this->iddpa;
    }

    /**
     * Set acopio
     *
     * @param boolean $acopio
     * @return NomEntidad
     */
    public function setAcopio($acopio)
    {
        $this->acopio = $acopio;

        return $this;
    }

    /**
     * Get acopio
     *
     * @return boolean
     */
    public function getAcopio()
    {
        return $this->acopio;
    }

    /**
     * Set idtipoentidad
     *
     * @param \NomencladorBundle\Entity\NomTipoEntidad $idtipoentidad
     * @return NomEntidad
     */
    public function setIdtipoentidad(\NomencladorBundle\Entity\NomTipoEntidad $idtipoentidad = null)
    {
        $this->idtipoentidad = $idtipoentidad;

        return $this;
    }

    /**
     * Get idtipoentidad
     *
     * @return \NomencladorBundle\Entity\NomTipoEntidad
     */
    public function getIdtipoentidad()
    {
        return $this->idtipoentidad;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomEntidad
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
        return ' la entidad: ' . $this->nombre;
    }

    /**
     * Set receptor
     *
     * @param boolean $receptor
     * @return NomEntidad
     */
    public function setReceptor($receptor)
    {
        $this->receptor = $receptor;
    
        return $this;
    }

    /**
     * Get receptor
     *
     * @return boolean 
     */
    public function getReceptor()
    {
        return $this->receptor;
    }

    /**
     * Set diasvencidos
     *
     * @param integer $diasvencidos
     * @return NomEntidad
     */
    public function setDiasvencidos($diasvencidos)
    {
        $this->diasvencidos = $diasvencidos;
    
        return $this;
    }

    /**
     * Get diasvencidos
     *
     * @return integer 
     */
    public function getDiasvencidos()
    {
        return $this->diasvencidos;
    }
}
