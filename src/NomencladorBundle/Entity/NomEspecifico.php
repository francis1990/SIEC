<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * NomEspecifico
 *
 * @ORM\Table(name="nom_especifico")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomEspecifico
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idespecifico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idespecifico;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\Regex(pattern="/\d{2}/",message="El valor debe ser un dígito.")
     * @Assert\Length(max=2,min=2, exactMessage = "El valor debe tener exactamente {{ limit }} caracteres.")
     *
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
     * @ORM\ManyToMany(targetEntity="NomTipoespecifico", mappedBy="idespecifico")
     */
    private $idtipoespecifico;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="NomSubgenerico", inversedBy="idespecifico" )
     * @ORM\JoinTable(name="nom_subgenerico_especifico",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idespecifico", referencedColumnName="idespecifico",onDelete="cascade")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idsubgenerico", referencedColumnName="idsubgenerico")
     *   }
     * )
     */
    private $idsubgenerico;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    public function __toString()
    {
        return $this->getNombre();
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idtipoespecifico = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idsubgenerico = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idespecifico
     *
     * @return integer
     */
    public function getIdespecifico()
    {
        return $this->idespecifico;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomEspecifico
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
     * @return NomEspecifico
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
     * Add idtipoespecifico
     *
     * @param \NomencladorBundle\Entity\NomTipoespecifico $idtipoespecifico
     * @return NomEspecifico
     */
    public function addIdtipoespecifico(\NomencladorBundle\Entity\NomTipoespecifico $idtipoespecifico)
    {
        $this->idtipoespecifico[] = $idtipoespecifico;

        return $this;
    }

    /**
     * Remove idtipoespecifico
     *
     * @param \NomencladorBundle\Entity\NomTipoespecifico $idtipoespecifico
     */
    public function removeIdtipoespecifico(\NomencladorBundle\Entity\NomTipoespecifico $idtipoespecifico)
    {
        $this->idtipoespecifico->removeElement($idtipoespecifico);
    }

    /**
     * Get idtipoespecifico
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdtipoespecifico()
    {
        return $this->idtipoespecifico;
    }

    /**
     * Add idsubgenerico
     *
     * @param \NomencladorBundle\Entity\NomSubgenerico $idsubgenerico
     * @return NomEspecifico
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
     * Set activo
     *
     * @param boolean $activo
     * @return NomEspecifico
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
     * @return NomEspecifico
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
        return ' el específico: ' . $this->nombre;
    }

}
