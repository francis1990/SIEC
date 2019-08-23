<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 20/01/2017
 * Time: 9:02
 */

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomCuentacontable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use NomencladorBundle\Entity\NomUeb;
use NomencladorBundle\Entity\NomMonedadestino;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * DatParteCuentasCobrar
 *
 * @ORM\Table(name="dat_parte_cuentascobrar")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteCuentasCobrarRepository")
 * @UniqueEntity(fields= {"factura"}, message="Existe un elemento con esta factura.")
 * @UniqueEntity(fields= {"fecha", "ueb", "idcuentacontable","cliente"}, message="Existe un elemento con fecha, UEB y cuenta seleccionado.")
 */
class DatParteCuentasCobrar extends DatParte
{

    /**
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\NomCuentacontable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcuentacontable", referencedColumnName="idcuentacontable")
     * })
     */
    private $idcuentacontable;

    /**
     * @var NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente", referencedColumnName="identidad")
     * })
     */
    private $cliente;

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float",  nullable=false, precision=10, scale=2)
     */
    private $valor;

    /**
     * @var integer
     *
     * @ORM\Column(name="diasvencido", type="integer",  nullable=true)
     */
    private $diasvencido;

    /**
     * @var float
     *
     * @ORM\Column(name="montovencido", type="float",  nullable=true)
     */
    private $montovencido;

    /**
     * @var integer
     *
     * @ORM\Column(name="factura", type="string",length=50, nullable=true)
     */
    private $factura;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_reclama", type="date", nullable=true)
     */
    private $fecha_reclama;

    /**
     * @var NomMonedadestino
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomMonedadestino")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="moneda", referencedColumnName="id")
     * })
     */
    private $moneda;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_pagada", type="date", nullable=true)
     */
    private $fecha_pagada;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechadocumento", type="date", nullable=true)
     */
    private $fechadocumento;



    /**
     * Set valor
     *
     * @param float $valor
     * @return DatParteCuentasCobrar
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set diasvencido
     *
     * @param integer $diasvencido
     * @return DatParteCuentasCobrar
     */
    public function setDiasvencido($diasvencido)
    {
        $this->diasvencido = $diasvencido;

        return $this;
    }

    /**
     * Get diasvencido
     *
     * @return integer 
     */
    public function getDiasvencido()
    {
        return $this->diasvencido;
    }

    /**
     * Set montovencido
     *
     * @param float $montovencido
     * @return DatParteCuentasCobrar
     */
    public function setMontovencido($montovencido)
    {
        $this->montovencido = $montovencido;

        return $this;
    }

    /**
     * Get montovencido
     *
     * @return float 
     */
    public function getMontovencido()
    {
        return $this->montovencido;
    }

    /**
     * Set factura
     *
     * @param string $factura
     * @return DatParteCuentasCobrar
     */
    public function setFactura($factura)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return string 
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * Set fecha_reclama
     *
     * @param \DateTime $fechaReclama
     * @return DatParteCuentasCobrar
     */
    public function setFechaReclama($fechaReclama)
    {
        $this->fecha_reclama = $fechaReclama;

        return $this;
    }

    /**
     * Get fecha_reclama
     *
     * @return \DateTime 
     */
    public function getFechaReclama()
    {
        return $this->fecha_reclama;
    }

    /**
     * Set fecha_pagada
     *
     * @param \DateTime $fechaPagada
     * @return DatParteCuentasCobrar
     */
    public function setFechaPagada($fechaPagada)
    {
        $this->fecha_pagada = $fechaPagada;

        return $this;
    }

    /**
     * Get fecha_pagada
     *
     * @return \DateTime 
     */
    public function getFechaPagada()
    {
        return $this->fecha_pagada;
    }

    /**
     * Set idcuentacontable
     *
     * @param \NomencladorBundle\Entity\NomCuentacontable $idcuentacontable
     * @return DatParteCuentasCobrar
     */
    public function setIdcuentacontable(\NomencladorBundle\Entity\NomCuentacontable $idcuentacontable = null)
    {
        $this->idcuentacontable = $idcuentacontable;

        return $this;
    }

    /**
     * Get idcuentacontable
     *
     * @return \NomencladorBundle\Entity\NomCuentacontable 
     */
    public function getIdcuentacontable()
    {
        return $this->idcuentacontable;
    }


    /**
     * Set cliente
     *
     * @param \NomencladorBundle\Entity\NomEntidad $cliente
     * @return DatParteCuentasCobrar
     */
    public function setCliente(\NomencladorBundle\Entity\NomEntidad $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \NomencladorBundle\Entity\NomEntidad 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set moneda
     *
     * @param \NomencladorBundle\Entity\NomMonedadestino $moneda
     * @return DatParteCuentasCobrar
     */
    public function setMoneda(\NomencladorBundle\Entity\NomMonedadestino $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda
     *
     * @return \NomencladorBundle\Entity\NomMonedadestino
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    public function getNombreEntidad()
    {
        return ' el parte de cuentas por cobrar: Factura: ' . $this->factura." - Cliente: ".$this->getCliente()->getNombre();
    }

    /**
     * @Assert\IsTrue(message = "La fecha de reclamación debe ser menor o igual a la fecha del parte")
     */
    public function isFechaReclamacionValida(){
      return$this->getFecha()>=$this->getFechaReclama();
    }



    /**
     * Set fechadocumento
     *
     * @param \DateTime $fechadocumento
     * @return DatParteCuentasCobrar
     */
    public function setFechadocumento($fechadocumento)
    {
        $this->fechadocumento = $fechadocumento;
    
        return $this;
    }

    /**
     * Get fechadocumento
     *
     * @return \DateTime 
     */
    public function getFechadocumento()
    {
        return $this->fechadocumento;
    }
}
