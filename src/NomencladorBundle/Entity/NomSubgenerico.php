<?php

namespace NomencladorBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use NomencladorBundle\Entity\NomGenerico;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomSubgenerico
 *
 * @ORM\Table(name="nom_subgenerico")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomSubgenericoRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre", message="Existe un elemento con el mismo nombre.")
 */
class NomSubgenerico
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idsubgenerico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idsubgenerico;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\Regex(pattern="/\d{2}/",message="El valor debe ser un dígito.")
     * @Assert\Length(max=2,min=2, exactMessage = "El valor debe tener exactamente {{ limit }} caracteres.")
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
     * @ORM\ManyToMany(targetEntity="NomFormato", mappedBy="idsubgenerico")
     */
    private $idformato;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="NomEspecifico", mappedBy="idsubgenerico")
     */
    private $idespecifico;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="NomSabor", mappedBy="idsubgenerico")
     */
    private $idsabor;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var NomGenerico
     *
     * @ORM\ManyToOne(targetEntity="NomGenerico", inversedBy="subgenericos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idgenerico", referencedColumnName="idgenerico")
     * })
     */
    private $generico;

    /**
     * @var bool
     *
     * @ORM\Column(name="empaque", type="boolean")
     */
    private $empaque;

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
        $this->idformato = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idespecifico = new \Doctrine\Common\Collections\ArrayCollection();
        $this->idsabor = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idsubgenerico
     *
     * @return integer
     */
    public function getIdsubgenerico()
    {
        return $this->idsubgenerico;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomSubgenerico
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
     * @return NomSubgenerico
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->alias =  Util::getSlug($nombre);

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
     * @return NomSubgenerico
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
     * Add idformato
     *
     * @param \NomencladorBundle\Entity\NomFormato $idformato
     * @return NomSubgenerico
     */
    public function addIdformato(\NomencladorBundle\Entity\NomFormato $idformato)
    {
        $this->idformato[] = $idformato;

        return $this;
    }

    /**
     * Remove idformato
     *
     * @param \NomencladorBundle\Entity\NomFormato $idformato
     */
    public function removeIdformato(\NomencladorBundle\Entity\NomFormato $idformato)
    {
        $this->idformato->removeElement($idformato);
    }

    /**
     * Get idformato
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdformato()
    {
        return $this->idformato;
    }

    /**
     * Add idespecifico
     *
     * @param \NomencladorBundle\Entity\NomEspecifico $idespecifico
     * @return NomSubgenerico
     */
    public function addIdespecifico(\NomencladorBundle\Entity\NomEspecifico $idespecifico)
    {
        $this->idespecifico[] = $idespecifico;

        return $this;
    }

    /**
     * Remove idespecifico
     *
     * @param \NomencladorBundle\Entity\NomEspecifico $idespecifico
     */
    public function removeIdespecifico(\NomencladorBundle\Entity\NomEspecifico $idespecifico)
    {
        $this->idespecifico->removeElement($idespecifico);
    }

    /**
     * Get idespecifico
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdespecifico()
    {
        return $this->idespecifico;
    }

    /**
     * Add idsabor
     *
     * @param \NomencladorBundle\Entity\NomSabor $idsabor
     * @return NomSubgenerico
     */
    public function addIdsabor(\NomencladorBundle\Entity\NomSabor $idsabor)
    {
        $this->idsabor[] = $idsabor;

        return $this;
    }

    /**
     * Remove idsabor
     *
     * @param \NomencladorBundle\Entity\NomSabor $idsabor
     */
    public function removeIdsabor(\NomencladorBundle\Entity\NomSabor $idsabor)
    {
        $this->idsabor->removeElement($idsabor);
    }

    /**
     * Get idsabor
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdsabor()
    {
        return $this->idsabor;
    }

    /**
     * Set generico
     *
     * @param \NomencladorBundle\Entity\NomGenerico $generico
     * @return NomSubgenerico
     */
    public function setGenerico(\NomencladorBundle\Entity\NomGenerico $generico = null)
    {
        $this->generico = $generico;

        return $this;
    }

    /**
     * Get generico
     *
     * @return \NomencladorBundle\Entity\NomGenerico
     */
    public function getGenerico()
    {
        return $this->generico;
    }

    /**
     * Set empaque
     *
     * @param boolean $empaque
     * @return NomSubgenerico
     */
    public function setEmpaque($empaque)
    {
        $this->empaque = $empaque;
        return $this;
    }

    /**
     * Get empaque
     *
     * @return boolean
     */
    public function getEmpaque()
    {
        return $this->empaque;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomSubgenerico
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
        return ' el sub-genérico: ' . $this->nombre;
    }
}
