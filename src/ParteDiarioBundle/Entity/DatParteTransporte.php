<?php
/**
 * Created by PhpStorm.
 * User: jorge
 * Date: 23/11/2016
 * Time: 14:37
 */

namespace ParteDiarioBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomTipotransporte;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * DatParteTransporte
 *
 * @ORM\Table(name="dat_parte_transporte")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteTransporteRepository")
 * @UniqueEntity(fields= {"fecha", "ueb", "tipotransporte"}, message="Existe un elemento con fecha, UEB y tipo transporte seleccionado.")
 */
class DatParteTransporte extends DatParte
{

    /**
     * @var NomTipotransporte
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomTipotransporte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transporte", referencedColumnName="id")
     * })
     */
    private $tipotransporte;

    /**
     * @var integer
     *
     * @ORM\Column(name="existencia", type="integer", nullable=false)
     * @Assert\Range(
     *      min = "1",
     *      minMessage = "El valor mínimo del campo es 1")
     */
    private $existencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="disponible", type="integer", nullable=true)
     */
    private $disponible;

    /**
     * @var float
     *
     * @ORM\Column(name="cdt", type="float", nullable=true)
     */
    private $cdt;


    /**
     * Set existencia
     *
     * @param integer $existencia
     * @return DatParteTransporte
     */
    public function setExistencia($existencia)
    {
        $this->existencia = $existencia;
        return $this;
    }

    /**
     * Get existencia
     *
     * @return integer
     */
    public function getExistencia()
    {
        return $this->existencia;
    }

    /**
     * Set disponible
     *
     * @param integer $disponible
     * @return DatParteTransporte
     */
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;

        return $this;
    }

    /**
     * Get disponible
     *
     * @return integer
     */
    public function getDisponible()
    {
        return $this->disponible;
    }

    /**
     * Set cdt
     *
     * @param float $cdt
     * @return DatParteTransporte
     */
    public function setCdt($cdt)
    {
        $this->cdt = $cdt;

        return $this;
    }

    /**
     * Get cdt
     *
     * @return float
     */
    public function getCdt()
    {
        if ($this->getExistencia() != 0) {
            return number_format($this->getDisponible() * 100 / $this->getExistencia(), 2, '.', ',');
        } else {
            return 0;
        }
    }

    /**
     * Set tipotransporte
     *
     * @param \NomencladorBundle\Entity\NomTipotransporte $tipotransporte
     * @return DatParteTransporte
     */
    public function setTipotransporte(\NomencladorBundle\Entity\NomTipotransporte $tipotransporte = null)
    {
        $this->tipotransporte = $tipotransporte;

        return $this;
    }

    /**
     * Get tipotransporte
     *
     * @return \NomencladorBundle\Entity\NomTipotransporte
     */
    public function getTipotransporte()
    {
        return $this->tipotransporte;
    }

    public function getNombreEntidad()
    {
        return ' el parte de transporte: Tipo de Transporte: ' . $this->getTipotransporte()->getNombre();
    }

}
