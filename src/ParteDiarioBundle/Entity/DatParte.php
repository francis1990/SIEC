<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ParteDiarioBundle\Entity\DatIncidencia;
use NomencladorBundle\Entity\NomUeb;


/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="tipop", type="string")
 * @ORM\DiscriminatorMap({"parte" = "DatParte", "portador" = "DatPartePortador", "transporte"="DatParteTransporte",
 * "venta"="DatParteVenta","movimiento"="DatParteMovimiento","mercancia"="DatParteMercanciaVinculo",
 * "produccion"="DatPartediarioProduccion", "economia"="DatPartediarioEconomia", "aseguramiento"="DatParteAseguramiento",
 * "cuentas"="DatParteCuentasCobrar","acopio"="DatParteAcopio","desvio"="DatParteDesvio","consumo"="DatParteDiarioConsAseg"})
 */
class DatParte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idparte", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private  $idparte;

    /**
     * @var NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ueb", referencedColumnName="idueb")
     * })
     */
    private $ueb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var \ParteDiarioBundle\Entity\DatIncidencia
     *
     * @ORM\OneToMany(targetEntity="ParteDiarioBundle\Entity\DatIncidencia",  cascade={"persist", "remove"}, mappedBy="parte", orphanRemoval=true)
     *   @ORM\JoinColumn(referencedColumnName="idincidencia")
     */
    private $incidencias;


    /**
     * Get idparte
     *
     * @return integer 
     */
    public function getIdparte()
    {
        return $this->idparte;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return DatParte
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set ueb
     *
     * @param \NomencladorBundle\Entity\NomUeb $ueb
     * @return DatParte
     */
    public function setUeb(\NomencladorBundle\Entity\NomUeb $ueb = null)
    {
        $this->ueb = $ueb;
    
        return $this;
    }

    /**
     * Get ueb
     *
     * @return \NomencladorBundle\Entity\NomUeb 
     */
    public function getUeb()
    {
        return $this->ueb;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->incidencias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add incidencias
     *
     * @param \ParteDiarioBundle\Entity\DatIncidencia $incidencias
     * @return DatParte
     */
    public function addIncidencia(\ParteDiarioBundle\Entity\DatIncidencia $incidencias)
    {
        $incidencias->setParte($this);
        $incidencias->setFecha($this->fecha);
        $incidencias->setEntidad($this->ueb);
        $this->incidencias[] = $incidencias;
    
        return $this;
    }

    /**
     * Remove incidencias
     *
     * @param \ParteDiarioBundle\Entity\DatIncidencia $incidencias
     */
    public function removeIncidencia(\ParteDiarioBundle\Entity\DatIncidencia $incidencias)
    {
        $this->incidencias->removeElement($incidencias);
    }

    /**
     * Get incidencias
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncidencias()
    {
        return $this->incidencias;
    }
}
