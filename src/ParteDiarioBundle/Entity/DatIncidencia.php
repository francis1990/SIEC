<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatIncidencia
 *
 * @ORM\Table(name="dat_incidencia")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatIncidenciaRepository")
 */
class DatIncidencia
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idincidencia", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idincidencia;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="ParteDiarioBundle\Entity\DatParte",inversedBy="incidencias")
     *   @ORM\JoinColumn(referencedColumnName="idparte", nullable=true , onDelete="cascade")
     */
    private $parte;

    /**
     * @var Date
     *
     * @ORM\Column(name="fecha", type="date")
     */
    private $fecha;

    /**
     * @var \NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idueb", referencedColumnName="idueb")
     * })
     */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=500, nullable=false)
     */
    private $descripcion;

     /**
     * @var \NomClasificacionIncidencia
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomClasificacionIncidencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcasificacion", referencedColumnName="id")
     * })
     */
    private $idcasificacion;

    /**
     * @var \NomTipoIncidencia
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomTipoIncidencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtipo", referencedColumnName="idtipoincidencia")
     * })
     */
    private $idtipo;


    /**
     * Get idincidencia
     *
     * @return integer 
     */
    public function getIdincidencia()
    {
        return $this->idincidencia;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->descripcion;
    }

    /**
     * @return Date
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param Date $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return DatIncidencia
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
     * Set idcasificacion
     *
     * @param \NomencladorBundle\Entity\NomClasificacionIncidencia $idcasificacion
     * @return DatIncidencia
     */
    public function setIdcasificacion(\NomencladorBundle\Entity\NomClasificacionIncidencia $idcasificacion = null)
    {
        $this->idcasificacion = $idcasificacion;

        return $this;
    }

    /**
     * Get idcasificacion
     *
     * @return \NomencladorBundle\Entity\NomClasificacionIncidencia 
     */
    public function getIdcasificacion()
    {
        return $this->idcasificacion;
    }

    /**
     * Set idtipo
     *
     * @param \NomencladorBundle\Entity\NomTipoIncidencia $idtipo
     * @return DatIncidencia
     */
    public function setIdtipo(\NomencladorBundle\Entity\NomTipoIncidencia $idtipo = null)
    {
        $this->idtipo = $idtipo;

        return $this;
    }

    /**
     * Get idtipo
     *
     * @return \NomencladorBundle\Entity\NomTipoIncidencia 
     */
    public function getIdtipo()
    {
        return $this->idtipo;
    }

    /**
     * @return mixed
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * @param mixed $parte
     */
    public function setParte($parte)
    {
        $this->parte = $parte;
    }
    /**
     * Set entidad
     *
     * @param \NomencladorBundle\Entity\NomUeb $entidad
     * @return DatIncidencia
     */
    public function setEntidad(\NomencladorBundle\Entity\NomUeb $entidad = null)
    {
        $this->entidad = $entidad;
    
        return $this;
    }

    /**
     * Get entidad
     *
     * @return \NomencladorBundle\Entity\NomUeb 
     */
    public function getEntidad()
    {
        return $this->entidad;
    }


    public function getNombreEntidad()
    {
        return ' la incidencia: Tipo: '.$this->getIdtipo()->getNombre(). " - UEB". $this->getEntidad()->getNombre();
    }
}
