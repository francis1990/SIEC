<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;

/**
 * NomTipoNorma
 *
 * @ORM\Table(name="nom_tipo_norma")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomTipoNormaRepository")
 */
class NomTipoNorma
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=4)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=120)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=120)
     */
    private $alias;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @ORM\OneToMany(targetEntity="NomencladorBundle\Entity\NomNorma", mappedBy="tiponorma", cascade={"persist"})
     */
    private $normas;

    public function __toString()
    {
     return $this->nombre;
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
     * @return NomTipoNorma
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
     * @return NomTipoNorma
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
     * Set alias
     *
     * @param string $alias
     * @return NomTipoNorma
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

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomTipoNorma
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
     * Constructor
     */
    public function __construct()
    {
        $this->normas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add normas
     *
     * @param \NomencladorBundle\Entity\NomNorma $normas
     * @return NomTipoNorma
     */
    public function addNorma(\NomencladorBundle\Entity\NomNorma $normas)
    {
        $this->normas[] = $normas;
    
        return $this;
    }

    /**
     * Remove normas
     *
     * @param \NomencladorBundle\Entity\NomNorma $normas
     */
    public function removeNorma(\NomencladorBundle\Entity\NomNorma $normas)
    {
        $this->normas->removeElement($normas);
    }

    /**
     * Get normas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNormas()
    {
        return $this->normas;
    }
}
