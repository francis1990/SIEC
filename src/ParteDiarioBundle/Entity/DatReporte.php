<?php

namespace  ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatReporte
 *
 * @ORM\Table(name="dat_reporte")
 * @ORM\Entity
 */
class DatReporte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idreporte", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idreporte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="observación", type="string", length=500, nullable=false)
     */
    private $observación;
    /**
     * @var string
     *
     * @ORM\Column(name="aprueba", type="string", length=500, nullable=false)
     */
    private $aprueba;
    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="\AdminBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUsuario", referencedColumnName="idUsuario")
     * })
     */
    private $usuarios;


    /**
     * Get idreporte
     *
     * @return integer
     */
    public function getIdreporte()
    {
        return $this->idreporte;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return DatReporte
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
     * Set observación
     *
     * @param string $observación
     * @return DatReporte
     */
    public function setObservación($observación)
    {
        $this->observación = $observación;

        return $this;
    }

    /**
     * Get observación
     *
     * @return string 
     */
    public function getObservación()
    {
        return $this->observación;
    }

    /**
     * Set aprueba
     *
     * @param string $aprueba
     * @return DatReporte
     */
    public function setAprueba($aprueba)
    {
        $this->aprueba = $aprueba;

        return $this;
    }

    /**
     * Get aprueba
     *
     * @return string 
     */
    public function getAprueba()
    {
        return $this->aprueba;
    }

    /**
     * Set usuarios
     *
     * @param \AdminBundle\Entity\Usuario $usuarios
     * @return DatReporte
     */
    public function setUsuarios(\AdminBundle\Entity\Usuario $usuarios = null)
    {
        $this->usuarios = $usuarios;

        return $this;
    }

    /**
     * Get usuarios
     *
     * @return \AdminBundle\Entity\Usuario 
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }
}
