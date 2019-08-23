<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * NomNorma
 *
 * @ORM\Table(name="nom_norma")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomNormaRepository")
 * @UniqueEntity(fields= {"producto","tiponorma"}, message="Existe una norma para este producto y tipo NC seleccionado.")
 */
class NomNorma
{
    /**
     * @var int
     *
     * @ORM\Column(name="idnorma", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idnorma;

    /**
     * @var NomProducto
     *
     * @ORM\ManyToOne(targetEntity="NomProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto", referencedColumnName="idproducto")
     * })
     */
    private $producto;

    /**
     * @var float
     * @Assert\Range(
     *      min = "0",
     *      minMessage = "El valor mínimo del campo es 0.")
     * @ORM\Column(name="valornorma", type="float")
     */
    private $valornorma;

    /**
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="umnorma", referencedColumnName="idunidadmedida")
     * })
     */
    private $umnorma;

    /**
     * @ORM\OneToMany(targetEntity="DatNormaAseguramiento", mappedBy="norma", cascade={"persist"})
     */
    private $aseguramientos;

    /**
     * @var NomTipoNorma
     *
     * @ORM\ManyToOne(targetEntity="NomTipoNorma",inversedBy="normas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tiponorma", referencedColumnName="id")
     * })
     */
    private $tiponorma;

    /**
     * @var float
     *
     * @ORM\Column(name="grasa", type="float",nullable=true)
     */
    private $grasa;

    /**
     * @var float
     *
     * @ORM\Column(name="sng", type="float",nullable=true)
     */
    private $sng;


    /**
     * Get idnorma
     *
     * @return integer
     */
    public function getIdnorma()
    {
        return $this->idnorma;
    }

    /**
     * Set idnorma
     *
     * @param integer $idnorma
     * @return NomNorma
     */
    public function setIdnorma($idnorma)
    {
        $this->idnorma = $idnorma;
        return $this;
    }

    /**
     * Set valornorma
     *
     * @param integer $valornorma
     * @return NomNorma
     */
    public function setValornorma($valornorma)
    {
        $this->valornorma = $valornorma;

        return $this;
    }

    /**
     * Get valornorma
     *
     * @return integer
     */
    public function getValornorma()
    {
        return $this->valornorma;
    }

    /**
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return NomNorma
     */
    public function setProducto(\NomencladorBundle\Entity\NomProducto $producto = null)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \NomencladorBundle\Entity\NomProducto
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set umnorma
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $umnorma
     * @return NomNorma
     */
    public function setUmnorma(\NomencladorBundle\Entity\NomUnidadmedida $umnorma = null)
    {
        $this->umnorma = $umnorma;

        return $this;
    }

    /**
     * Get umnorma
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getUmnorma()
    {
        return $this->umnorma;
    }

    /**
     * Add aseguramientos
     *
     * @param \NomencladorBundle\Entity\DatNormaAseguramiento $aseguramientos
     * @return NomNorma
     */
    public function addAseguramiento(\NomencladorBundle\Entity\DatNormaAseguramiento $aseguramientos)
    {
        $aseguramientos->setNorma($this);
        $this->aseguramientos[] = $aseguramientos;

        return $this;
    }

    /**
     * Remove aseguramientos
     *
     * @param \NomencladorBundle\Entity\DatNormaAseguramiento $aseguramientos
     */
    public function removeAseguramiento(\NomencladorBundle\Entity\DatNormaAseguramiento $aseguramientos)
    {
        $this->aseguramientos->removeElement($aseguramientos);
    }

    /**
     * Get aseguramientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAseguramientos()
    {
        return $this->aseguramientos;
    }

    public function __toString()
    {
        return $this->getProducto()->getNombre();
    }

    public function getNombreEntidad()
    {
        return ' la norma de consumo: ' . $this->producto->getNombre()." Cada ".$this->valornorma. " ".$this->umnorma->getNombre();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aseguramientos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set tiponorma
     *
     * @param \NomencladorBundle\Entity\NomTipoNorma $tiponorma
     * @return NomNorma
     */
    public function setTiponorma(\NomencladorBundle\Entity\NomTipoNorma $tiponorma = null)
    {
        $this->tiponorma = $tiponorma;
    
        return $this;
    }

    /**
     * Get tiponorma
     *
     * @return \NomencladorBundle\Entity\NomTipoNorma 
     */
    public function getTiponorma()
    {
        return $this->tiponorma;
    }

    /**
     * Set grasa
     *
     * @param float $grasa
     * @return NomNorma
     */
    public function setGrasa($grasa)
    {
        $this->grasa = $grasa;
    
        return $this;
    }

    /**
     * Get grasa
     *
     * @return float 
     */
    public function getGrasa()
    {
        return $this->grasa;
    }

    /**
     * Set sng
     *
     * @param float $sng
     * @return NomNorma
     */
    public function setSng($sng)
    {
        $this->sng = $sng;
    
        return $this;
    }

    /**
     * Get sng
     *
     * @return float 
     */
    public function getSng()
    {
        return $this->sng;
    }
}
