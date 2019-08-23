<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomDpa;
use NomencladorBundle\Util\Util;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatConfig
 *
 * @ORM\Table(name="cfg_gral")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\DatConfigRepository")
 * @UniqueEntity(fields= {"reup_entidad"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombreEntidad",message="Existe un elemento con el mismo nombre.")
 *
 */
class DatConfig
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_trabajo", type="datetime",nullable=true)
     */
    private $fechaTrabajo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_entidad", type="string", length=100,nullable=true)
     */

    private $nombreEntidad;
    /**
     * @var string
     *
     * @ORM\Column(name="reup_entidad", type="string", length=50,nullable=true)
     */
    private $reup_entidad;

    /**
     * @var NomDpa
     *
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\NomDpa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iddpa", referencedColumnName="iddpa")
     * })
     */
    private $iddpa;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;


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
     * Set fechaTrabajo
     *
     * @param \DateTime $fechaTrabajo
     * @return DatConfig
     */
    public function setFechaTrabajo($fechaTrabajo)
    {
        $this->fechaTrabajo = $fechaTrabajo;

        return $this;
    }

    /**
     * Get fechaTrabajo
     *
     * @return \DateTime
     */
    public function getFechaTrabajo()
    {
        return $this->fechaTrabajo;
    }

    /**
     * Set nombreEntidad
     *
     * @param string $nombreEntidad
     * @return DatConfig
     */
    public function setNombreEntidad($nombreEntidad)
    {
        $this->alias = Util::getSlug($nombreEntidad);
        $this->nombreEntidad = $nombreEntidad;

        return $this;
    }

    /**
     * Get nombreEntidad
     *
     * @return string
     */
    public function getNombreEntidad()
    {
        return $this->nombreEntidad;
    }

    /**
     * Set reup_entidad
     *
     * @param string $reupEntidad
     * @return DatConfig
     */
    public function setReupEntidad($reupEntidad)
    {
        $this->reup_entidad = $reupEntidad;

        return $this;
    }

    /**
     * Get reup_entidad
     *
     * @return string
     */
    public function getReupEntidad()
    {
        return $this->reup_entidad;
    }


    /**
     * Set direccion
     *
     * @param string $direccion
     * @return DatConfig
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set iddpa
     *
     * @param \NomencladorBundle\Entity\NomDpa $iddpa
     * @return DatConfig
     */
    public function setIddpa(\NomencladorBundle\Entity\NomDpa $iddpa = null)
    {
        $this->iddpa = $iddpa;

        return $this;
    }

    /**
     * Get iddpa
     *
     * @return \NomencladorBundle\Entity\NomDpa
     */
    public function getIddpa()
    {
        return $this->iddpa;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return DatConfig
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
