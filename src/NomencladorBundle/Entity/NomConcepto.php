<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomConcepto
 *
 * @ORM\Table(name="nom_concepto")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomConceptoRepository")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre", message="Existe un elemento con ese nombre.")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con ese código.")
 */
class NomConcepto
{
    /**
     * @var int
     *
     * @ORM\Column(name="idconcepto", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idconcepto;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var bool
     *
     * @ORM\Column(name="tipo", type="boolean", nullable=true)
     */
    private $tipo;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var integer
     *
     * @ORM\Column(name="conceptodefault", type="integer",nullable=true)
     */
    private $conceptodefault;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;


    /**
     * Get idconcepto
     *
     * @return integer
     */
    public function getIdconcepto()
    {
        return $this->idconcepto;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomConcepto
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
     * @return NomConcepto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->alias =  Util::getSlug($nombre);
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
     * @return NomConcepto
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

    public function __toString()
    {
        return $this->nombre;
    }


    /**
     * Set tipo
     *
     * @param boolean $tipo
     * @return NomConcepto
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return boolean 
     */
    public function getTipo()
    {
        return $this->tipo;
    }



    /**
     * Set conceptodefault
     *
     * @param integer $conceptodefault
     * @return NomConcepto
     */
    public function setConceptodefault($conceptodefault)
    {
        $this->conceptodefault = $conceptodefault;
    
        return $this;
    }

    /**
     * Get conceptodefault
     *
     * @return integer 
     */
    public function getConceptodefault()
    {
        return $this->conceptodefault;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomConcepto
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
        return ' el concepto: ' . $this->nombre;
    }
}
