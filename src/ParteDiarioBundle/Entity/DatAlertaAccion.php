<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DatAlertaAccion
 *
 * @ORM\Table(name="dat_alerta_accion")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatAlertaAccionRepository")
 */
class DatAlertaAccion
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
     * @var \ParteDiarioBundle\Entity\DatAlerta
     *
     * @ORM\ManyToOne(targetEntity="\ParteDiarioBundle\Entity\DatAlerta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idalerta", referencedColumnName="idalerta")
     * })
     */
    private $alerta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date",nullable=true)
     */
    private $fecha;

    /**
     * @var \AdminBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="\AdminBundle\Entity\Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario", referencedColumnName="idUsuario")
     * })
     */
    private $usuario;


    /**
     * @var \NomencladorBundle\Entity\NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idueb", referencedColumnName="idueb")
     * })
     * @Assert\NotNull(message = "Debe seleccionar un elemento.")
     */
    private $ueb;


    /**
     * @var string
     *
     * @ORM\Column(name="actividad", type="string", length=255)
     */
    private $actividad;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="string", length=255)
     */
    private $accion;

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
     * Set actividad
     *
     * @param string $actividad
     * @return DatAlertaAccion
     */
    public function setActividad($actividad)
    {
        $this->actividad = $actividad;
    
        return $this;
    }

    /**
     * Get actividad
     *
     * @return string 
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return DatAlertaAccion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set alerta
     *
     * @param \ParteDiarioBundle\Entity\DatAlerta $alerta
     * @return DatAlertaAccion
     */
    public function setAlerta(\ParteDiarioBundle\Entity\DatAlerta $alerta = null)
    {
        $this->alerta = $alerta;
    
        return $this;
    }

    /**
     * Get alerta
     *
     * @return \ParteDiarioBundle\Entity\DatAlerta 
     */
    public function getAlerta()
    {
        return $this->alerta;
    }

    /**
     * Set usuario
     *
     * @param \AdminBundle\Entity\Usuario $usuario
     * @return DatAlertaAccion
     */
    public function setUsuario(\AdminBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AdminBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set ueb
     *
     * @param \NomencladorBundle\Entity\NomUeb $ueb
     * @return DatAlertaAccion
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return DatAlertaAccion
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
     * Set accion
     *
     * @param string $accion
     * @return DatAlertaAccion
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
    
        return $this;
    }

    /**
     * Get accion
     *
     * @return string 
     */
    public function getAccion()
    {
        return $this->accion;
    }
}
