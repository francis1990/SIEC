<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use NomencladorBundle\Entity\NomSubgenerico;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomGenerico
 *
 * @ORM\Table(name="nom_generico")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomGenericoRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomGenerico
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idgenerico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idgenerico;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\Regex(pattern="/\d{2}/",message="El valor debe ser un dígito")
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
     * @ORM\OneToMany(targetEntity="NomSubgenerico", mappedBy="generico")
     */
    private $subgenericos;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var bool
     *
     * @ORM\Column(name="acopio", type="boolean")
     */
    private $acopio;

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
        $this->subgenericos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idgenerico
     *
     * @return integer
     */
    public function getIdgenerico()
    {
        return $this->idgenerico;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomGenerico
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
     * @return NomGenerico
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
     * Add subgenericos
     *
     * @param \NomencladorBundle\Entity\NomSubgenerico $subgenericos
     * @return NomGenerico
     */
    public function addSubgenerico(\NomencladorBundle\Entity\NomSubgenerico $subgenericos)
    {
        $this->subgenericos[] = $subgenericos;

        return $this;
    }

    /**
     * Remove subgenericos
     *
     * @param \NomencladorBundle\Entity\NomSubgenerico $subgenericos
     */
    public function removeSubgenerico(\NomencladorBundle\Entity\NomSubgenerico $subgenericos)
    {
        $this->subgenericos->removeElement($subgenericos);
    }

    /**
     * Get subgenericos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubgenericos()
    {
        return $this->subgenericos;
    }


    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomGenerico
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
     * Set acopio
     *
     * @param boolean $acopio
     * @return NomGenerico
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
     * Set alias
     *
     * @param string $alias
     * @return NomGenerico
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
        return ' el genérico: ' . $this->nombre;
    }

}
