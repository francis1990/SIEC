<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatReportes
 *
 * @ORM\Table(name="rep_reportes")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatReportesRepository")
 */
class DatReportes
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
     * @ORM\Column(name="id_tiporeporte", type="integer")
     */
    private $idTiporeporte;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=100)
     */
    private $descripcion;

    /**
     * @var int
     *
     * @ORM\Column(name="cant_columnas", type="integer")
     */
    private $cantColumnas;

    /**
     * @var array
     *
     * @ORM\Column(name="mostrar_filas_encabezado", type="json_array")
     */
    private $mostrarFilasEncabezado;

    /**
     * @var bool
     *
     * @ORM\Column(name="es_libre", type="boolean", nullable=true)
     */
    private $esLibre;

    /**
     * @var int
     *
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;

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
     * Set idTiporeporte
     *
     * @param integer $idTiporeporte
     * @return DatReportes
     */
    public function setIdTiporeporte($idTiporeporte)
    {
        $this->idTiporeporte = $idTiporeporte;
    
        return $this;
    }

    /**
     * Get idTiporeporte
     *
     * @return integer 
     */
    public function getIdTiporeporte()
    {
        return $this->idTiporeporte;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return DatReportes
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return DatReportes
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
     * Set cantColumnas
     *
     * @param integer $cantColumnas
     * @return DatReportes
     */
    public function setCantColumnas($cantColumnas)
    {
        $this->cantColumnas = $cantColumnas;
    
        return $this;
    }

    /**
     * Get cantColumnas
     *
     * @return integer 
     */
    public function getCantColumnas()
    {
        return $this->cantColumnas;
    }

    /**
     * Set mostrarFilasEncabezado
     *
     * @param array $mostrarFilasEncabezado
     * @return DatReportes
     */
    public function setMostrarFilasEncabezado($mostrarFilasEncabezado)
    {
        $this->mostrarFilasEncabezado = $mostrarFilasEncabezado;
    
        return $this;
    }

    /**
     * Get mostrarFilasEncabezado
     *
     * @return array 
     */
    public function getMostrarFilasEncabezado()
    {
        return $this->mostrarFilasEncabezado;
    }

    /**
     * Set esLibre
     *
     * @param boolean $esLibre
     * @return DatReportes
     */
    public function setEsLibre($esLibre)
    {
        $this->esLibre = $esLibre;
    
        return $this;
    }

    /**
     * Get esLibre
     *
     * @return boolean 
     */
    public function getEsLibre()
    {
        return $this->esLibre;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     * @return DatReportes
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return DatReportes
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
