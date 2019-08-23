<?php

namespace ParteDiarioBundle\Entity;

use AdminBundle\Entity\Usuario;
use Doctrine\Common\Collections\ArrayCollection;
use NomencladorBundle\Entity\NomAseguramiento;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use NomencladorBundle\NomencladorBundle;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\HttpFoundation\Tests\StringableObject;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * DatAlerta
 *
 * @ORM\Table(name="dat_alerta")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatAlertaRepository")
 */
class DatAlerta
{
    /**
     * @var int
     *
     * @ORM\Column(name="idalerta", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idalerta;

    /**
     * @ORM\ManyToMany(targetEntity="\AdminBundle\Entity\Usuario", inversedBy="alertas")
     * @ORM\JoinTable(name="usuario_alerta",
     * joinColumns={@ORM\JoinColumn(name="idalerta", referencedColumnName="idalerta", onDelete="cascade")},
     * inverseJoinColumns={@ORM\JoinColumn(name="idusuario", referencedColumnName="idUsuario")})
     * @Assert\Count(
     *     min = "1",
     * minMessage = "Debe seleccionar al menos un elemento.",
     * )
     *
     */
    protected $usuarios;

    /**
     * @var Date
     *
     * @ORM\Column(name="fecha", type="date",nullable=true)
     */
    private $fecha;

    /**
     * @var \NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idueb", referencedColumnName="idueb")
     * })
      * @Assert\NotNull(message = "Debe seleccionar un elemento.")
     */
    private $entidad;

    /**
     * @var \NomencladorBundle\Entity\NomTipotransporte
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomTipotransporte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private $tipo_transporte;

    /**
     * @var string
     *
     * @ORM\Column(name="Actividad", type="string", length=255)
     */
    private $actividad;

    /**
     * @var \NomencladorBundle\Entity\NomProducto
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idproducto", referencedColumnName="idproducto")
     * })
     */
    private $producto;

    /**
     * @var \NomencladorBundle\Entity\NomMonedadestino
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomMonedadestino")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idmonedadestino", referencedColumnName="id")
     * })
     */
    private $moneda;

    /**
     * @var \NomencladorBundle\Entity\NomAseguramiento
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomAseguramiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idaseguramiento", referencedColumnName="idaseguramiento")
     * })
     */
    private $insumo;

    /**
     * @var \NomencladorBundle\Entity\NomPortador
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomPortador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idportador", referencedColumnName="idportador")
     * })
     */
    private $portador;

    /**
     * @var \NomencladorBundle\Entity\NomEjercicio
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEjercicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idejercicio", referencedColumnName="idejercicio")
     * })
     */
    private $ejercicio;

    /**
     * @var \NomencladorBundle\Entity\NomTipoPlan
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomTipoPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtipoplan", referencedColumnName="idtipoplan")
     * })
     */
    private $tipo_plan;

    /**
     * @var \NomencladorBundle\Entity\NomCuentacontable
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomCuentacontable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcuentacontable", referencedColumnName="idcuentacontable")
     * })
     */
    private $cuenta;

    /**
     * @var \NomencladorBundle\Entity\NomGrupointeres
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomGrupointeres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idgrupointeres", referencedColumnName="idgrupointeres")
     * })
     */
    private $grupo_interes;


    /**
     * @var String
     *
     * @ORM\Column(name="periodo", type="string", length=255,nullable = true)
     */
    private $periodo;

    /**
     * @var String
     *
     * @ORM\Column(name="consumo_inventario", type="string", length=255, nullable = true)
     */
    private $consumo_inventario;

    /**
     * @var String
     *
     * @ORM\Column(name="vencida_cuenta", type="string", length=255, nullable = true)
     */
    private $vencida_cuenta;


    /**
     * DatAlerta constructor.
     * @param \Usuario $usuarios
     */
    public function __construct()
    {
        $this->revisada=false;
        $this->usuarios = new ArrayCollection();
    }

    /**
     * @var String
     *
     * @ORM\Column(name="operador", type="string")
     *
     */
    private $operador;


    /**
     * @var float
     *
     * @ORM\Column(name="cant", type="float", nullable=false)
     */
    private $cant;

    /**
     * @var float
     *
     * @ORM\Column(name="porciento", type="float", nullable=true)
     */
    private $porciento;

