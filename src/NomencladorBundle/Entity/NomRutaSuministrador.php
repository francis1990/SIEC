<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomRuta
 *
 * @ORM\Table(name="nom_ruta_entidad_producto")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"entidad","producto","ruta"}, message="Existe un elemento con entidad y producto seleccionado.")
 */
class NomRutaSuministrador
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entidad", referencedColumnName="identidad")
     * })
     */
    private $entidad;
    /**
     * @var NomProducto
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto", referencedColumnName="idproducto")
     * })
     */
    private $producto;

    /**
     * @var NomRuta
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomRuta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ruta", referencedColumnName="idruta",onDelete="cascade")
     * })
     */
    private $ruta;

    /**
     * @var string
     *
     * @ORM\Column(name="caracterizacion", type="string", length=255, nullable=true)
     */
    private $caracterizacion;

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
     * Set caracterizacion
     *
     * @param string $caracterizacion
     * @return NomRutaSuministrador
     */
    public function setCaracterizacion($caracterizacion)
    {
        $this->caracterizacion = $caracterizacion;

        return $this;
    }

    /**
     * Get caracterizacion
     *
     * @return string 
     */
    public function getCaracterizacion()
    {
        return $this->caracterizacion;
    }

    /**
     * Set entidad
     *
     * @param \NomencladorBundle\Entity\NomEntidad $entidad
     * @return NomRutaSuministrador
     */
    public function setEntidad(\NomencladorBundle\Entity\NomEntidad $entidad = null)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad
     *
     * @return \NomencladorBundle\Entity\NomEntidad 
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return NomRutaSuministrador
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
     * Set ruta
     *
     * @param \NomencladorBundle\Entity\NomRuta $ruta
     * @return NomRutaSuministrador
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
}
