<?php

namespace ParteDiarioBundle\Entity;

use NomencladorBundle\Entity\NomGrupointeres;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomUeb;
use NomencladorBundle\Entity\NomEntidad;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatParteVenta
 *
 * @ORM\Table(name="dat_parte_venta")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteVentaRepository")
 * @UniqueEntity(fields= {"factura"}, message="Existe un elemento con esta factura.")
 * @UniqueEntity(fields= {"fecha", "ueb", "cliente"}, message="Existe un elemento con fecha, UEB y cliente seleccionado.")
 */
class DatParteVenta extends DatParte
{

    /**
     * @var NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente", referencedColumnName="identidad")
     * })
     */
    private $cliente;


    /**
     * @var integer
     *
     * @ORM\Column(name="factura", type="string",length=20, nullable=false)
     */
    private $factura;
    /**
     * @var integer
     *
     * @ORM\Column(name="cancelada", type="string",length=20, nullable=true)
     */
    private $cancelada;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="ParteDiarioBundle\Entity\DatVentaProducto", mappedBy="parte", cascade={"persist", "remove"})
     * @Assert\Count(min=1, minMessage="Debe agregar al menos 1 producto")
     */
    private $productos;

    /**
     * @var float
     *
     * @ORM\Column(name="importefinalmn", type="float",precision=10, scale=5,  nullable=true)
     */
    private $importefinalmn;
    /**
     * @var float
     *
     * @ORM\Column(name="importefinalcuc", type="float",precision=10, scale=5,  nullable=true)
     */
    private $importefinalcuc;

    /**
     * @var NomGrupointeres
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomGrupointeres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grupo", referencedColumnName="idgrupointeres")
     * })
     */
    private $grupo;

    /**
     * Set cliente
     *
     * @param \NomencladorBundle\Entity\NomEntidad $cliente
     * @return DatParteVenta
     */
    public function setCliente(\NomencladorBundle\Entity\NomEntidad $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \NomencladorBundle\Entity\NomEntidad
     */
    public function getCliente()
    {
        return $this->cliente;
    }


    /**
     * Set factura
     *
     * @param string $factura
     * @return DatParteVenta
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
     * Set cancelada
     *
     * @param string $cancelada
     * @return DatParteVenta
     */
    public function setCancelada($cancelada)
    {
        $this->cancelada = $cancelada;

        return $this;
    }

    /**
     * Get cancelada
     *
     * @return string
     */
    public function getCancelada()
    {
        return $this->cancelada;
    }


    /**
     * Add productos
     *
     * @param \ParteDiarioBundle\Entity\DatVentaProducto $productos
     * @return DatParteVenta
     */
    public function addProducto(\ParteDiarioBundle\Entity\DatVentaProducto $productos)
    {
        $productos->setParte($this);
        $this->productos[] = $productos;

        return $this;
    }

    /**
     * Remove productos
     *
     * @param \ParteDiarioBundle\Entity\DatVentaProducto $productos
     */
    public function removeProducto(\ParteDiarioBundle\Entity\DatVentaProducto $productos)
    {
        $this->productos->removeElement($productos);
    }

    /**
     * Get productos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductos()
    {
        return $this->productos;
    }

    /**
     * Set importefinalcuc
     *
     * @param float $importefinalcuc
     * @return DatParteVenta
     */
    public function setImporteFinalCuc($importe)
    {
        $this->importefinalcuc = $importe;

        return $this;
    }

    /**
     * Get importefinalcuc
     *
     * @return float
     */
    public function getImporteFinalCuc()
    {
        return $this->importefinalcuc;
    }

    /**
     * Set importefinalmn
     *
     * @param float $importefinalmn
     * @return DatParteVenta
     */
    public function setImporteFinalMn($importe)
    {
        $this->importefinalmn = $importe;

        return $this;
    }

    /**
     * Get importefinalmn
     *
     * @return float
     */
    public function getImporteFinalMn()
    {
        return $this->importefinalmn;
    }

    public function getNombreEntidad()
    {
        return ' el parte de venta: Factura' . $this->factura." - Cliente: ".$this->getCliente()->getNombre();
    }



    /**
     * Set grupo
     *
     * @param \NomencladorBundle\Entity\NomGrupointeres $grupo
     * @return DatParteVenta
     */
    public function setGrupo(\NomencladorBundle\Entity\NomGrupointeres $grupo = null)
    {
        $this->grupo = $grupo;
    
        return $this;
    }

    /**
     * Get grupo
     *
     * @return \NomencladorBundle\Entity\NomGrupointeres 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }
}