    /**
     * @var string
     *
     * @ORM\Column(name="Descripcion", type="string", length=255)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $descripcion;


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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->idalerta;
    }

    /**
     * Add usuarios
     *
     * @param \AdminBundle\Entity\Usuario $usuario
     * @return Usuario
     */
    public function addUsuario(Usuario $usuario)
    {
        $this->usuarios[] = $usuario;

        return $this;
    }


    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->actividad;
    }


    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Set fecha
     *
     * @param date $fecha
     * @return DatAlerta
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return Date
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @return mixed
     */
    public function getOperador()
    {
        return $this->operador;
    }

    /**
     * @param mixed $operador
     */
    public function setOperador($operador)
    {
        $this->operador = $operador;
    }

    /**
     * Set actividad
     *
     * @param string $actividad
     * @return DatAlerta
     */
    public function setActividad($actividad)
    {
        $this->actividad = $actividad;

        return $this;
    }

    /**
     * Get actividad
     *
     * @return string
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set cant
     *
     * @param string $cant
     * @return DatAlerta
     */
    public function setCant($cant)
    {
        $this->cant = $cant;

        return $this;
    }

    /**
     * Get cant
     *
     * @return string
     */
    public function getCant()
    {
        return $this->cant;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return DatAlerta
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set usuarios
     *
     * @param \AdminBundle\Entity\Usuario $usuarios
     * @return DatAlerta
     */
    public function setUsuarios(\AdminBundle\Entity\Usuario $usuarios = null)
    {
        $this->usuarios = $usuarios;

        return $this;
    }

    /**
     * Get idalerta
     *
     * @return integer
     */
    public function getIdalerta()
    {
        return $this->idalerta;
    }

    /**
     * Remove usuarios
     *
     * @param \AdminBundle\Entity\Usuario $usuarios
     */
    public function removeUsuario(\AdminBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * @return mixed
     */
    public function getAlerta()
    {
        return $this->alerta;
    }

    /**
     * @param mixed $alerta
     */
    public function setAlerta($alerta)
    {
        $this->alerta = $alerta;
    }

    /**
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param mixed $relation
     */
    public function setRelation($relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return mixed
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * @param mixed $producto
     */
    public function setProducto($producto)
    {
        $this->producto = $producto;
    }

    /**
     * @return mixed
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * @param mixed $moneda
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
    }

    /**
     * @return mixed
     */
    public function getInsumo()
    {
        return $this->insumo;
    }

    /**
     * @param mixed $insumo
     */
    public function setInsumo($insumo)
    {
        $this->insumo = $insumo;
    }


    /**
     * @return \NomencladorBundle\Entity\NomTipotransporte
     */
    public function getTipoTransporte()
    {
        return $this->tipo_transporte;
    }

    /**
     * @param \NomencladorBundle\Entity\NomTipotransporte $tipo_transporte
     */
    public function setTipoTransporte($tipo_transporte)
    {
        $this->tipo_transporte = $tipo_transporte;
    }

    /**
     * @return \NomencladorBundle\Entity\NomPortador
     */
    public function getPortador()
    {
        return $this->portador;
    }

    /**
     * @param \NomencladorBundle\Entity\NomPortador $portador
     */
    public function setPortador($portador)
    {
        $this->portador = $portador;
    }

    /**
     * @return String
     */
    public function getConsumoInventario()
    {
        return $this->consumo_inventario;
    }

    /**
     * @param String $consumo_inventario
     */
    public function setConsumoInventario($consumo_inventario)
    {
        $this->consumo_inventario = $consumo_inventario;
    }

    /**
     * @return \NomencladorBundle\Entity\NomEjercicio
     */
    public function getEjercicio()
    {
        return $this->ejercicio;
    }

    /**
     * @param \NomencladorBundle\Entity\NomEjercicio $ejercicio
     */
    public function setEjercicio($ejercicio)
    {
        $this->ejercicio = $ejercicio;
    }

    /**
     * @return \NomencladorBundle\Entity\NomTipoPlan
     */
    public function getTipoPlan()
    {
        return $this->tipo_plan;
    }

    /**
     * @param \NomencladorBundle\Entity\NomTipoPlan $tipo_plan
     */
    public function setTipoPlan($tipo_plan)
    {
        $this->tipo_plan = $tipo_plan;
    }

    /**
     * @return String
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * @param String $periodo
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;
    }

    /**
     * @return \NomencladorBundle\Entity\NomCuentacontable
     */
    public function getCuenta()
    {
        return $this->cuenta;
    }

    /**
     * @param \NomencladorBundle\Entity\NomCuentacontable $cuenta
     */
    public function setCuenta($cuenta)
    {
        $this->cuenta = $cuenta;
    }

    /**
     * @return String
     */
    public function getVencidaCuenta()
    {
        return $this->vencida_cuenta;
    }

    /**
     * @param String $vencida_cuenta
     */
    public function setVencidaCuenta($vencida_cuenta)
    {
        $this->vencida_cuenta = $vencida_cuenta;
    }

    /**
     * @return \NomencladorBundle\Entity\NomGrupointeres
     */
    public function getGrupoInteres()
    {
        return $this->grupo_interes;
    }

    /**
     * @param \NomencladorBundle\Entity\NomGrupointeres $grupo_interes
     */
    public function setGrupoInteres($grupo_interes)
    {
        $this->grupo_interes = $grupo_interes;
    }

    /**
     * @return NomEntidad
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param NomEntidad $cliente
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Set entidad
     *
     * @param \NomencladorBundle\Entity\NomUeb $entidad
     * @return DatAlerta
     */
    public function setEntidad(\NomencladorBundle\Entity\NomUeb $entidad = null)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad
     *
     * @return \NomencladorBundle\Entity\NomUeb
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    public function getNombreEntidad()
    {
        return ' la alerta: UEB: '.$this->getEntidad()->getNombre()." - Actividad: ".$this->actividad;
    }







    /**
     * Set porciento
     *
     * @param float $porciento
     * @return DatAlerta
     */
    public function setPorciento($porciento)
    {
        $this->porciento = $porciento;
    
        return $this;
    }

    /**
     * Get porciento
     *
     * @return float 
     */
    public function getPorciento()
    {
        return $this->porciento;
    }


}
