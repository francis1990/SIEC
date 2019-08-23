<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatIndicadores
 *
 * @ORM\Table(name="rep_indicadores")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatIndicadoresRepository")
 */
class DatIndicadores
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
     * @var int
     *
     * @ORM\Column(name="id_actividad", type="integer")
     */
    private $idActividad;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_campo", type="string", length=100)
     */
    private $nombreCampo;

    /**
     * @var int
     *
     * @ORM\Column(name="id_tipodato", type="integer")
     */
    private $idTipodato;

    /**
     * @var int
     *
     * @ORM\Column(name="cant_decimales", type="integer")
     */
    private $cantDecimales;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_tabla_fk", type="string", length=100)
     */
    private $nombreTablaFk;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_campo_tabla_fk", type="string", length=100)
     */
    private $nombreCampoTablaFk;

    /**
     * @var bool
     *
     * @ORM\Column(name="puede_ser_campo_fila", type="boolean", nullable=true)
     */
    private $puedeSerCampoFila;

    /**
     * @var bool
     *
     * @ORM\Column(name="puede_ser_campo_encabezado", type="boolean", nullable=true)
     */
    private $puedeSerCampoEncabezado;

    /**
     * @var bool
     *
     * @ORM\Column(name="es_campo_valor", type="boolean", nullable=true)
     */
    private $esCampoValor;

    /**
     * @var bool
     *
     * @ORM\Column(name="es_campo_llave", type="boolean", nullable=true)
     */
    private $esCampoLlave;

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
     * Set idActividad
     *
     * @param integer $idActividad
     * @return DatIndicadores
     */
    public function setIdActividad($idActividad)
    {
        $this->idActividad = $idActividad;
    
        return $this;
    }

    /**
     * Get idActividad
     *
     * @return integer 
     */
    public function getIdActividad()
    {
        return $this->idActividad;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return DatIndicadores
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
     * Set nombreCampo
     *
     * @param string $nombreCampo
     * @return DatIndicadores
     */
    public function setNombreCampo($nombreCampo)
    {
        $this->nombreCampo = $nombreCampo;
    
        return $this;
    }

    /**
     * Get nombreCampo
     *
     * @return string 
     */
    public function getNombreCampo()
    {
        return $this->nombreCampo;
    }

    /**
     * Set idTipodato
     *
     * @param integer $idTipodato
     * @return DatIndicadores
     */
    public function setIdTipodato($idTipodato)
    {
        $this->idTipodato = $idTipodato;
    
        return $this;
    }

    /**
     * Get idTipodato
     *
     * @return integer 
     */
    public function getIdTipodato()
    {
        return $this->idTipodato;
    }

    /**
     * Set cantDecimales
     *
     * @param integer $cantDecimales
     * @return DatIndicadores
     */
    public function setCantDecimales($cantDecimales)
    {
        $this->cantDecimales = $cantDecimales;
    
        return $this;
    }

    /**
     * Get cantDecimales
     *
     * @return integer 
     */
    public function getCantDecimales()
    {
        return $this->cantDecimales;
    }

    /**
     * Set nombreTablaFk
     *
     * @param string $nombreTablaFk
     * @return DatIndicadores
     */
    public function setNombreTablaFk($nombreTablaFk)
    {
        $this->nombreTablaFk = $nombreTablaFk;
    
        return $this;
    }

    /**
     * Get nombreTablaFk
     *
     * @return string 
     */
    public function getNombreTablaFk()
    {
        return $this->nombreTablaFk;
    }

    /**
     * Set nombreCampoTablaFk
     *
     * @param string $nombreCampoTablaFk
     * @return DatIndicadores
     */
    public function setNombreCampoTablaFk($nombreCampoTablaFk)
    {
        $this->nombreCampoTablaFk = $nombreCampoTablaFk;
    
        return $this;
    }

    /**
     * Get nombreCampoTablaFk
     *
     * @return string 
     */
    public function getNombreCampoTablaFk()
    {
        return $this->nombreCampoTablaFk;
    }

    /**
     * Set puedeSerCampoFila
     *
     * @param boolean $puedeSerCampoFila
     * @return DatIndicadores
     */
    public function setPuedeSerCampoFila($puedeSerCampoFila)
    {
        $this->puedeSerCampoFila = $puedeSerCampoFila;
    
        return $this;
    }

    /**
     * Get puedeSerCampoFila
     *
     * @return boolean 
     */
    public function getPuedeSerCampoFila()
    {
        return $this->puedeSerCampoFila;
    }

    /**
     * Set puedeSerCampoEncabezado
     *
     * @param boolean $puedeSerCampoEncabezado
     * @return DatIndicadores
     */
    public function setPuedeSerCampoEncabezado($puedeSerCampoEncabezado)
    {
        $this->puedeSerCampoEncabezado = $puedeSerCampoEncabezado;
    
        return $this;
    }

    /**
     * Get puedeSerCampoEncabezado
     *
     * @return boolean 
     */
    public function getPuedeSerCampoEncabezado()
    {
        return $this->puedeSerCampoEncabezado;
    }

    /**
     * Set esCampoValor
     *
     * @param boolean $esCampoValor
     * @return DatIndicadores
     */
    public function setEsCampoValor($esCampoValor)
    {
        $this->esCampoValor = $esCampoValor;
    
        return $this;
    }

    /**
     * Get esCampoValor
     *
     * @return boolean 
     */
    public function getEsCampoValor()
    {
        return $this->esCampoValor;
    }

    /**
     * Set esCampoLlave
     *
     * @param boolean $esCampoLlave
     * @return DatIndicadores
     */
    public function setEsCampoLlave($esCampoLlave)
    {
        $this->esCampoLlave = $esCampoLlave;
    
        return $this;
    }

    /**
     * Get esCampoLlave
     *
     * @return boolean 
     */
    public function getEsCampoLlave()
    {
        return $this->esCampoLlave;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return DatIndicadores
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
