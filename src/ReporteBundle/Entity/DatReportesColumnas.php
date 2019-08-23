<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatReportesColumnas
 *
 * @ORM\Table(name="rep_reportes_columnas")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatReportesColumnasRepository")
 */
class DatReportesColumnas
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
     * @ORM\Column(name="decimales", type="integer")
     */
    private $decimales;

    /**
     * @var string
     *
     * @ORM\Column(name="formula", type="string", length=100)
     */
    private $formula;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion_formula", type="string", length=100)
     */
    private $descripcionFormula;

    /**
     * @var bool
     *
     * @ORM\Column(name="replica_formula_x_filas", type="boolean")
     */
    private $replicaFormulaXFilas;

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
     * @return DatReportesColumnas
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
     * Set fila
     *
     * @param integer $fila
     * @return DatReportesColumnas
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
     * @return DatReportesColumnas
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
     * Set decimales
     *
     * @param integer $decimales
     * @return DatReportesColumnas
     */
    public function setDecimales($decimales)
    {
        $this->decimales = $decimales;
    
        return $this;
    }

    /**
     * Get decimales
     *
     * @return integer 
     */
    public function getDecimales()
    {
        return $this->decimales;
    }

    /**
     * Set formula
     *
     * @param string $formula
     * @return DatReportesColumnas
     */
    public function setFormula($formula)
    {
        $this->formula = $formula;
    
        return $this;
    }

    /**
     * Get formula
     *
     * @return string 
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Set descripcionFormula
     *
     * @param string $descripcionFormula
     * @return DatReportesColumnas
     */
    public function setDescripcionFormula($descripcionFormula)
    {
        $this->descripcionFormula = $descripcionFormula;
    
        return $this;
    }

    /**
     * Get descripcionFormula
     *
     * @return string 
     */
    public function getDescripcionFormula()
    {
        return $this->descripcionFormula;
    }

    /**
     * Set replicaFormulaXFilas
     *
     * @param boolean $replicaFormulaXFilas
     * @return DatReportesColumnas
     */
    public function setReplicaFormulaXFilas($replicaFormulaXFilas)
    {
        $this->replicaFormulaXFilas = $replicaFormulaXFilas;
    
        return $this;
    }

    /**
     * Get replicaFormulaXFilas
     *
     * @return boolean 
     */
    public function getReplicaFormulaXFilas()
    {
        return $this->replicaFormulaXFilas;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return DatReportesColumnas
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
