<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomRuta
 *
 * @ORM\Table(name="nom_ruta")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"}, errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomRuta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idruta", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idruta;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=true)
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
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * @ORM\OneToMany(targetEntity="NomRutaSuministrador", mappedBy="ruta", cascade={"persist", "remove"} ,orphanRemoval=true)
     * @Assert\Count(min=1, minMessage="Debe agregar al menos 1 suministrador")
     */
    private $suministradores;

    /**
     * Get idruta
     *
     * @return integer
     */
    public function getIdruta()
    {
        return $this->idruta;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomRuta
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
     * Set codigo
     *
     * @param string $codigo
     * @return NomRuta
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
     * @return NomRuta
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

    public function __toString()
    {
        return $this->getNombre();
    }


    /**
     * Set alias
     *
     * @param string $alias
     * @return NomRuta
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
        return ' la ruta: ' . $this->nombre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->suministradores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add suministradores
     *
     * @param \NomencladorBundle\Entity\NomRutaSuministrador $suministradores
     * @return NomRuta
     */
    public function addSuministradore(\NomencladorBundle\Entity\NomRutaSuministrador $suministradores)
    {
        $suministradores->setRuta($this);
        $this->suministradores[] = $suministradores;

        return $this;
    }

    /**
     * Remove suministradores
     *
     * @param \NomencladorBundle\Entity\NomRutaSuministrador $suministradores
     */
    public function removeSuministradore(\NomencladorBundle\Entity\NomRutaSuministrador $suministradores)
    {
        $this->suministradores->removeElement($suministradores);
    }

    /**
     * Get suministradores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSuministradores()
    {
        return $this->suministradores;
    }
}
