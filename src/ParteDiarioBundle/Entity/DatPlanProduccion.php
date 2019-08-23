<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatPlanProduccion
 *
 * @ORM\Table(name="dat_plan_produccion")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatPlanProduccionRepository")
 * @UniqueEntity(fields= {"idejercicio", "idproducto", "idtipoplan","idueb","excluir"}, message="Existe un elemento para este Tipo plan, Ejercicio y producto seleccionado")
 */
class DatPlanProduccion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idplanproduccion", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idplanproduccion;
    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float",  nullable=false, precision=10)
     */
    private $cantidad;

    /**
     * @var \NomEjercicio
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEjercicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idejercicio", referencedColumnName="idejercicio")
     * })
     */
    private $idejercicio;
    /**
     * @var \NomProducto
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idproducto", referencedColumnName="idproducto")
     * })
     */
    private $idproducto;
    /**
     * @var \NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idueb", referencedColumnName="idueb")
     * })
     */
    private $idueb;
    /**
     * @var \NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idunidadmedida", referencedColumnName="idunidadmedida")
     * })
     */
    private $idunidadmedida;
    /**
     * @var float
     *
     * @ORM\Column(name="enero", type="float",nullable=true, precision=10)
     */
    private $enero;
    /**
     * @var float
     *
     * @ORM\Column(name="febrero", type="float", nullable=true, precision=10)
     */
    private $febrero;
    /**
     * @var float
     *
     * @ORM\Column(name="marzo", type="float", nullable=true, precision=10)
     */
    private $marzo;
    /**
     * @var float
     *
     * @ORM\Column(name="abril", type="float", nullable=true, precision=10)
     */
    private $abril;
    /**
     * @var float
     *
     * @ORM\Column(name="mayo", type="float", nullable=true, precision=10)
     */
    private $mayo;
    /**
     * @var float
     *
     * @ORM\Column(name="junio", type="float", nullable=true, precision=10)
     */
    private $junio;
    /**
     * @var float
     *
     * @ORM\Column(name="julio", type="float", nullable=true, precision=10)
     */
    private $julio;
    /**
     * @var float
     *
     * @ORM\Column(name="agosto", type="float",  nullable=true, precision=10)
     */
    private $agosto;
    /**
     * @var float
     *
     * @ORM\Column(name="septiembre", type="float", nullable=true, precision=10)
     */
    private $septiembre;
    /**
     * @var float
     *
     * @ORM\Column(name="octubre", type="float", nullable=true, precision=10)
     */
    private $octubre;
    /**
     * @var float
     *
     * @ORM\Column(name="noviembre", type="float", nullable=true, precision=10)
     */
    private $noviembre;
    /**
     * @var float
     *
     * @ORM\Column(name="diciembre", type="float", nullable=true, precision=10)
     */
    private $diciembre;
    /**
     * @var \NomTipoPlan
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomTipoPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtipoplan", referencedColumnName="idtipoplan")
     * })
     */
    private $idtipoplan;


    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer", nullable=false)
     *
     */
    private $idpadre;


    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;

    /**
     * @var \NomMonedadestino
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomMonedadestino")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idmonedadestino", referencedColumnName="id")
     * })
     */
    private $idmonedadestino;
    /**
     * @var string
     *
     * @ORM\Column(name="excluir", type="string",  nullable=false)
     */
    private $excluir;

    public function setExcluir($ex)
    {
        $this->excluir = $ex;

        return $this;
    }

    public function getExcluir()
    {
        return $this->excluir;
    }

    /**
     * Set idunidadmedida
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida
     * @return DatPlanProduccion
     */
    public function setIdunidadmedida(\NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida = null)
    {
        $this->idunidadmedida = $idunidadmedida;

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


    public function getText()//Orden del texto ---  ejercicio, producto, ueb y moneda
    {
        $pro = ($this->idproducto != '') ? $this->idproducto->getNombre() : '';
        $ueb = ($this->idueb != '') ? $this->idueb->getNombre() : '';
        $um = ($this->idunidadmedida != '') ? $this->idunidadmedida->getNombre() : '';
        $md = ($this->idmonedadestino != '') ? $this->idmonedadestino->getNombre() : '';
        $eje = ($this->idejercicio != '') ? $this->idejercicio->getNombre() : '';
        $text = $pro . '_' . $eje . '_' . '' . '_' . $um . '_' . $ueb . '_' . $md;
        return $text;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getText();
    }

    /**
     * Set cantidad
     *
     * @param float
     * @return float
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get idproducto
     *
     * @return \NomencladorBundle\Entity\NomProducto
     */
    public function getIdproducto()
    {
        return $this->idproducto;
    }

    /**
     * Set idproducto
     *
     * @param \NomencladorBundle\Entity\NomProducto $idproducto
     * @return DatPlanProduccion
     */
    public function setIdproducto(\NomencladorBundle\Entity\NomProducto $idproducto = null)
    {
        $this->idproducto = $idproducto;

        return $this;
    }

    /**
     * Get idueb
     *
     * @return \NomencladorBundle\Entity\NomUeb
     */
    public function getIdueb()
    {
        return $this->idueb;
    }

    /**
     * Set enero
     *
     * @param float $enero
     * @return NomPlanProduccion
     */
    public function setEnero($enero)
    {
        $this->enero = $enero;

        return $this;
    }

    /**
     * Get enero
     *
     * @return float
     */
    public function getEnero()
    {
        return $this->enero;
    }

    /**
     * Set febrero
     *
     * @param float $febrero
     * @return NomPlanProduccion
     */
    public function setFebrero($febrero)
    {
        $this->febrero = $febrero;

        return $this;
    }

    /**
     * Get febrero
     *
     * @return float
     */
    public function getFebrero()
    {
        return $this->febrero;
    }

    /**
     * Set marzo
     *
     * @param float $marzo
     * @return NomPlanProduccion
     */
    public function setMarzo($marzo)
    {
        $this->marzo = $marzo;

        return $this;
    }

    /**
     * Get marzo
     *
     * @return float
     */
    public function getMarzo()
    {
        return $this->marzo;
    }

    /**
     * Set abril
     *
     * @param float $abril
     * @return NomPlanProduccion
     */
    public function setAbril($abril)
    {
        $this->abril = $abril;

        return $this;
    }

    /**
     * Get abril
     *
     * @return float
     */
    public function getAbril()
    {
        return $this->abril;
    }

    /**
     * Set mayo
     *
     * @param float $mayo
     * @return NomPlanProduccion
     */
    public function setMayo($mayo)
    {
        $this->mayo = $mayo;

        return $this;
    }

    /**
     * Get mayo
     *
     * @return float
     */
    public function getMayo()
    {
        return $this->mayo;
    }

    /**
     * Set junio
     *
     * @param float $junio
     * @return NomPlanProduccion
     */
    public function setJunio($junio)
    {
        $this->junio = $junio;

        return $this;
    }

    /**
     * Get junio
     *
     * @return float
     */
    public function getJunio()
    {
        return $this->junio;
    }

    /**
     * Set julio
     *
     * @param float $julio
     * @return NomPlanProduccion
     */
    public function setJulio($julio)
    {
        $this->julio = $julio;

        return $this;
    }

    /**
     * Get julio
     *
     * @return float
     */
    public function getJulio()
    {
        return $this->julio;
    }

    /**
     * Set agosto
     *
     * @param float $agosto
     * @return NomPlanProduccion
     */
    public function setAgosto($agosto)
    {
        $this->agosto = $agosto;

        return $this;
    }

    /**
     * Get agosto
     *
     * @return float
     */
    public function getAgosto()
    {
        return $this->agosto;
    }

    /**
     * Set septiembre
     *
     * @param float $septiembre
     * @return NomPlanProduccion
     */
    public function setSeptiembre($septiembre)
    {
        $this->septiembre = $septiembre;

        return $this;
    }

    /**
     * Get septiembre
     *
     * @return float
     */
    public function getSeptiembre()
    {
        return $this->septiembre;
    }

    /**
     * Set octubre
     *
     * @param float $octubre
     * @return NomPlanProduccion
     */
    public function setOctubre($octubre)
    {
        $this->octubre = $octubre;

        return $this;
    }

    /**
     * Get octubre
     *
     * @return float
     */
    public function getOctubre()
    {
        return $this->octubre;
    }

    /**
     * Set noviembre
     *
     * @param float $noviembre
     * @return NomPlanProduccion
     */
    public function setNoviembre($noviembre)
    {
        $this->noviembre = $noviembre;

        return $this;
    }

    /**
     * Get noviembre
     *
     * @return float
     */
    public function getNoviembre()
    {
        return $this->noviembre;
    }

    /**
     * Set diciembre
     *
     * @param float $diciembre
     * @return NomPlanProduccion
     */
    public function setDiciembre($diciembre)
    {
        $this->diciembre = $diciembre;

        return $this;
    }

    /**
     * Get diciembre
     *
     * @return float
     */
    public function getDiciembre()
    {
        return $this->diciembre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idruta = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get idplanproduccion
     *
     * @return integer
     */
    public function getidplanproduccion()
    {
        return $this->idplanproduccion;
    }


    public function getTotal()
    {
        $tol = $this->getEnero() + $this->getFebrero() + $this->getAbril() + $this->getMayo() + $this->getMarzo() + $this->getJulio() + $this->getSeptiembre()
            + $this->getDiciembre();
        return $tol;
    }

    /**
     * Set idueb
     *
     * @param \NomencladorBundle\Entity\NomUeb $idueb
     * @return DatPlanProduccion
     */
    public function setIdueb(\NomencladorBundle\Entity\NomUeb $idueb = null)
    {
        $this->idueb = $idueb;

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

    /**
     * Set idmonedadestino
     *
     * @param \NomencladorBundle\Entity\NomMonedadestino idmonedadestino
     * @return DatPlanProduccion
     */
    public function setIdmoneda(\NomencladorBundle\Entity\NomMonedadestino $idmonedadestino = null)
    {
        $this->idmonedadestino = $idmonedadestino;

        return $this;
    }

    /**
     * Get idmonedadestino
     *
     * @return \NomencladorBundle\Entity\NomMonedadestino
     */

    public function getIdmonedadestino()
    {
        return $this->idmonedadestino;
    }

    /**
     * Set idpadre
     *
     * @param integer $idpadre
     * @return DatPlanProduccion
     */
    public function setIdpadre($idpadre)
    {
        $this->idpadre = $idpadre;

        return $this;
    }

    /**
     * Get idpadre
     *
     * @return integer
     */
    public function getIdpadre()
    {
        return $this->idpadre;
    }


    /**
     * Set idmonedadestino
     *
     * @param \NomencladorBundle\Entity\NomMonedadestino $idmonedadestino
     * @return DatPlanProduccion
     */
    public function setIdmonedadestino(\NomencladorBundle\Entity\NomMonedadestino $idmonedadestino = null)
    {
        $this->idmonedadestino = $idmonedadestino;

        return $this;
    }

    /**
     * Set idtipoplan
     *
     * @param \NomencladorBundle\Entity\NomTipoPlan $idtipoplan
     * @return DatPlanProduccion
     */
    public function setIdtipoplan(\NomencladorBundle\Entity\NomTipoPlan $idtipoplan = null)
    {
        $this->idtipoplan = $idtipoplan;

        return $this;
    }

    /**
     * Get idtipoplan
     *
     * @return \NomencladorBundle\Entity\NomTipoPlan
     */
    public function getIdtipoplan()
    {
        return $this->idtipoplan;
    }

    /**
     * Set idejercicio
     *
     * @param \NomencladorBundle\Entity\NomEjercicio $idejercicio
     * @return DatPlanProduccion
     */
    public function setIdejercicio(\NomencladorBundle\Entity\NomEjercicio $idejercicio = null)
    {
        $this->idejercicio = $idejercicio;

        return $this;
    }

    /**
     * Get idejercicio
     *
     * @return \NomencladorBundle\Entity\NomEjercicio
     */
    public function getIdejercicio()
    {
        return $this->idejercicio;
    }



    /**
     * Set hoja
     *
     * @param boolean $hoja
     * @return DatPlanProduccion
     */
    public function setHoja($hoja)
    {
        $this->hoja = $hoja;

        return $this;
    }

    /**
     * Get hoja
     *
     * @return boolean
     */
    public function getHoja()
    {
        return $this->hoja;
    }

    public function getNombreEntidad()
    {
        return ' el plan de nivel de actividad: Producto: ' . $this->getIdproducto()->getNombre();
    }
}
