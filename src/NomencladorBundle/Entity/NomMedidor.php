<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomMedidor
 *
 * @ORM\Table(name="nom_medidor")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomMedidorRepository")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre", message="Existe un elemento con ese nombre")
 */
class NomMedidor
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
     * @ORM\Column(name="nombre", type="string", length=100, unique=true)
     */
    private $nombre;

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
     * Set nombre
     *
     * @param string $nombre
     * @return NomMedidor
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

    public function __toString()
    {
        return $this->nombre;
    }


    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomMedidor
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
     * @return NomMedidor
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
        return ' el medidor: ' . $this->nombre;
    }
}
