<?php

namespace NomencladorBundle\Entity;

use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomProducto
 *
 * @ORM\Table(name="nom_producto")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomProducto
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idproducto", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idproducto;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=14, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var \NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="umOperativa", referencedColumnName="idunidadmedida")
     * })
     */
    private $umOperativa;

    /**
     * @var integer
     *
     * @ORM\Column(name="factor", type="float", nullable=true, precision=10, scale=5)
     */
    private $factor;

    /**
     * @var string
     *
     * @ORM\Column(name="cod_onei", type="string", length=255, nullable=true)
     */
    private $codOnei;

    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer", nullable=false)
     */
    private $nivel;

    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer", nullable=true)
     */
    private $idpadre;

    /**
     * @var \NomSubgenerico
     *
     * @ORM\ManyToOne(targetEntity="NomSubgenerico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idsubgenerico", referencedColumnName="idsubgenerico")
     * })
     */
    private $idsubgenerico;

    /**
     * @var \NomGenerico
     *
     * @ORM\ManyToOne(targetEntity="NomGenerico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idgenerico", referencedColumnName="idgenerico")
     * })
     */
    private $idgenerico;

    /**
     * @var \NomTipoespecifico
     *
     * @ORM\ManyToOne(targetEntity="NomTipoespecifico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idtipoespecifico", referencedColumnName="idtipoespecifico")
     * })
     */
    private $idtipoespecifico;

    /**
     * @var \NomSabor
     *
     * @ORM\ManyToOne(targetEntity="NomSabor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idsabor", referencedColumnName="idsabor")
     * })
     */
    private $idsabor;

    /**
     * @var \NomFormato
     *
     * @ORM\ManyToOne(targetEntity="NomFormato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idformato", referencedColumnName="id")
     * })
     */
    private $idformato;

    /**
     * @var \NomEspecifico
     *
     * @ORM\ManyToOne(targetEntity="NomEspecifico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idespecifico", referencedColumnName="idespecifico")
     * })
     */
    private $idespecifico;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;


    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomProducto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->alias = Util::getSlug($nombre);
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        $this->nombre = $this->generarNombre();
        return $this->nombre;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomProducto
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return integer
     */
    public function getCodigo()
    {
        $this->codigo = $this->generarCodigo();
        return $this->codigo;
    }

    /**
     * Get idproducto
     *
     * @return integer
     */
    public function getIdproducto()
    {
        return $this->idproducto;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return NomProducto
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set codOnei
     *
     * @param string $codOnei
     * @return NomProducto
     */
    public function setCodOnei($codOnei)
    {
        $this->codOnei = $codOnei;

        return $this;
    }

    /**
     * Get codOnei
     *
     * @return string
     */
    public function getCodOnei()
    {
        return $this->codOnei;
    }

    /**
     * Set idpadre
     *
     * @param integer $idpadre
     * @return NomProducto
     */
    public function setIdpadre($idpadre)
    {
        $this->idpadre = $idpadre;

        return $this;
    }

    /**
     * Set idproducto
     *
     * @param integer $idproducto
     * @return NomProducto
     */
    public function setIdproducto($idproducto)
    {
        $this->idproducto = $idproducto;

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
     * Set idsubgenerico
     *
     * @param \NomencladorBundle\Entity\NomSubgenerico $idsubgenerico
     * @return NomProducto
     */
    public function setIdsubgenerico(\NomencladorBundle\Entity\NomSubgenerico $idsubgenerico = null)
    {
        $this->idsubgenerico = $idsubgenerico;

        return $this;
    }

    /**
     * Get idsubgenerico
     *
     * @return \NomencladorBundle\Entity\NomSubgenerico
     */
    public function getIdsubgenerico()
    {
        return $this->idsubgenerico;
    }

    /**
     * Set idgenerico
     *
     * @param \NomencladorBundle\Entity\NomGenerico $idgenerico
     * @return NomProducto
     */
    public function setIdgenerico(\NomencladorBundle\Entity\NomGenerico $idgenerico = null)
    {
        $this->idgenerico = $idgenerico;

        return $this;
    }

    /**
     * Get idgenerico
     *
     * @return \NomencladorBundle\Entity\NomGenerico
     */
    public function getIdgenerico()
    {
        return $this->idgenerico;
    }

    /**
     * Set idtipoespecifico
     *
     * @param \NomencladorBundle\Entity\NomTipoespecifico $idtipoespecifico
     * @return NomProducto
     */
    public function setIdtipoespecifico(\NomencladorBundle\Entity\NomTipoespecifico $idtipoespecifico = null)
    {
        $this->idtipoespecifico = $idtipoespecifico;

        return $this;
    }

    /**
     * Get idtipoespecifico
     *
     * @return \NomencladorBundle\Entity\NomTipoespecifico
     */
    public function getIdtipoespecifico()
    {
        return $this->idtipoespecifico;
    }

    /**
     * Set idsabor
     *
     * @param \NomencladorBundle\Entity\NomSabor $idsabor
     * @return NomProducto
     */
    public function setIdsabor(\NomencladorBundle\Entity\NomSabor $idsabor = null)
    {
        $this->idsabor = $idsabor;

        return $this;
    }

    /**
     * Get idsabor
     *
     * @return \NomencladorBundle\Entity\NomSabor
     */
    public function getIdsabor()
    {
        return $this->idsabor;
    }

    /**
     * Set idformato
     *
     * @param \NomencladorBundle\Entity\NomFormato $idformato
     * @return NomProducto
     */
    public function setIdformato(\NomencladorBundle\Entity\NomFormato $idformato = null)
    {
        $this->idformato = $idformato;

        return $this;
    }

    /**
     * Get idformato
     *
     * @return \NomencladorBundle\Entity\NomFormato
     */
    public function getIdformato()
    {
        return $this->idformato;
    }

    /**
     * Set idespecifico
     *
     * @param \NomencladorBundle\Entity\NomEspecifico $idespecifico
     * @return NomProducto
     */
    public function setIdespecifico(\NomencladorBundle\Entity\NomEspecifico $idespecifico = null)
    {
        $this->idespecifico = $idespecifico;

        return $this;
    }

    /**
     * Get idespecifico
     *
     * @return \NomencladorBundle\Entity\NomEspecifico
     */
    public function getIdespecifico()
    {
        return $this->idespecifico;
    }

    public function __toString()
    {

        return $this->generarNombre();
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomProducto
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }

    public function generarNombre()
    {
        $name = '';

        if ($this->getIdgenerico() != null && $this->getIdgenerico()->getCodigo() != '00')
            $name = $this->getIdgenerico()->getNombre();
        if ($this->getIdsubgenerico() != null && $this->getIdsubgenerico()->getCodigo() != '00')
            $name = $this->getIdsubgenerico()->getNombre();
        if ($this->getIdespecifico() != null && $this->getIdespecifico()->getCodigo() != '00')
            $name = $name . ' ' . $this->getIdespecifico()->getNombre();
        if ($this->getIdtipoespecifico() != null && $this->getIdtipoespecifico()->getCodigo() != '00')
            $name = $name . ' ' . $this->getIdtipoespecifico()->getNombre();
        if ($this->getIdsabor() != null && $this->getIdsabor()->getCodigo() != '000')
            $name = $name . ' ' . $this->getIdsabor()->getNombre();
        if ($this->getIdformato() != null && $this->getIdformato()->getCodigo() != '00')
            $name = $name . ' ' . $this->getIdformato()->getNombre();
        $this->nombre = $name;
        return $name;
    }

    public function generarCodigo()
    {
        $codigo = '';
        $this->getIdgenerico() != null ? $codigo = $codigo . $this->getIdgenerico()->getCodigo() : $codigo = $codigo . '00';
        $this->getIdsubgenerico() != null ? $codigo = $codigo . $this->getIdsubgenerico()->getCodigo() : $codigo = $codigo . '00';
        $this->getIdespecifico() != null ? $codigo = $codigo . $this->getIdespecifico()->getCodigo() : $codigo = $codigo . '00';
        $this->getIdtipoespecifico() != null ? $codigo = $codigo . $this->getIdtipoespecifico()->getCodigo() : $codigo = $codigo . '00';
        $this->getIdsabor() != null ? $codigo = $codigo . $this->getIdsabor()->getCodigo() : $codigo = $codigo . '000';
        $this->getIdformato() != null ? $codigo = $codigo . $this->getIdformato()->getCodigo() : $codigo = $codigo . '000';
        $this->codigo = $codigo;
        return $codigo;
    }


    /**
     * Set factor
     *
     * @param float $factor
     * @return NomProducto
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * Get factor
     *
     * @return float
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set umOperativa
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $umOperativa
     * @return NomProducto
     */
    public function setUmOperativa(\NomencladorBundle\Entity\NomUnidadmedida $umOperativa = null)
    {
        $this->umOperativa = $umOperativa;

        return $this;
    }

    /**
     * Get umOperativa
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getUmOperativa()
    {
        return $this->umOperativa;
    }

    /**
     * Set hoja
     *
     * @param boolean $hoja
     * @return NomProducto
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

    public function getUmVenta()
    {
        if ($this->getIdformato() != null)
            return $this->getIdformato()->getIdunidadmedida();
        else
            return null;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomProducto
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function getNombreEntidad()
    {
        return ' el producto: ' . $this->nombre;
    }

}
