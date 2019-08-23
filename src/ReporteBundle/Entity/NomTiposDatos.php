<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NomTiposDatos
 *
 * @ORM\Table(name="rep_tipos_datos")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\NomTiposDatosRepository")
 */
class NomTiposDatos
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
     * @ORM\Column(name="nombre", type="string", length=25)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_real", type="string", length=25)
     */
    private $nombreReal;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;


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
     * @return NomTiposDatos
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
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
     * Set nombreReal
     *
     * @param string $nombreReal
     * @return NomTiposDatos
     */
    public function setNombreReal($nombreReal)
    {
        $this->nombreReal = $nombreReal;
    
        return $this;
    }

    /**
     * Get nombreReal
     *
     * @return string 
     */
    public function getNombreReal()
    {
        return $this->nombreReal;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomTiposDatos
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
}
