<?php

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use NomencladorBundle\Entity\NomEjercicio;
use NomencladorBundle\Entity\NomEntidad;
use NomencladorBundle\Entity\NomUeb;
use NomencladorBundle\Entity\NomProducto;
use NomencladorBundle\Entity\NomGrupointeres;
use NomencladorBundle\Entity\NomUnidadmedida;
use NomencladorBundle\Entity\NomMonedadestino;
use NomencladorBundle\Entity\NomTipoPlan;

/**
 * DatPlanVenta
 *
 * @ORM\Table(name="dat_plan_venta")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Entity\DatPlanRepository")
 * @UniqueEntity(fields= {"idejercicio", "idproducto", "idtipoplan", "excluir","idgrupocliente","is_val"}, message="Existe un elemento para este Tipo plan, Ejercicio, producto y Grupo de interés seleccionado.")
 */
class DatPlanVenta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idplanventa", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idplanventa;
    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_val", type="boolean")
     */
    private $is_val;
    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float",  nullable=false, precision=10)
     */
    private $valor;

    /**
     * @var NomEjercicio
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEjercicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idejercicio", referencedColumnName="idejercicio")
     * })
     */
    private $idejercicio;
    /**
     * @var NomEntidad
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomEntidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="identidad", referencedColumnName="identidad")
     * })
     */
    private $identidad;
    /**
     * @var NomProducto
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomProducto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idproducto", referencedColumnName="idproducto")
     * })
     */
    private $idproducto;
    /**
     * @var NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idueb", referencedColumnName="idueb")
     * })
     */
    private $idueb;
    /**
     * @var NomGrupointeres
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomGrupointeres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idgrupocliente", referencedColumnName="idgrupointeres")
     * })
     */
    private $idgrupocliente;

    /**
     * @var NomGrupointeres
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomGrupointeres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grupopadre", referencedColumnName="idgrupointeres")
     * })
     */
    private $grupopadre;

    /**
     * @var NomGrupointeres
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomGrupointeres")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grupoentidad", referencedColumnName="idgrupointeres")
     * })
     */
    private $grupoentidad;


    /**
     * @var NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idunidadmedida", referencedColumnName="idunidadmedida")
     * })
     */
    private $idunidadmedida;
    /**
     * @var NomMonedadestino
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomMonedadestino")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idmonedadestino", referencedColumnName="id")
     * })
     */
    private $idmonedadestino;
    /**
     * @var float
     *
     * @ORM\Column(name="enero", type="float", nullable=true, precision=10)
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
     * @ORM\Column(name="agosto", type="float", nullable=true, precision=10)
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
     * @var NomTipoPlan
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
     * Set idtipoplan
     *
     * @param \NomencladorBundle\Entity\NomTipoPlan $idtipoplan
     * @return DatPlanVenta
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
     * Set idunidadmedida
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida
     * @return DatPlanVenta
     */
    public function setIdunidadmedida(\NomencladorBundle\Entity\NomUnidadmedida $idunidadmedida = null)
    {
        $this->idunidadmedida = $idunidadmedida;

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
        $ueb = $this->getIdueb() == null ? 0 : $this->getIdueb()->getIdueb();
        $mon = $this->getIdmonedadestino() == null ? 0 : $this->getIdmonedadestino()->getId();
        $ex = $ueb . '-' . $mon;
        $this->setExcluir($ex);
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * @return DatPlanVenta
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
     * Get idplanventa
     *
     * @return integer
     */
    public function getIdplanventa()
    {
        return $this->idplanventa;
    }

    /**
     * Set valor
     *
     * @param float $valor
     * @return DatPlanVenta
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return float
     */
    public function getValor()
    {
       return $this->valor;
    }

    /**
     * Set idejercicio
     *
     * @param \NomencladorBundle\Entity\NomEjercicio $idejercicio
     * @return DatPlanVenta
     */
    public function setIdejercicio(\NomencladorBundle\Entity\NomEjercicio $idejercicio = null)
    {
        $this->idejercicio = $idejercicio;

        return $this;
    }

    /**
     * Add idproducto
     *
     * @param \NomencladorBundle\Entity\NomProducto $idproducto
     * @return DatPlanVenta
     */
    public function addIdproducto(\NomencladorBundle\Entity\NomProducto $idproducto)
    {
        $this->idproducto[] = $idproducto;

        return $this;
    }

    /**
     * Remove idproducto
     *
     * @param \NomencladorBundle\Entity\NomProducto $idproducto
     */
    public function removeIdproducto(\NomencladorBundle\Entity\NomProducto $idproducto)
    {
        $this->idproducto->removeElement($idproducto);
    }

    /**
     * Set idueb
     *
     * @param \NomencladorBundle\Entity\NomUeb $idueb
     * @return DatPlanVenta
     */
    public function setIdueb(\NomencladorBundle\Entity\NomUeb $idueb = null)
    {
        $this->idueb = $idueb;
        $ueb = $this->getIdueb() == null ? 0 : $this->getIdueb()->getIdueb();
        $mon = $this->getIdmonedadestino() == null ? 0 : $this->getIdmonedadestino()->getId();
        $ex = $ueb . '-' . $mon;
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
     * Set identidad
     *
     * @param \NomencladorBundle\Entity\NomEntidad $identidad
     * @return DatPlanVenta
     */
    public function setIdentidad(\NomencladorBundle\Entity\NomEntidad $identidad = null)
    {
        $this->identidad = $identidad;

        return $this;
    }

    /**
     * Get identidad
     *
     * @return \NomencladorBundle\Entity\NomEntidad
     */
    public function getIdentidad()
    {
        return $this->identidad;
    }


    /**
     * Set idgrupocliente
     *
     * @param \NomencladorBundle\Entity\NomGrupointeres $idgrupointeres
     * @return DatPlanVenta
     */
    public function setIdgrupocliente(\NomencladorBundle\Entity\NomGrupointeres $idgrupocliente = null)
    {
        $this->idgrupocliente = $idgrupocliente;

        return $this;
    }

    /**
     * Get idgrupocliente
     *
     * @return \NomencladorBundle\Entity\NomGrupointeres
     */
    public function getIdgrupocliente()
    {
        return $this->idgrupocliente;
    }

    /**
     * Set idmonedadestino
     *
     * @param \NomencladorBundle\Entity\NomMonedadestino $idmonedadestino
     * @return DatPlanVenta
     */
    public function setIdmonedadestino(\NomencladorBundle\Entity\NomMonedadestino $idmonedadestino = null)
    {
        $this->idmonedadestino = $idmonedadestino;
        $ueb = $this->getIdueb() == null ? 0 : $this->getIdueb()->getIdueb();
        $mon = $this->getIdmonedadestino() == null ? 0 : $this->getIdmonedadestino()->getId();
        $ex = $ueb . '-' . $mon;
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
     * Set idpadre
     *
     * @param integer $idpadre
     * @return DatPlanVenta
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
     * Set hoja
     *
     * @param boolean $hoja
     * @return DatPlanVenta
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
     * Set is_val
     *
     * @param boolean $isVal
     * @return DatPlanVenta
     */
    public function setIsVal($isVal)
    {
        $this->is_val = $isVal;

        return $this;
    }

    /**
     * Get is_val
     *
     * @return boolean
     */
    public function getIsVal()
    {
        return $this->is_val;
    }

    public function getNombreEntidad()
    {
        return ' el plan de venta: Cliente: ' . $this->getIdgrupocliente()->getNombre() . " - Producto" . $this->getIdproducto()->getNombre();
    }

    /**
     * Set grupopadre
     *
     * @param \NomencladorBundle\Entity\NomGrupointeres $grupopadre
     * @return DatPlanVenta
     */
    public function setGrupopadre(\NomencladorBundle\Entity\NomGrupointeres $grupopadre = null)
    {
        $this->grupopadre = $grupopadre;
    
        return $this;
    }

    /**
     * Get grupopadre
     *
     * @return \NomencladorBundle\Entity\NomGrupointeres 
     */
    public function getGrupopadre()
    {
        return $this->grupopadre;
    }

    /**
     * Set grupoentidad
     *
     * @param \NomencladorBundle\Entity\NomGrupointeres $grupoentidad
     * @return DatPlanVenta
     */
    public function setGrupoentidad(\NomencladorBundle\Entity\NomGrupointeres $grupoentidad = null)
    {
        $this->grupoentidad = $grupoentidad;
    
        return $this;
    }

    /**
     * Get grupoentidad
     *
     * @return \NomencladorBundle\Entity\NomGrupointeres 
     */
    public function getGrupoentidad()
    {
        return $this->grupoentidad;
    }
}
