<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomGrupointeres
 *
 * @ORM\Table(name="nom_grupointeres")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo","idpadre"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias","idpadre"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomGrupointeres
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idgrupointeres", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idgrupointeres;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=12, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     *
     */
    private $codigo;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;
    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;
    /**
     * @var \NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="identidad", referencedColumnName="identidad")
     * })
     */
    private $identidad;
    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer", nullable=false)
     */
    private $nivel;

    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer", nullable=false)
     *
     */
    private $idpadre;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * Get idgrupointeres
     *
     * @return integer
     */
    public function getIdgrupointeres()
    {
        return $this->idgrupointeres;
    }

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return NomGrupointeres
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Get identidad
     *
     * @return \NomencladorBundle\Entity\NomEntidad
     */
    public function getIdentidad()
    {
        return $this->identidad;
    }

    /**
     * Set identidad
     *
     * @param \NomencladorBundle\Entity\NomEntidad $identidad
     * @return NomGrupointeres
     */
    public function setIdentidad(\NomencladorBundle\Entity\NomEntidad $identidad = null)
    {
        $this->identidad = $identidad;

        return $this;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomGrupointeres
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->alias = Util::getSlug($nombre);
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomGrupointeres
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return NomGrupointeres
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set idpadre
     *
     * @param integer $idpadre
     * @return NomGrupointeres
     */
    public function setIdpadre($idpadre)
    {
        $this->idpadre = $idpadre;

        return $this;
    }

    /**
     * Get idpadre
     *
     * @return integer
     */
    public function getIdpadre()
    {
        return $this->idpadre;
    }

    /**
     * Set hoja
     *
     * @param boolean $hoja
     * @return NomGrupointeres
     */
    public function setHoja($hoja)
    {
        $this->hoja = $hoja;

        return $this;
    }

    /**
     * Get hoja
     *
     * @return boolean
     */
    public function getHoja()
    {
        return $this->hoja;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomGrupointeres
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

    public function getNombreEntidad()
    {
        return ' el grupo de interés: ' . $this->nombre;
    }

}
