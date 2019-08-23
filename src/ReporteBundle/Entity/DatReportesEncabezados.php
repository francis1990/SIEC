<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatReportesEncabezados
 *
 * @ORM\Table(name="rep_reportes_encabezados")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatReportesEncabezadosRepository")
 */
class DatReportesEncabezados
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
     * @ORM\Column(name="id_reporte", type="integer")
     */
    private $idReporte;

    /**
     * @var int
     *
     * @ORM\Column(name="id_columnaslistas_datos", type="integer")
     */
    private $idColumnaslistasDatos;

    /**
     * @var bool
     *
     * @ORM\Column(name="utilizar_nombre_indicador", type="boolean")
     */
    private $utilizarNombreIndicador;

    /**
     * @var bool
     *
     * @ORM\Column(name="rotulo", type="boolean")
     */
    private $rotulo;

    /**
     * @var bool
     *
     * @ORM\Column(name="para_filas_izquierda", type="boolean")
     */
    private $paraFilasIzquierda;

    /**
     * @var int
     *
     * @ORM\Column(name="fila", type="integer")
     */
    private $fila;

    /**
     * @var int
     *
     * @ORM\Column(name="col", type="integer")
     */
    private $col;

    /**
     * @var int
     *
     * @ORM\Column(name="col_spam", type="integer")
     */
    private $colSpam;

    /**
     * @var int
     *
     * @ORM\Column(name="row_spam", type="integer")
     */
    private $rowSpam;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_campo_UM", type="string", length=100)
     */
    private $nombreCampoUM;

    /**
     * @var bool
     *
     * @ORM\Column(name="mostrar_um", type="boolean")
     */
    private $mostrarUm;

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
     * Set idReporte
     *
     * @param integer $idReporte
     * @return DatReportesEncabezados
     */
    public function setIdReporte($idReporte)
    {
        $this->idReporte = $idReporte;
    
        return $this;
    }

    /**
     * Get idReporte
     *
     * @return integer 
     */
    public function getIdReporte()
    {
        return $this->idReporte;
    }

    /**
     * Set idColumnaslistasDatos
     *
     * @param integer $idColumnaslistasDatos
     * @return DatReportesEncabezados
     */
    public function setIdColumnaslistasDatos($idColumnaslistasDatos)
    {
        $this->idColumnaslistasDatos = $idColumnaslistasDatos;
    
        return $this;
    }

    /**
     * Get idColumnaslistasDatos
     *
     * @return integer 
     */
    public function getIdColumnaslistasDatos()
    {
        return $this->idColumnaslistasDatos;
    }

    /**
     * Set utilizarNombreIndicador
     *
     * @param boolean $utilizarNombreIndicador
     * @return DatReportesEncabezados
     */
    public function setUtilizarNombreIndicador($utilizarNombreIndicador)
    {
        $this->utilizarNombreIndicador = $utilizarNombreIndicador;
    
        return $this;
    }

    /**
     * Get utilizarNombreIndicador
     *
     * @return boolean 
     */
    public function getUtilizarNombreIndicador()
    {
        return $this->utilizarNombreIndicador;
    }

    /**
     * Set rotulo
     *
     * @param boolean $rotulo
     * @return DatReportesEncabezados
     */
    public function setRotulo($rotulo)
    {
        $this->rotulo = $rotulo;
    
        return $this;
    }

    /**
     * Get rotulo
     *
     * @return boolean 
     */
    public function getRotulo()
    {
        return $this->rotulo;
    }

    /**
     * Set paraFilasIzquierda
     *
     * @param boolean $paraFilasIzquierda
     * @return DatReportesEncabezados
     */
    public function setParaFilasIzquierda($paraFilasIzquierda)
    {
        $this->paraFilasIzquierda = $paraFilasIzquierda;
    
        return $this;
    }

    /**
     * Get paraFilasIzquierda
     *
     * @return boolean 
     */
    public function getParaFilasIzquierda()
    {
        return $this->paraFilasIzquierda;
    }

    /**
     * Set fila
     *
     * @param integer $fila
     * @return DatReportesEncabezados
     */
    public function setFila($fila)
    {
        $this->fila = $fila;
    
        return $this;
    }

    /**
     * Get fila
     *
     * @return integer 
     */
    public function getFila()
    {
        return $this->fila;
    }

    /**
     * Set col
     *
     * @param integer $col
     * @return DatReportesEncabezados
     */
    public function setCol($col)
    {
        $this->col = $col;
    
        return $this;
    }

    /**
     * Get col
     *
     * @return integer 
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * Set colSpam
     *
     * @param integer $colSpam
     * @return DatReportesEncabezados
     */
    public function setColSpam($colSpam)
    {
        $this->colSpam = $colSpam;
    
        return $this;
    }

    /**
     * Get colSpam
     *
     * @return integer 
     */
    public function getColSpam()
    {
        return $this->colSpam;
    }

    /**
     * Set rowSpam
     *
     * @param integer $rowSpam
     * @return DatReportesEncabezados
     */
    public function setRowSpam($rowSpam)
    {
        $this->rowSpam = $rowSpam;
    
        return $this;
    }

    /**
     * Get rowSpam
     *
     * @return integer 
     */
    public function getRowSpam()
    {
        return $this->rowSpam;
    }

    /**
     * Set nombreCampoUM
     *
     * @param string $nombreCampoUM
     * @return DatReportesEncabezados
     */
    public function setNombreCampoUM($nombreCampoUM)
    {
        $this->nombreCampoUM = $nombreCampoUM;
    
        return $this;
    }

    /**
     * Get nombreCampoUM
     *
     * @return string 
     */
    public function getNombreCampoUM()
    {
        return $this->nombreCampoUM;
    }

    /**
     * Set mostrarUm
     *
     * @param boolean $mostrarUm
     * @return DatReportesEncabezados
     */
    public function setMostrarUm($mostrarUm)
    {
        $this->mostrarUm = $mostrarUm;
    
        return $this;
    }

    /**
     * Get mostrarUm
     *
     * @return boolean 
     */
    public function getMostrarUm()
    {
        return $this->mostrarUm;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     * @return DatReportesEncabezados
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
     * @return DatReportesEncabezados
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
