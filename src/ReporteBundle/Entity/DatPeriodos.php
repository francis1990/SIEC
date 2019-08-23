<?php

namespace ReporteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DatPeriodos
 *
 * @ORM\Table(name="rep_dat_periodos")
 * @ORM\Entity(repositoryClass="ReporteBundle\Repository\DatPeriodosRepository")
 */
class DatPeriodos
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ident", type="string", length=255)
     */
    private $ident;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     */
    private $descripcion;

    /**
     * @var NomPeriodos
     *
     * @ORM\ManyToOne(targetEntity="NomPeriodos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="diai", referencedColumnName="id")
     * })
     */
    private $diai;

    /**
     * @var int
     *
     * @ORM\Column(name="diaiv", type="integer")
     */
    private $diaiv;

    /**
     * @var NomPeriodos
     *
     * @ORM\ManyToOne(targetEntity="NomPeriodos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mesi", referencedColumnName="id")
     * })
     */
    private $mesi;

    /**
     * @var int
     *
     * @ORM\Column(name="mesiv", type="integer")
     */
    private $mesiv;

    /**
     * @var NomPeriodos
     *
     * @ORM\ManyToOne(targetEntity="NomPeriodos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="anoi", referencedColumnName="id")
     * })
     */
    private $anoi;

    /**
     * @var int
     *
     * @ORM\Column(name="anoiv", type="integer")
     */
    private $anoiv;

    /**
     * @var NomPeriodos
     *
     * @ORM\ManyToOne(targetEntity="NomPeriodos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="diaf", referencedColumnName="id")
     * })
     */
    private $diaf;

    /**
     * @var int
     *
     * @ORM\Column(name="diafv", type="integer")
     */
    private $diafv;

    /**
     * @var NomPeriodos
     *
     * @ORM\ManyToOne(targetEntity="NomPeriodos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mesf", referencedColumnName="id")
     * })
     */
    private $mesf;

    /**
     * @var int
     *
     * @ORM\Column(name="mesfv", type="integer")
     */
    private $mesfv;

    /**
     * @var NomPeriodos
     *
     * @ORM\ManyToOne(targetEntity="NomPeriodos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="anof", referencedColumnName="id")
     * })
     */
    private $anof;

    /**
     * @var int
     *
     * @ORM\Column(name="anofv", type="integer")
     */
    private $anofv;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;


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
     * Set ident
     *
     * @param string $ident
     * @return DatPeriodos
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;
    
        return $this;
    }

    /**
     * Get ident
     *
     * @return string 
     */
    public function getIdent()
    {
        return $this->ident;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return DatPeriodos
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
     * Set diaiv
     *
     * @param integer $diaiv
     * @return DatPeriodos
     */
    public function setDiaiv($diaiv)
    {
        $this->diaiv = $diaiv;
    
        return $this;
    }

    /**
     * Get diaiv
     *
     * @return integer 
     */
    public function getDiaiv()
    {
        return $this->diaiv;
    }

    /**
     * Set mesiv
     *
     * @param integer $mesiv
     * @return DatPeriodos
     */
    public function setMesiv($mesiv)
    {
        $this->mesiv = $mesiv;
    
        return $this;
    }

    /**
     * Get mesiv
     *
     * @return integer 
     */
    public function getMesiv()
    {
        return $this->mesiv;
    }


    /**
     * Set anoiv
     *
     * @param integer $anoiv
     * @return DatPeriodos
     */
    public function setAnoiv($anoiv)
    {
        $this->anoiv = $anoiv;
    
        return $this;
    }

    /**
     * Get anoiv
     *
     * @return integer 
     */
    public function getAnoiv()
    {
        return $this->anoiv;
    }


    /**
     * Set diafv
     *
     * @param integer $diafv
     * @return DatPeriodos
     */
    public function setDiafv($diafv)
    {
        $this->diafv = $diafv;
    
        return $this;
    }

    /**
     * Get diafv
     *
     * @return integer 
     */
    public function getDiafv()
    {
        return $this->diafv;
    }

    /**
     * Set mesfv
     *
     * @param integer $mesfv
     * @return DatPeriodos
     */
    public function setMesfv($mesfv)
    {
        $this->mesfv = $mesfv;
    
        return $this;
    }

    /**
     * Get mesfv
     *
     * @return integer 
     */
    public function getMesfv()
    {
        return $this->mesfv;
    }


    /**
     * Set anofv
     *
     * @param integer $anofv
     * @return DatPeriodos
     */
    public function setAnofv($anofv)
    {
        $this->anofv = $anofv;
    
        return $this;
    }

    /**
     * Get anofv
     *
     * @return integer 
     */
    public function getAnofv()
    {
        return $this->anofv;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return DatPeriodos
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

    /**
     * Set diai
     *
     * @param \ReporteBundle\Entity\NomPeriodos $diai
     * @return DatPeriodos
     */
    public function setDiai(\ReporteBundle\Entity\NomPeriodos $diai = null)
    {
        $this->diai = $diai;
    
        return $this;
    }

    /**
     * Get diai
     *
     * @return \ReporteBundle\Entity\NomPeriodos 
     */
    public function getDiai()
    {
        return $this->diai;
    }

    /**
     * Set mesi
     *
     * @param \ReporteBundle\Entity\NomPeriodos $mesi
     * @return DatPeriodos
     */
    public function setMesi(\ReporteBundle\Entity\NomPeriodos $mesi = null)
    {
        $this->mesi = $mesi;
    
        return $this;
    }

    /**
     * Get mesi
     *
     * @return \ReporteBundle\Entity\NomPeriodos 
     */
    public function getMesi()
    {
        return $this->mesi;
    }

    /**
     * Set anoi
     *
     * @param \ReporteBundle\Entity\NomPeriodos $anoi
     * @return DatPeriodos
     */
    public function setAnoi(\ReporteBundle\Entity\NomPeriodos $anoi = null)
    {
        $this->anoi = $anoi;
    
        return $this;
    }

    /**
     * Get anoi
     *
     * @return \ReporteBundle\Entity\NomPeriodos 
     */
    public function getAnoi()
    {
        return $this->anoi;
    }

    /**
     * Set diaf
     *
     * @param \ReporteBundle\Entity\NomPeriodos $diaf
     * @return DatPeriodos
     */
    public function setDiaf(\ReporteBundle\Entity\NomPeriodos $diaf = null)
    {
        $this->diaf = $diaf;
    
        return $this;
    }

    /**
     * Get diaf
     *
     * @return \ReporteBundle\Entity\NomPeriodos 
     */
    public function getDiaf()
    {
        return $this->diaf;
    }

    /**
     * Set mesf
     *
     * @param \ReporteBundle\Entity\NomPeriodos $mesf
     * @return DatPeriodos
     */
    public function setMesf(\ReporteBundle\Entity\NomPeriodos $mesf = null)
    {
        $this->mesf = $mesf;
    
        return $this;
    }

    /**
     * Get mesf
     *
     * @return \ReporteBundle\Entity\NomPeriodos 
     */
    public function getMesf()
    {
        return $this->mesf;
    }

    /**
     * Set anof
     *
     * @param \ReporteBundle\Entity\NomPeriodos $anof
     * @return DatPeriodos
     */
    public function setAnof(\ReporteBundle\Entity\NomPeriodos $anof = null)
    {
        $this->anof = $anof;
    
        return $this;
    }

    /**
     * Get anof
     *
     * @return \ReporteBundle\Entity\NomPeriodos 
     */
    public function getAnof()
    {
        return $this->anof;
    }
    public function __toString()
    {
        return $this->getIdent();
    }
}
