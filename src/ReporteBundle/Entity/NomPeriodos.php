<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NomPeriodos
 *
 * @ORM\Table(name="rep_periodos")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\NomPeriodosRepository")
 */
class NomPeriodos
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
     * @var integer
     *
     * @ORM\Column(name="ident", type="string", length=50)
     */
    private $ident;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var int
     *
     * @ORM\Column(name="frecuencia", type="integer")
     */
    private $frecuencia;

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
     * Set descripcion
     *
     * @param string $descripcion
     * @return NomPeriodos
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set frecuencia
     *
     * @param integer $frecuencia
     * @return NomPeriodos
     */
    public function setFrecuencia($frecuencia)
    {
        $this->frecuencia = $frecuencia;
    
        return $this;
    }

    /**
     * Get frecuencia
     *
     * @return integer 
     */
    public function getFrecuencia()
    {
        return $this->frecuencia;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomPeriodos
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

    public function __toString()
    {
        return $this->getIdent();
    }

    /**
     * Set ident
     *
     * @param string $ident
     * @return NomPeriodos
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;
    
        return $this;
    }

    /**
     * Get ident
     *
     * @return string 
     */
    public function getIdent()
    {
        return $this->ident;
    }
}
