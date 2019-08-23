<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DatPlanAcopio
 *
 * @ORM\Table(name="dat_plan_acopio")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatPlanAcopioRepository")
 * @UniqueEntity(fields= {"idproducto",  "excluir","idejercicio", "idtipoplan","suministrador","idueb"}, message="Existe un elemento con UEB, tipo plan, ejercicio, suministrador y producto seleccionado.")
 *

 */
class DatPlanAcopio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idplanacopio", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idplanacopio;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float",  nullable=false, precision=10, scale=3)
     */
    private $cantidad;


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
     * @var \NomencladorBundle\Entity\NomTipoPlan
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomTipoPlan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtipoplan", referencedColumnName="idtipoplan")
     * })
     */
    private $idtipoplan;
    /**
     * @var float
     *
     * @ORM\Column(name="enero", type="float", nullable=true , precision=10)
     */
    private $enero;
    /**
     * @var float
     *
     * @ORM\Column(name="febrero", type="float", nullable=true , precision=10)
     */
    private $febrero;
    /**
     * @var float
     *
     * @ORM\Column(name="marzo", type="float", nullable=true , precision=10)
     */
    private $marzo;
    /**
     * @var float
     *
     * @ORM\Column(name="abril", type="float", nullable=true , precision=10)
     */
    private $abril;
    /**
     * @var float
     *
     * @ORM\Column(name="mayo", type="float", nullable=true , precision=10)
     */
    private $mayo;
    /**
     * @var float
     *
     * @ORM\Column(name="junio", type="float", nullable=true , precision=10)
     */
    private $junio;
    /**
     * @var float
     *
     * @ORM\Column(name="julio", type="float", nullable=true , precision=10)
     */
    private $julio;
    /**
     * @var float
     *
     * @ORM\Column(name="agosto", type="float", nullable=true , precision=10)
     */
    private $agosto;
    /**
     * @var float
     *
     * @ORM\Column(name="septiembre", type="float", nullable=true , precision=10)
     */
    private $septiembre;
    /**
     * @var float
     *
     * @ORM\Column(name="octubre", type="float", nullable=true , precision=10)
     */
    private $octubre;
    /**
     * @var float
     *
     * @ORM\Column(name="noviembre", type="float", nullable=true , precision=10)
     */
    private $noviembre;
    /**
     * @var float
     *
     * @ORM\Column(name="diciembre", type="float", nullable=true , precision=10)
     */
    private $diciembre;

    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;

    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer", nullable=false)
     *
     */
    private $idpadre;
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
     * @var \NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idsuministrador", referencedColumnName="identidad")
     * })
     */
    private $suministrador;

    /**
     * @var string
     *
     * @ORM\Column(name="excluir", type="string",  nullable=false)
     */
    private $excluir;

    /**
     * Set idunidadmedida
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida
     * @return DatPlanAcopio
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
     * @return DatPlanVenta
     */
    public function setIdproducto(\NomencladorBundle\Entity\NomProducto $idproducto = null)
    {
        $this->idproducto = $idproducto;
        $ueb=$this->getIdueb()==null? 0: $this->getIdueb()->getIdueb();
        $mon=$this->getIdmonedadestino()==null? 0: $this->getIdmonedadestino()->getId();
        $ex=  $ueb.'-'.$mon;
        $this->setExcluir($ex);
        return $this;
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * @return DatPlanAcopio
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
     * Get idplanacopio
     *
     * @return integer
     */
    public function getIdplanacopio()
    {
        return $this->idplanacopio;
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
     * @return DatPlanAcopio
     */
    public function setIdueb(\NomencladorBundle\Entity\NomUeb $idueb = null)
    {
        $this->idueb = $idueb;
        $ueb=$this->getIdueb()==null? 0: $this->getIdueb()->getIdueb();
        $mon=$this->getIdmonedadestino()==null? 0: $this->getIdmonedadestino()->getId();
        $ex=  $ueb.'-'.$mon;
        $this->setExcluir($ex);
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
     * Set idtipoplan
     *
     * @param \NomencladorBundle\Entity\NomTipoPlan $idtipoplan
     * @return DatPlanAcopio
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
     * Set idmonedadestino
     *
     * @param \NomencladorBundle\Entity\NomMonedadestino $idmonedadestino
     * @return DatPlanAcopio
     */
    public function setIdmonedadestino(\NomencladorBundle\Entity\NomMonedadestino $idmonedadestino = null)
    {
        $this->idmonedadestino = $idmonedadestino;
        $ueb=$this->getIdueb()==null? 0: $this->getIdueb()->getIdueb();
        $mon=$this->getIdmonedadestino()==null? 0: $this->getIdmonedadestino()->getId();
        $ex=  $ueb.'-'.$mon;
        $this->setExcluir($ex);
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
     * Set hoja
     *
     * @param boolean $hoja
     * @return DatPlanAcopio
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



    /**
     * Set idejercicio
     *
     * @param \NomencladorBundle\Entity\NomEjercicio $idejercicio
     * @return DatPlanAcopio
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
     * Set idpadre
     *
     * @param integer $idpadre
     * @return DatPlanAcopio
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

    public function setExcluir($ex)
    {
        $this->excluir = $ex;

        return $this;
    }

    public function getExcluir()
    {
        return $this->excluir;
    }

    public function getNombreEntidad()
    {
        return ' el plan de acopio: Producto: ' . $this->getIdproducto()->getNombre();
    }


    /**
     * Set suministrador
     *
     * @param \NomencladorBundle\Entity\NomEntidad $suministrador
     * @return DatPlanAcopio
     */
    public function setSuministrador(\NomencladorBundle\Entity\NomEntidad $suministrador = null)
    {
        $this->suministrador = $suministrador;
    
        return $this;
    }

    /**
     * Get suministrador
     *
     * @return \NomencladorBundle\Entity\NomEntidad 
     */
    public function getSuministrador()
    {
        return $this->suministrador;
    }
}
