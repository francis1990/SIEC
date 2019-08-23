<?php

namespace ParteDiarioBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomAlmacen;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatParteMercanciaVinculo
 *
 * @ORM\Table(name="dat_parte_mervinculo")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteMercanciaVinculoRepository")
 * @UniqueEntity(fields= {"anno","factura"}, errorPath="factura",message="Existe este número de factura para el año actual.")
 * @UniqueEntity(fields= {"fecha", "ueb", "producto","almacen","entidad"}, message="Existe un elemento para esta fecha, UEB, producto, entidad.")
 */
class DatParteMercanciaVinculo extends DatParte
{

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
     * @var NomAlmacen
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomAlmacen")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="almacen", referencedColumnName="idalmacen")
     * })
     */
    private $almacen;
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
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="um", referencedColumnName="idunidadmedida")
     * })
     */
    private $um;


    /**
     * @var float
     *
     * @ORM\Column(name="cantidadmn", type="float", precision=10, scale=3, nullable=false)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="factura", type="string",length=20, nullable=false)
     */
    private $factura;

    /**
     * @var float
     *
     * @ORM\Column(name="preciomn", type="float",precision=10, scale=5,  nullable=true)
     */
    private $preciomn;

    /**
     * @var float
     *
     * @ORM\Column(name="preciocuc", type="float",precision=10, scale=5,  nullable=true)
     */
    private $preciocuc;

    /**
     * @var float
     *
     * @ORM\Column(name="importemn", type="float",precision=10, scale=5,  nullable=false)
     */
    private $importemn;

    /**
     * @var float
     *
     * @ORM\Column(name="importecuc", type="float",precision=10, scale=5,  nullable=false)
     */
    private $importecuc;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno_factura", type="integer", nullable=false)
     */
    private $anno;


    /**
     * Set almacen
     *
     * @param \NomencladorBundle\Entity\NomAlmacen $almacen
     * @return DatParteMovimiento
     */
    public function setAlmacen(\NomencladorBundle\Entity\NomAlmacen $almacen = null)
    {
        $this->almacen = $almacen;
        return $this;
    }

    /**
     * Get almacen
     *
     * @return \NomencladorBundle\Entity\NomAlmacen
     */
    public function getAlmacen()
    {
        return $this->almacen;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return DatParteMercanciaVinculo
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }


    /**
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return DatParteMercanciaVinculo
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
     * Set entidad
     *
     * @param \NomencladorBundle\Entity\NomEntidad $entidad
     * @return DatParteMercanciaVinculo
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
     * Set um
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $um
     * @return DatParteMercanciaVinculo
     */
    public function setUm(\NomencladorBundle\Entity\NomUnidadmedida $um = null)
    {
        $this->um = $um;

        return $this;
    }

    /**
     * Get um
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getUm()
    {
        return $this->um;
    }

    /**
     * Set factura
     *
     * @param string $factura
     * @return DatParteMercanciaVinculo
     */
    public function setFactura($factura)
    {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return string
     */
    public function getFactura()
    {
        return $this->factura;
    }

    /**
     * Set preciomn
     *
     * @param float $preciomn
     * @return DatParteMercanciaVinculo
     */
    public function setPreciomn($preciomn)
    {
        $this->preciomn = $preciomn;

        return $this;
    }

    /**
     * Get preciomn
     *
     * @return float
     */
    public function getPreciomn()
    {
        return $this->preciomn;
    }

    /**
     * Set preciocuc
     *
     * @param float $preciocuc
     * @return DatParteMercanciaVinculo
     */
    public function setPreciocuc($preciocuc)
    {
        $this->preciocuc = $preciocuc;

        return $this;
    }

    /**
     * Get preciocuc
     *
     * @return float
     */
    public function getPreciocuc()
    {
        return $this->preciocuc;
    }

    /**
     * Set importemn
     *
     * @param float $importemn
     * @return DatParteMercanciaVinculo
     */
    public function setImportemn($importemn)
    {
        $this->importemn = $importemn;

        return $this;
    }

    /**
     * Get importemn
     *
     * @return float
     */
    public function getImportemn()
    {
        return $this->importemn;
    }

    /**
     * Set importecuc
     *
     * @param float $importecuc
     * @return DatParteMercanciaVinculo
     */
    public function setImportecuc($importecuc)
    {
        $this->importecuc = $importecuc;

        return $this;
    }

    /**
     * Get importecuc
     *
     * @return float
     */
    public function getImportecuc()
    {
        return $this->importecuc;
    }

    public function getNombreEntidad()
    {
        return ' el parte mercancía por vínculo: Factura: ' . $this->factura." - Producto: ".$this->getProducto()->getNombre();
    }


    /**
     * Set anno
     *
     * @param integer $anno
     * @return DatParteMercanciaVinculo
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return integer
     */
    public function getAnno()
    {
        return $this->anno;
    }
}
