<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 23/11/2016
 * Time: 14:37
 */

namespace ParteDiarioBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomPortador;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatPartePortador
 *
 * @ORM\Table(name="dat_parte_portador")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatPartePortadorRepository")
 * @UniqueEntity(fields= {"fecha", "ueb", "portador"}, message="Existe un elemento con fecha, UEB y portador seleccionado.")
 */
class DatPartePortador extends DatParte {

    /**
     * @var NomPortador
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\NomPortador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portador", referencedColumnName="idportador")
     * })
     */
    private $portador;

    /**
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="um", referencedColumnName="idunidadmedida")
     * })
     */
    private $um;

    /**
     * @var float
     *
     * @ORM\Column(name="consumo", type="float",  nullable=true, precision=10, scale=3)
     */
    private $consumo;

    /**
     * @var string
     *
     * @ORM\Column(name="pico", type="float",  nullable=true)
     */
    private $pico;

    /**
     * @var string
     *
     * @ORM\Column(name="madrugada", type="float",  nullable=true)
     */
    private $madrugada;

    /**
     * @var float
     *
     * @ORM\Column(name="alcance", type="float",  nullable=true)
     */
    private $alcance;

    /**
     * @var float
     *
     * @ORM\Column(name="inventario", type="float",  nullable=true)
     */
    private $inventario;
    /**
     * @var float
     *
     * @ORM\Column(name="existencia", type="float",  nullable=true)
     */
    private $existencia;
    /**
     * @var float
     *
     * @ORM\Column(name="entrada", type="float",  nullable=true)
     */
    private $entrada;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="ParteDiarioBundle\Entity\DatPartePortadorMedidor", mappedBy="parte", cascade={"persist", "remove"})
     * @Assert\Count(min=1, minMessage="Debe agregar al menos 1 medidor")
     */
    private $listMedidor;

    /**
     * Set consumo
     *
     * @param float $consumo
     * @return DatPartePortador
     */
    public function setConsumo($consumo)
    {
        $this->consumo = $consumo;
    
        return $this;
    }

    /**
     * Get consumo
     *
     * @return float 
     */
    public function getConsumo()
    {
        return $this->consumo;
    }

    /**
     * Set pico
     *
     * @param string $pico
     * @return DatPartePortador
     */
    public function setPico($pico)
    {
        $this->pico = $pico;
    
        return $this;
    }

    /**
     * Get pico
     *
     * @return string 
     */
    public function getPico()
    {
        return $this->pico;
    }

    /**
     * Set madrugada
     *
     * @param string $madrugada
     * @return DatPartePortador
     */
    public function setMadrugada($madrugada)
    {
        $this->madrugada = $madrugada;
    
        return $this;
    }

    /**
     * Get madrugada
     *
     * @return string 
     */
    public function getMadrugada()
    {
        return $this->madrugada;
    }

    /**
     * Set alcance
     *
     * @param float $alcance
     * @return DatPartePortador
     */
    public function setAlcance($alcance)
    {
        $this->alcance = $alcance;
    
        return $this;
    }

    /**
     * Get alcance
     *
     * @return float 
     */
    public function getAlcance()
    {
        return $this->alcance;
    }

    /**
     * Set inventario
     *
     * @param float $inventario
     * @return DatPartePortador
     */
    public function setInventario($inventario)
    {
        $this->inventario = $inventario;
    
        return $this;
    }

    /**
     * Get inventario
     *
     * @return float 
     */
    public function getInventario()
    {
        return $this->inventario;
    }

    /**
     * Set existencia
     *
     * @param float $existencia
     * @return DatPartePortador
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
     * @return DatPartePortador
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
     * Set portador
     *
     * @param \NomencladorBundle\Entity\NomPortador $portador
     * @return DatPartePortador
     */
    public function setPortador(\NomencladorBundle\Entity\NomPortador $portador = null)
    {
        $this->portador = $portador;
    
        return $this;
    }

    /**
     * Get portador
     *
     * @return \NomencladorBundle\Entity\NomPortador 
     */
    public function getPortador()
    {
        return $this->portador;
    }

    /**
     * Set um
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $um
     * @return DatPartePortador
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
     * Add listMedidor
     *
     * @param \ParteDiarioBundle\Entity\DatPartePortadorMedidor $listMedidor
     * @return DatPartePortador
     */
    public function addListMedidor(\ParteDiarioBundle\Entity\DatPartePortadorMedidor $listMedidor)
    {
        $listMedidor->setParte($this);
        $this->listMedidor[] = $listMedidor;
    
        return $this;
    }

    /**
     * Remove listMedidor
     *
     * @param \ParteDiarioBundle\Entity\DatPartePortadorMedidor $listMedidor
     */
    public function removeListMedidor(\ParteDiarioBundle\Entity\DatPartePortadorMedidor $listMedidor)
    {
        $this->listMedidor->removeElement($listMedidor);
    }

    /**
     * Get listMedidor
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getListMedidor()
    {
        return $this->listMedidor;
    }

    public function getNombreEntidad()
    {
        return ' el parte de Portadores energéticos: Portador energético: ' . $this->getPortador()->getNombre();
    }
}
