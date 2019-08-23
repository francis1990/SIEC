<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatPartePortadorMedidor
 *
 * @ORM\Table(name="dat_parte_portador_medidor")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatPartePortadorMedidorRepository")
 */
class DatPartePortadorMedidor
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
     * @var
     * @ORM\ManyToOne(targetEntity="ParteDiarioBundle\Entity\DatPartePortador", inversedBy="listMedidor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(referencedColumnName="idparte")
     * })
     */
    private $parte;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\NomMedidor")
     *
     */
    private $medidor;

    /**
     * @var float
     *
     * @ORM\Column(name="lectura", type="float")
     */
    private $lectura;

    /**
     * @var float
     *
     * @ORM\Column(name="multiplicador", type="float", nullable=true)
     */
    private $multiplicador;

    /**
     * @var float
     *
     * @ORM\Column(name="consumo", type="float")
     */
    private $consumo;


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
     * Set lectura
     *
     * @param float $lectura
     * @return DatPartePortadorMedidor
     */
    public function setLectura($lectura)
    {
        $this->lectura = $lectura;
    
        return $this;
    }

    /**
     * Get lectura
     *
     * @return float 
     */
    public function getLectura()
    {
        return $this->lectura;
    }

    /**
     * Set multiplicador
     *
     * @param float $multiplicador
     * @return DatPartePortadorMedidor
     */
    public function setMultiplicador($multiplicador)
    {
        $this->multiplicador = $multiplicador;
    
        return $this;
    }

    /**
     * Get multiplicador
     *
     * @return float 
     */
    public function getMultiplicador()
    {
        return $this->multiplicador;
    }

    /**
     * Set consumo
     *
     * @param float $consumo
     * @return DatPartePortadorMedidor
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
     * Set parte
     *
     * @param \ParteDiarioBundle\Entity\DatPartePortador $parte
     * @return DatPartePortadorMedidor
     */
    public function setParte(\ParteDiarioBundle\Entity\DatPartePortador $parte = null)
    {
        $this->parte = $parte;
    
        return $this;
    }

    /**
     * Get parte
     *
     * @return \ParteDiarioBundle\Entity\DatPartePortador 
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * Set medidor
     *
     * @param \NomencladorBundle\Entity\NomMedidor $medidor
     * @return DatPartePortadorMedidor
     */
    public function setMedidor(\NomencladorBundle\Entity\NomMedidor $medidor = null)
    {
        $this->medidor = $medidor;
    
        return $this;
    }

    /**
     * Get medidor
     *
     * @return \NomencladorBundle\Entity\NomMedidor 
     */
    public function getMedidor()
    {
        return $this->medidor;
    }
}
