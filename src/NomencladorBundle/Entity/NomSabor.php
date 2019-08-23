<?php

namespace NomencladorBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomSabor
 *
 * @ORM\Table(name="nom_sabor")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomSabor
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idsabor", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idsabor;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=3, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\Regex(pattern="/\d{2}/",message="El valor debe ser un dígito.")
     * @Assert\Length(max=3,min=3, exactMessage = "El valor debe tener exactamente {{ limit }} caracteres.")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco")
     */
    private $nombre;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="NomSubgenerico", inversedBy="idsabor")
     * @ORM\JoinTable(name="nom_subgenerico_sabor",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idsabor", referencedColumnName="idsabor",onDelete="cascade")
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

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idsubgenerico = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idsabor
     *
     * @return integer
     */
    public function getIdsabor()
    {
        return $this->idsabor;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomSabor
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
     * @return NomSabor
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
     * Set activo
     *
     * @param boolean $activo
     * @return NomSabor
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
     * Add idsubgenerico
     *
     * @param \NomencladorBundle\Entity\NomSubgenerico $idsubgenerico
     * @return NomSabor
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

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomSabor
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
        return ' el sabor: ' . $this->nombre;
    }

}
