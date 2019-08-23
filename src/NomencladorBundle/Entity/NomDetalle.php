<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NomDetalle
 *
 * @ORM\Table(name="nom_detalle")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 */
class NomDetalle
{
    /**
     * @var integer
     *
     * @ORM\Column(name="iddetalle", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iddetalle;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=6, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     *
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;
    /**
     * Get iddetalle
     *
     * @return integer 
     */
    public function getidDetalle()
    {
        return $this->iddetalle;
    }

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return NomDetalle
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
     * Set nombre
     *
     * @param string $nombre
     * @return NomDetalle
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

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
     * @return NomDetalle
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
}
