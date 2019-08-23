<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomPortador
 *
 * @ORM\Table(name="nom_portador")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con ese código")
 * @UniqueEntity(fields= {"nombre"}, message="Existe un elemento con ese nombre.")
 */
class NomPortador
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idportador", type= "integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idportador;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var bool
     *
     * @ORM\Column(name="alcance", type="boolean")
     */
    private $alcance;
    /**
     * @var bool
     *
     * @ORM\Column(name="dia", type="boolean")
     */
    private $dia;
    /**
     * @var bool
     *
     * @ORM\Column(name="madrugada", type="boolean")
     */
    private $madrugada;
    /**
     * @var bool
     *
     * @ORM\Column(name="pico", type="boolean")
     */
    private $pico;
    /**
     * @var bool
     *
     * @ORM\Column(name="inventario", type="boolean")
     */
    private $inventario;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\Length(max=2,min=2, exactMessage = "El valor debe tener exactamente {{ limit }} caracteres.")
     *
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;
    /**
     * @var \NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idunidadmedida", referencedColumnName="idunidadmedida")
     * })
     */
    private $idunidadmedida;

    /**
     * @var bool
     *
     * @ORM\Column(name="existencia", type="boolean")
     */
    private $existencia;

    /**
     * @var bool
     *
     * @ORM\Column(name="entrada", type="boolean")
     */
    private $entrada;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * Get idportador
     *
     * @return integer
     */

    public function getIdportador()
    {
        return $this->idportador;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomPortador
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
     * Set nombre
     *
     * @param string $nombre
     * @return NomPortador
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
     * Set idunidadmedida
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida
     * @return NomPortador
     */
    public function setIdunidadmedida(\NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida = null)
    {
        $this->idunidadmedida = $idunidadmedida;

        return $this;
    }

    /**
     * Get idunidadmedida
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getIdunidadmedida()
    {
        return $this->idunidadmedida;
    }


    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomPortador
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
     * Set alcance
     *
     * @param boolean $alcance
     * @return NomPortador
     */
    public function setAlcance($alcance)
    {
        $this->alcance = $alcance;

        return $this;
    }

    /**
     * Get alcance
     *
     * @return boolean
     */
    public function getAlcance()
    {
        return $this->alcance;
    }

    /**
     * Set dia
     *
     * @param boolean $dia
     * @return NomPortador
     */
    public function setDia($dia)
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia
     *
     * @return boolean
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set madrugada
     *
     * @param boolean $madrugada
     * @return NomPortador
     */
    public function setMadrugada($madrugada)
    {
        $this->madrugada = $madrugada;

        return $this;
    }

    /**
     * Get madrugada
     *
     * @return boolean
     */
    public function getMadrugada()
    {
        return $this->madrugada;
    }

    /**
     * Set pico
     *
     * @param boolean $pico
     * @return NomPortador
     */
    public function setPico($pico)
    {
        $this->pico = $pico;

        return $this;
    }

    /**
     * Get pico
     *
     * @return boolean
     */
    public function getPico()
    {
        return $this->pico;
    }

    /**
     * Set inventario
     *
     * @param boolean $inventario
     * @return NomPortador
     */
    public function setInventario($inventario)
    {
        $this->inventario = $inventario;

        return $this;
    }

    /**
     * Get inventario
     *
     * @return boolean
     */
    public function getInventario()
    {
        return $this->inventario;
    }

    /**
     * Set entrada
     *
     * @param boolean $entrada
     * @return NomPortador
     */
    public function setEntrada($entrada)
    {
        $this->entrada = $entrada;

        return $this;
    }

    /**
     * Get entrada
     *
     * @return boolean
     */
    public function getEntrada()
    {
        return $this->entrada;
    }

    /**
     * Set existencia
     *
     * @param boolean $existencia
     * @return NomPortador
     */
    public function setExistencia($existencia)
    {
        $this->existencia = $existencia;

        return $this;
    }

    /**
     * Get existencia
     *
     * @return boolean
     */
    public function getExistencia()
    {
        return $this->existencia;
    }



    /**
     * Set alias
     *
     * @param string $alias
     * @return NomPortador
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
