<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatNormaAseguramiento
 *
 * @ORM\Table(name="dat_norma_aseguramiento")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\DatNormaAseguramientoRepository")
 */
class DatNormaAseguramiento
{
    /**
     * @var int
     *
     * @ORM\Column(name="idnormaaseg", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idnormaaseg;

    /**
     * @var NomNorma
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomNorma", inversedBy="aseguramientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="norma", referencedColumnName="idnorma", onDelete="cascade")
     * })
     */
    private $norma;

    /**
     * @var NomAseguramiento
     *
     * @ORM\ManyToOne(targetEntity="NomAseguramiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aseguramiento", referencedColumnName="idaseguramiento")
     * })
     */
    private $aseguramiento;

    /**
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="umaseg", referencedColumnName="idunidadmedida")
     * })
     */
    private $umaseg;

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
     * @var float
     *
     * @ORM\Column(name="cantaseg", type="float",nullable=true)
     */
    private $cantaseg;

    /**
     * @var float
     *
     * @ORM\Column(name="norma_neta", type="float",nullable=true)
     */
    private $normaneta;

    /**
     * @var float
     *
     * @ORM\Column(name="perdida", type="float",nullable=true)
     */
    private $perdida;

    /**
     * @var float
     *
     * @ORM\Column(name="cantaseg_grasa", type="float",nullable=true)
     */
    private $cantaseg_grasa;

    /**
     * @var float
     *
     * @ORM\Column(name="norma_neta_grasa", type="float",nullable=true)
     */
    private $normaneta_grasa;

    /**
     * @var float
     *
     * @ORM\Column(name="perdida_grasa", type="float",nullable=true)
     */
    private $perdida_grasa;

    /**
     * @var float
     *
     * @ORM\Column(name="cantaseg_sng", type="float",nullable=true)
     */
    private $cantaseg_sng;

    /**
     * @var float
     *
     * @ORM\Column(name="norma_neta_sng", type="float",nullable=true)
     */
    private $normaneta_sng;

    /**
     * @var float
     *
     * @ORM\Column(name="perdida_sng", type="float",nullable=true)
     */
    private $perdida_sng;


    /**
     * Get idnormaaseg
     *
     * @return integer
     */
    public function getIdnormaaseg()
    {
        return $this->idnormaaseg;
    }

    /**
     * Set idnormaaseg
     *
     * @param integer $idnormaaseg
     * @return DatNormaAseguramiento
     */
    public function setIdnormaaseg($idnormaaseg)
    {
        $this->idnormaaseg = $idnormaaseg;
        return $this;
    }

    /**
     * Set norma
     *
     * @param \NomencladorBundle\Entity\NomNorma $norma
     * @return DatNormaAseguramiento
     */
    public function setNorma(\NomencladorBundle\Entity\NomNorma $norma = null)
    {
        $this->norma = $norma;

        return $this;
    }

    /**
     * Get norma
     *
     * @return \NomencladorBundle\Entity\NomNorma
     */
    public function getNorma()
    {
        return $this->norma;
    }

    /**
     * Set aseguramiento
     *
     * @param \NomencladorBundle\Entity\NomAseguramiento $aseguramiento
     * @return DatNormaAseguramiento
     */
    public function setAseguramiento(\NomencladorBundle\Entity\NomAseguramiento $aseguramiento = null)
    {
        $this->aseguramiento = $aseguramiento;

        return $this;
    }

    /**
     * Get aseguramiento
     *
     * @return \NomencladorBundle\Entity\NomAseguramiento
     */
    public function getAseguramiento()
    {
        return $this->aseguramiento;
    }

    /**
     * Set umaseg
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $umaseg
     * @return DatNormaAseguramiento
     */
    public function setUmaseg(\NomencladorBundle\Entity\NomUnidadmedida $umaseg = null)
    {
        $this->umaseg = $umaseg;

        return $this;
    }

