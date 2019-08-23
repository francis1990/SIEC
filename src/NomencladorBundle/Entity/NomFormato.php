<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomFormato
 *
 * @ORM\Table(name="nom_formato")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomFormato
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
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=3, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\Regex(pattern="/\d/",message="El valor debe ser un dígito.")
     * @Assert\Length(max=3,min=3, exactMessage = "El valor debe tener exactamente {{ limit }} caracteres.")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;


    /**
     * @var float
     *
     * @ORM\Column(name="peso", type="float", precision=10, scale=5, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\GreaterThan(value = 0,message = "El valor debe ser mayor que {{ compared_value }}.")
     */
    private $peso;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="NomSubgenerico", inversedBy="idformato")
     * @ORM\JoinTable(name="nom_subgenerico_formato",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idformato", referencedColumnName="id",onDelete="cascade")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idsubgenerico", referencedColumnName="idsubgenerico")
     *   }
     * )
     */
    private $idsubgenerico;

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
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idsubgenerico = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set codigo
     *
     * @param string $codigo
     * @return NomFormato
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
     * @return NomFormato
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
     * Set peso
     *
     * @param float $peso
     * @return NomFormato
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso
     *
     * @return float
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set idunidadmedida
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida
     * @return NomFormato
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

    /**
     * Add idsubgenerico
     *
     * @param \NomencladorBundle\Entity\NomSubgenerico $idsubgenerico
     * @return NomFormato
     */
    public function addIdsubgenerico(\NomencladorBundle\Entity\NomSubgenerico $idsubgenerico)
    {
        $this->idsubgenerico[] = $idsubgenerico;

        return $this;
    }

    /**
     * Remove idsubgenerico
     *
     * @param \NomencladorBundle\Entity\NomSubgenerico $idsubgenerico
     */
    public function removeIdsubgenerico(\NomencladorBundle\Entity\NomSubgenerico $idsubgenerico)
    {
        $this->idsubgenerico->removeElement($idsubgenerico);
    }

    /**
     * Get idsubgenerico
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdsubgenerico()
    {
        return $this->idsubgenerico;
    }

    public function __toString()
    {
        return $this->getNombre();
    }


    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomFormato
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
     * Set alias
     *
     * @param string $alias
     * @return NomFormato
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
        return ' el formato: ' . $this->nombre;
    }

}
