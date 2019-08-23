<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 03/02/2017
 * Time: 12:28
 */

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\DatNormaAseguramiento;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use ParteDiarioBundle\Entity\DatPartediarioProduccion;

/**
 * DatConsumoAseguramiento
 *
 * @ORM\Table(name="dat_parteconsumo_aseg")
 * @ORM\Entity
 */
class DatConsumoAseguramiento
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idconsumoaseg", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idconsumoaseg;

    /**
     * @var DatParteDiarioConsAseg
     *
     * @ORM\ManyToOne(targetEntity="\ParteDiarioBundle\Entity\DatParteDiarioConsAseg", inversedBy="consumos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parte", referencedColumnName="idparte", onDelete="cascade")
     * })
     */
    private $parte;

    /**
     * @var DatNormaAseguramiento
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\DatNormaAseguramiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idnormaaseguramiento", referencedColumnName="idnormaaseg")
     * })
     */
    private $aseguramiento;

    /**
     * @var float
     *
     * @ORM\Column(name="realbruto", type="float",  nullable=true)
     */
    private $realbruto;

    /**
     * @var float
     *
     * @ORM\Column(name="grasa", type="float",  nullable=true)
     */
    private $grasa;

    /**
     * @var float
     *
     * @ORM\Column(name="sng", type="float",  nullable=true)
     */
    private $sng;


    /**
     * Get idconsumoaseg
     *
     * @return integer 
     */
    public function getIdconsumoaseg()
    {
        return $this->idconsumoaseg;
    }

    /**
     * Set realbruto
     *
     * @param float $realbruto
     * @return DatConsumoAseguramiento
     */
    public function setRealbruto($realbruto)
    {
        $this->realbruto = $realbruto;
    
        return $this;
    }

    /**
     * Get realbruto
     *
     * @return float 
     */
    public function getRealbruto()
    {
        return $this->realbruto;
    }

    /**
     * Set grasa
     *
     * @param float $grasa
     * @return DatConsumoAseguramiento
     */
    public function setGrasa($grasa)
    {
        $this->grasa = $grasa;
    
        return $this;
    }

    /**
     * Get grasa
     *
     * @return float 
     */
    public function getGrasa()
    {
        return $this->grasa;
    }

    /**
     * Set sng
     *
     * @param float $sng
     * @return DatConsumoAseguramiento
     */
    public function setSng($sng)
    {
        $this->sng = $sng;
    
        return $this;
    }

    /**
     * Get sng
     *
     * @return float 
     */
    public function getSng()
    {
        return $this->sng;
    }

    /**
     * Set parte
     *
     * @param \ParteDiarioBundle\Entity\DatParteDiarioConsAseg $parte
     * @return DatConsumoAseguramiento
     */
    public function setParte(\ParteDiarioBundle\Entity\DatParteDiarioConsAseg $parte = null)
    {
        $this->parte = $parte;
    
        return $this;
    }

    /**
     * Get parte
     *
     * @return \ParteDiarioBundle\Entity\DatParteDiarioConsAseg 
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * Set aseguramiento
     *
     * @param \NomencladorBundle\Entity\DatNormaAseguramiento $aseguramiento
     * @return DatConsumoAseguramiento
     */
    public function setAseguramiento(\NomencladorBundle\Entity\DatNormaAseguramiento $aseguramiento = null)
    {
        $this->aseguramiento = $aseguramiento;
    
        return $this;
    }

    /**
     * Get aseguramiento
     *
     * @return \NomencladorBundle\Entity\DatNormaAseguramiento 
     */
    public function getAseguramiento()
    {
        return $this->aseguramiento;
    }
}
