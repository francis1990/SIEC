<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatColumnasListasDatos
 *
 * @ORM\Table(name="rep_columnas_listas_datos")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatColumnasListasDatosRepository")
 */
class DatColumnasListasDatos
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
     * @ORM\Column(name="id_listasdatos", type="integer")
     */
    private $idListasdatos;

    /**
     * @var int
     *
     * @ORM\Column(name="id_indicador", type="integer")
     */
    private $idIndicador;

    /**
     * @var bool
     *
     * @ORM\Column(name="seleccionada", type="boolean")
     */
    private $seleccionada;

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
     * Set idListasdatos
     *
     * @param integer $idListasdatos
     * @return DatColumnasListasDatos
     */
    public function setIdListasdatos($idListasdatos)
    {
        $this->idListasdatos = $idListasdatos;
    
        return $this;
    }

    /**
     * Get idListasdatos
     *
     * @return integer 
     */
    public function getIdListasdatos()
    {
        return $this->idListasdatos;
    }

    /**
     * Set idIndicador
     *
     * @param integer $idIndicador
     * @return DatColumnasListasDatos
     */
    public function setIdIndicador($idIndicador)
    {
        $this->idIndicador = $idIndicador;
    
        return $this;
    }

    /**
     * Get idIndicador
     *
     * @return integer 
     */
    public function getIdIndicador()
    {
        return $this->idIndicador;
    }

    /**
     * Set seleccionada
     *
     * @param boolean $seleccionada
     * @return DatColumnasListasDatos
     */
    public function setSeleccionada($seleccionada)
    {
        $this->seleccionada = $seleccionada;
    
        return $this;
    }

    /**
     * Get seleccionada
     *
     * @return boolean 
     */
    public function getSeleccionada()
    {
        return $this->seleccionada;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     * @return DatColumnasListasDatos
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
     * @return DatColumnasListasDatos
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
