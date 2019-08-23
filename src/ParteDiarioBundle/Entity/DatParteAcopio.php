<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomRuta;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomUnidadmedida;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatParteAcopio
 *
 * @ORM\Table(name="dat_parte_acopio")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteAcopioRepository")
 * @UniqueEntity(fields= {"fecha", "destino", "ruta"}, message="Existe un elemento con fecha, destino y ruta seleccionada.")
 */
class DatParteAcopio extends DatParte
{
    /**
     * @var integer
     *
     * @ORM\Column(name="destino", type="string",  nullable=true)
     */
    private $destino;

    /**
     * @var NomRuta
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomRuta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ruta", referencedColumnName="idruta")
     * })
     */
    private $ruta;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="ParteDiarioBundle\Entity\DatEntidadAcopio", mappedBy="parte", cascade={"persist"})
     */
    private $acopio;

    /**
     * Set ruta
     *
     * @param \NomencladorBundle\Entity\NomRuta $ruta
     * @return DatParteAcopio
     */
    public function setRuta(\NomencladorBundle\Entity\NomRuta $ruta = null)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta
     *
     * @return \NomencladorBundle\Entity\NomRuta 
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Add acopio
     *
     * @param \ParteDiarioBundle\Entity\DatEntidadAcopio $acopio
     * @return DatParteAcopio
     */
    public function addAcopio(\ParteDiarioBundle\Entity\DatEntidadAcopio $acopio)
    {
        $acopio->setParte($this);
        $this->acopio[] = $acopio;
    
        return $this;
    }

    /**
     * Remove acopio
     *
     * @param \ParteDiarioBundle\Entity\DatEntidadAcopio $acopio
     */
    public function removeAcopio(\ParteDiarioBundle\Entity\DatEntidadAcopio $acopio)
    {
        $this->acopio->removeElement($acopio);
    }

    /**
     * Get acopio
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAcopio()
    {
        return $this->acopio;
    }

    /**
     * Set destino
     *
     * @param integer $destino
     * @return DatParteAcopio
     */
    public function setDestino($destino)
    {
        $this->destino = $destino;

        return $this;
    }

    /**
     * Get destino
     *
     * @return integer
     */
    public function getDestino()
    {
        return $this->destino;
    }

    public function getNombreEntidad()
    {
        return ' el parte de acopio: Destino: ' . $this->destino."-Ruta: ".$this->ruta;
    }


}
