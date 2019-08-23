<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatActividades
 *
 * @ORM\Table(name="rep_actividades")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatActividadesRepository")
 */
class DatActividades
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
     * @ORM\Column(name="nombre_act", type="string", length=100)
     */
    private $nombreAct;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_tabla", type="string", length=100)
     */
    private $nombreTabla;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=100)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_id_tabla", type="string", length=100)
     */
    private $nombreIdTabla;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_tabla_princ", type="string", length=100, nullable=true)
     */
    private $nombreTablaPrinc;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_id_tabla_princ", type="string", length=100, nullable=true)
     */
    private $nombreIdTablaPrinc;

    /**
     * @var bool
     *
     * @ORM\Column(name="es_parte", type="boolean", nullable=true)
     */
    private $esParte;

    /**
     * @var bool
     *
     * @ORM\Column(name="es_plan", type="boolean", nullable=true)
     */
    private $esPlan;

    /**
     * @var bool
     *
     * @ORM\Column(name="es_nomenclador", type="boolean", nullable=true)
     */
    private $esNomenclador;

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
     * Set nombreAct
     *
     * @param string $nombreAct
     * @return DatActividades
     */
    public function setNombreAct($nombreAct)
    {
        $this->nombreAct = $nombreAct;
    
        return $this;
    }

    /**
     * Get nombreAct
     *
     * @return string 
     */
    public function getNombreAct()
    {
        return $this->nombreAct;
    }

    /**
     * Set nombreTabla
     *
     * @param string $nombreTabla
     * @return DatActividades
     */
    public function setNombreTabla($nombreTabla)
    {
        $this->nombreTabla = $nombreTabla;
    
        return $this;
    }

    /**
     * Get nombreTabla
     *
     * @return string 
     */
    public function getNombreTabla()
    {
        return $this->nombreTabla;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return DatActividades
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
     * Set nombreIdTabla
     *
     * @param string $nombreIdTabla
     * @return DatActividades
     */
    public function setNombreIdTabla($nombreIdTabla)
    {
        $this->nombreIdTabla = $nombreIdTabla;
    
        return $this;
    }

    /**
     * Get nombreIdTabla
     *
     * @return string 
     */
    public function getNombreIdTabla()
    {
        return $this->nombreIdTabla;
    }

    /**
     * Set nombreTablaPrinc
     *
     * @param string $nombreTablaPrinc
     * @return DatActividades
     */
    public function setNombreTablaPrinc($nombreTablaPrinc)
    {
        $this->nombreTablaPrinc = $nombreTablaPrinc;
    
        return $this;
    }

    /**
     * Get nombreTablaPrinc
     *
     * @return string 
     */
    public function getNombreTablaPrinc()
    {
        return $this->nombreTablaPrinc;
    }

    /**
     * Set nombreIdTablaPrinc
     *
     * @param string $nombreIdTablaPrinc
     * @return DatActividades
     */
    public function setNombreIdTablaPrinc($nombreIdTablaPrinc)
    {
        $this->nombreIdTablaPrinc = $nombreIdTablaPrinc;
    
        return $this;
    }

    /**
     * Get nombreIdTablaPrinc
     *
     * @return string 
     */
    public function getNombreIdTablaPrinc()
    {
        return $this->nombreIdTablaPrinc;
    }

    /**
     * Set esParte
     *
     * @param boolean $esParte
     * @return DatActividades
     */
    public function setEsParte($esParte)
    {
        $this->esParte = $esParte;
    
        return $this;
    }

    /**
     * Get esParte
     *
     * @return boolean 
     */
    public function getEsParte()
    {
        return $this->esParte;
    }

    /**
     * Set esPlan
     *
     * @param boolean $esPlan
     * @return DatActividades
     */
    public function setEsPlan($esPlan)
    {
        $this->esPlan = $esPlan;
    
        return $this;
    }

    /**
     * Get esPlan
     *
     * @return boolean 
     */
    public function getEsPlan()
    {
        return $this->esPlan;
    }

    /**
     * Set esNomenclador
     *
     * @param boolean $esNomenclador
     * @return DatActividades
     */
    public function setEsNomenclador($esNomenclador)
    {
        $this->esNomenclador = $esNomenclador;
    
        return $this;
    }

    /**
     * Get esNomenclador
     *
     * @return boolean 
     */
    public function getEsNomenclador()
    {
        return $this->esNomenclador;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return DatActividades
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
