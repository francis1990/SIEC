<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 23/11/2016
 * Time: 14:36
 */

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use NomencladorBundle\Entity\NomUeb;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomAseguramiento;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatParteAseguramiento
 *
 * @ORM\Table(name="dat_parte_aseguramiento")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteAseguramientoRepository")
 * @UniqueEntity(fields= {"fecha", "ueb", "materiaprima"}, message="Existe un elemento con fecha, UEB y aseguramiento seleccionado.")
 */
class DatParteAseguramiento extends DatParte{

    /**
     * @var NomAseguramiento
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomAseguramiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="materiaprima", referencedColumnName="idaseguramiento")
     * })
     */
    private $materiaprima;

    /**
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="um", referencedColumnName="idunidadmedida")
     * })
     */
    private $um;

    /**
     * @var float
     *
     * @ORM\Column(name="existencia", type="float", nullable=true)
     */
    private $existencia;

    /**
     * @var float
     *
     * @ORM\Column(name="entrada", type="float", nullable=true)
     */
    private $entrada;

    /**
     * @var float
     *
     * @ORM\Column(name="reserva", type="float", nullable=true)
     */
    private $reserva;

    /**
     * @var float
     *
     * @ORM\Column(name="demanda", type="float", nullable=true)
     */
    private $demanda;

    /**
     * @var float
     *
     * @ORM\Column(name="cobertura", type="float", nullable=true)
     */
    private $cobertura;


    /**
     * Set existencia
     *
     * @param float $existencia
     * @return DatParteAseguramiento
     */
    public function setExistencia($existencia)
    {
        $this->existencia = $existencia;

        return $this;
    }

    /**
     * Get existencia
     *
     * @return float 
     */
    public function getExistencia()
    {
        return $this->existencia;
    }

    /**
     * Set entrada
     *
     * @param float $entrada
     * @return DatParteAseguramiento
     */
    public function setEntrada($entrada)
    {
        $this->entrada = $entrada;

        return $this;
    }

    /**
     * Get entrada
     *
     * @return float 
     */
    public function getEntrada()
    {
        return $this->entrada;
    }

    /**
     * Set reserva
     *
     * @param float $reserva
     * @return DatParteAseguramiento
     */
    public function setReserva($reserva)
    {
        $this->reserva = $reserva;

        return $this;
    }

    /**
     * Get reserva
     *
     * @return float 
     */
    public function getReserva()
    {
        return $this->reserva;
    }

    /**
     * Set cobertura
     *
     * @param float $cobertura
     * @return DatParteAseguramiento
     */
    public function setCobertura($cobertura)
    {
        $this->cobertura = $cobertura;

        return $this;
    }

    /**
     * Get cobertura
     *
     * @return float 
     */
    public function getCobertura()
    {
        return $this->cobertura;
    }

    /**
     * Set materiaprima
     *
     * @param \NomencladorBundle\Entity\NomAseguramiento $materiaprima
     * @return DatParteAseguramiento
     */
    public function setMateriaprima(\NomencladorBundle\Entity\NomAseguramiento $materiaprima = null)
    {
        $this->materiaprima = $materiaprima;

        return $this;
    }

    /**
     * Get materiaprima
     *
     * @return \NomencladorBundle\Entity\NomAseguramiento 
     */
    public function getMateriaprima()
    {
        return $this->materiaprima;
    }


    /**
     * Set um
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $um
     * @return DatParteAseguramiento
     */
    public function setUm(\NomencladorBundle\Entity\NomUnidadmedida $um = null)
    {
        $this->um = $um;

        return $this;
    }

    /**
     * Get um
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida 
     */
    public function getUm()
    {
        return $this->um;
    }

    /**
     * Set demanda
     *
     * @param float $demanda
     * @return DatParteAseguramiento
     */
    public function setDemanda($demanda)
    {
        $this->demanda = $demanda;

        return $this;
    }

    /**
     * Get demanda
     *
     * @return float 
     */
    public function getDemanda()
    {
        return $this->demanda;
    }

    public function getNombreEntidad()
    {
        return ' el parte de aseguramiento: ' . $this->getMateriaprima()->getNombre();
    }


}
