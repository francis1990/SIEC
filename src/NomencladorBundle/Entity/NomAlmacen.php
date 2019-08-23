<?php

namespace NomencladorBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomAlmacen
 *
 * @ORM\Table(name="nom_almacen")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomAlmacenRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomAlmacen
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idalmacen", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idalmacen;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=5, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco")
     */
    private $nombre;

    /**
     * @var NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ueb", referencedColumnName="idueb")
     * })
     * @Assert\NotBlank(message = "El valor no puede estar en blanco")
     */
    private $ueb;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var bool
     *
     * @ORM\Column(name="nevera", type="boolean")
     */
    private $nevera;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * Get idalmacen
     *
     * @return integer
     */
    public function getIdalmacen()
    {
        return $this->idalmacen;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomAlmacen
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

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomAlmacen
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
     * Set ueb
     *
     * @param \NomencladorBundle\Entity\NomUeb $ueb
     * @return NomAlmacen
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

    public function __toString()
    {
        return $this->getCodigo() . ' ' . $this->getNombre();
    }

    /**
     * Set nevera
     *
     * @param boolean $nevera
     * @return NomAlmacen
     */
    public function setNevera($nevera)
    {
        $this->nevera = $nevera;

        return $this;
    }

    /**
     * Get nevera
     *
     * @return boolean
     */
    public function getNevera()
    {
        return $this->nevera;
    }


    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomAlmacen
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomAlmacen
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
        return ' el almacén: ' . $this->nombre;
    }

}
