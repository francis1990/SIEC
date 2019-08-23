<?php

namespace ParteDiarioBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomMonedadestino;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomEntidad;

/**
 * DatVentaProducto
 *
 * @ORM\Table(name="dat_parteventa_producto")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteVentaRepository")
 */
class DatVentaProducto
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
     * @var DatPartediarioProduccion
     *
     * @ORM\ManyToOne(targetEntity="\ParteDiarioBundle\Entity\DatParteVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parte", referencedColumnName="idparte", onDelete="cascade")
     * })
     */
    private $parte;
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
     * @ORM\Column(name="importemn", type="float",precision=10, scale=5,  nullable=true)
     */
    private $importemn;

    /**
     * @var float
     *
     * @ORM\Column(name="importecuc", type="float",precision=10, scale=5,  nullable=true)
     */
    private $importecuc;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=3, nullable=false)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @ORM\Column(name="cantfisica", type="float", precision=10, scale=3, nullable=false)
     */
    private $cantfisica;

    /**
     * @var NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="origen", referencedColumnName="identidad")
     * })
     */
    private $origen;

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
     * @var float
     *
     * @ORM\Column(name="impuesto", type="float",precision=10, scale=2,  nullable=true)
     */
    private $impuesto;


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
     * Set preciomn
     *
     * @param float $preciomn
     * @return DatVentaProducto
     */
    public function setPreciomn($preciomn)
    {
        $this->preciomn = $preciomn;
        $this->setImportemn($preciomn * $this->getCantidad());
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
     * @return DatVentaProducto
     */
    public function setPreciocuc($preciocuc)
    {
        $this->preciocuc = $preciocuc;
        $this->setImportecuc($preciocuc * $this->getCantidad());

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
     * @return DatVentaProducto
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
     * @return DatVentaProducto
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

    /**
     * Set cantidad
     *
     * @param float $cantidad
     * @return DatVentaProducto
     */
    private function setCantidad($cantidad)
    {
        if (isset($this->producto) && !is_null($this->producto)) {
            $conversion = $this->getProducto()->getFactor();
            if ($conversion != null && $conversion != 0) {
                $this->cantidad = $this->cantfisica * $conversion;
            } else {
                $this->cantidad = $this->cantfisica * $GLOBALS['kernel']->getContainer()->get('nomenclador.nomconversion')->obtenerConversion($this->producto) * !is_null($this->producto->getIdformato()) ? $this->producto->getIdformato()->getPeso() : 1;
            }
        } else {
            $this->cantidad = $cantidad;
        }
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
     * Set parte
     *
     * @param \ParteDiarioBundle\Entity\DatParteVenta $parte
     * @return DatVentaProducto
     */
    public function setParte(\ParteDiarioBundle\Entity\DatParteVenta $parte = null)
    {
        $this->parte = $parte;

        return $this;
    }

    /**
     * Get parte
     *
     * @return \ParteDiarioBundle\Entity\DatParteVenta
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * Set producto
     *
     * @param \NomencladorBundle\Entity\NomProducto $producto
     * @return DatVentaProducto
     */
    public function setProducto(\NomencladorBundle\Entity\NomProducto $producto = null)
    {
        $this->producto = $producto;
        $this->setUm($producto->getUmOperativa());
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
     * Set um
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $um
     * @return DatVentaProducto
     */
    private function setUm(\NomencladorBundle\Entity\NomUnidadmedida $um = null)
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
     * Set origen
     *
     * @param \NomencladorBundle\Entity\NomEntidad $origen
     * @return DatVentaProducto
     */
    public function setOrigen(\NomencladorBundle\Entity\NomEntidad $origen = null)
    {
        $this->origen = $origen;

        return $this;
    }

    /**
     * Get origen
     *
     * @return \NomencladorBundle\Entity\NomEntidad
     */
    public function getOrigen()
    {
        return $this->origen;
    }

    /**
     * Set almacen
     *
     * @param \NomencladorBundle\Entity\NomAlmacen $almacen
     * @return DatVentaProducto
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
     * Set impuesto
     *
     * @param float $impuesto
     * @return DatVentaProducto
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto
     *
     * @return float
     */
    public function getImpuesto()
    {
        return $this->impuesto;
    }

    /**
     * Set cantfisica
     *
     * @param float $cantfisica
     * @return DatVentaProducto
     */
    public function setCantfisica($cantfisica)
    {
        $this->cantfisica = $cantfisica;
        $this->setCantidad($cantfisica);
        return $this;
    }

    /**
     * Get cantfisica
     *
     * @return float
     */
    public function getCantfisica()
    {
        return $this->cantfisica;
    }
}