    /**
     * Get umaseg
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getUmaseg()
    {
        return $this->umaseg;
    }

    /**
     * Set moneda
     *
     * @param \NomencladorBundle\Entity\NomMonedadestino $moneda
     * @return DatNormaAseguramiento
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

    public function __toString()
    {
        return 'hfghfdj';
    }

    /**
     * Set normaneta
     *
     * @param float $normaneta
     * @return DatNormaAseguramiento
     */
    public function setNormaneta($normaneta)
    {
        $this->normaneta = $normaneta;

        return $this;
    }

    /**
     * Get normaneta
     *
     * @return float
     */
    public function getNormaneta()
    {
        return $this->normaneta;
    }

    /**
     * Set perdida
     *
     * @param float $perdida
     * @return DatNormaAseguramiento
     */
    public function setPerdida($perdida)
    {
        $this->perdida = $perdida;

        return $this;
    }

    /**
     * Get perdida
     *
     * @return float
     */
    public function getPerdida()
    {
        return $this->perdida;
    }

    /**
     * Set cantaseg
     *
     * @param float $cantaseg
     * @return DatNormaAseguramiento
     */
    public function setCantaseg($cantaseg)
    {
        $this->cantaseg = $cantaseg;

        return $this;
    }

    /**
     * Get cantaseg
     *
     * @return float
     */
    public function getCantaseg()
    {
        return $this->cantaseg;
    }

    /**
     * Set cantaseg_grasa
     *
     * @param float $cantasegGrasa
     * @return DatNormaAseguramiento
     */
    public function setCantasegGrasa($cantasegGrasa)
    {
        $this->cantaseg_grasa = $cantasegGrasa;
    
        return $this;
    }

    /**
     * Get cantaseg_grasa
     *
     * @return float 
     */
    public function getCantasegGrasa()
    {
        return $this->cantaseg_grasa;
    }

    /**
     * Set normaneta_grasa
     *
     * @param float $normanetaGrasa
     * @return DatNormaAseguramiento
     */
    public function setNormanetaGrasa($normanetaGrasa)
    {
        $this->normaneta_grasa = $normanetaGrasa;
    
        return $this;
    }

    /**
     * Get normaneta_grasa
     *
     * @return float 
     */
    public function getNormanetaGrasa()
    {
        return $this->normaneta_grasa;
    }

    /**
     * Set perdida_grasa
     *
     * @param float $perdidaGrasa
     * @return DatNormaAseguramiento
     */
    public function setPerdidaGrasa($perdidaGrasa)
    {
        $this->perdida_grasa = $perdidaGrasa;
    
        return $this;
    }

    /**
     * Get perdida_grasa
     *
     * @return float 
     */
    public function getPerdidaGrasa()
    {
        return $this->perdida_grasa;
    }

    /**
     * Set cantaseg_sng
     *
     * @param float $cantasegSng
     * @return DatNormaAseguramiento
     */
    public function setCantasegSng($cantasegSng)
    {
        $this->cantaseg_sng = $cantasegSng;
    
        return $this;
    }

    /**
     * Get cantaseg_sng
     *
     * @return float 
     */
    public function getCantasegSng()
    {
        return $this->cantaseg_sng;
    }

    /**
     * Set normaneta_sng
     *
     * @param float $normanetaSng
     * @return DatNormaAseguramiento
     */
    public function setNormanetaSng($normanetaSng)
    {
        $this->normaneta_sng = $normanetaSng;
    
        return $this;
    }

    /**
     * Get normaneta_sng
     *
     * @return float 
     */
    public function getNormanetaSng()
    {
        return $this->normaneta_sng;
    }

    /**
     * Set perdida_sng
     *
     * @param float $perdidaSng
     * @return DatNormaAseguramiento
     */
    public function setPerdidaSng($perdidaSng)
    {
        $this->perdida_sng = $perdidaSng;
    
        return $this;
    }

    /**
     * Get perdida_sng
     *
     * @return float 
     */
    public function getPerdidaSng()
    {
        return $this->perdida_sng;
    }
}
