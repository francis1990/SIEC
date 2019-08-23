<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatReportesFilasResumen
 *
 * @ORM\Table(name="rep_reportes_filas_resumen")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatReportesFilasResumenRepository")
 */
class DatReportesFilasResumen
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
     * @ORM\Column(name="id_reportesfilas", type="integer")
     */
    private $idReportesfilas;

    /**
     * @var int
     *
     * @ORM\Column(name="nivel", type="integer")
     */
    private $nivel;

    /**
     * @var int
     *
     * @ORM\Column(name="col_datos", type="integer")
     */
    private $colDatos;

    /**
     * @var int
     *
     * @ORM\Column(name="funcion_resumen", type="integer")
     */
    private $funcionResumen;

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
     * Set idReportesfilas
     *
     * @param integer $idReportesfilas
     * @return DatReportesFilasResumen
     */
    public function setIdReportesfilas($idReportesfilas)
    {
        $this->idReportesfilas = $idReportesfilas;
    
        return $this;
    }

    /**
     * Get idReportesfilas
     *
     * @return integer 
     */
    public function getIdReportesfilas()
    {
        return $this->idReportesfilas;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return DatReportesFilasResumen
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;
    
        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set colDatos
     *
     * @param integer $colDatos
     * @return DatReportesFilasResumen
     */
    public function setColDatos($colDatos)
    {
        $this->colDatos = $colDatos;
    
        return $this;
    }

    /**
     * Get colDatos
     *
     * @return integer 
     */
    public function getColDatos()
    {
        return $this->colDatos;
    }

    /**
     * Set funcionResumen
     *
     * @param integer $funcionResumen
     * @return DatReportesFilasResumen
     */
    public function setFuncionResumen($funcionResumen)
    {
        $this->funcionResumen = $funcionResumen;
    
        return $this;
    }

    /**
     * Get funcionResumen
     *
     * @return integer 
     */
    public function getFuncionResumen()
    {
        return $this->funcionResumen;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return DatReportesFilasResumen
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
