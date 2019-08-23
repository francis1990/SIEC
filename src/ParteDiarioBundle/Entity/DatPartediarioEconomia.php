<?php
/**
 * Created by PhpStorm.
 * User: francis
 * Date: 20/01/2017
 * Time: 9:02
 */

namespace ParteDiarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Entity\NomCuentacontable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use NomencladorBundle\Entity\NomUeb;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * DatPartediarioEconomia
 *
 * @ORM\Table(name="dat_parte_economia")
 * @ORM\Entity(repositoryClass="ParteDiarioBundle\Repository\DatParteEconomiaRepository")
 * @UniqueEntity(fields= {"fecha", "ueb", "idcuentacontable"}, message="Existe un elemento con fecha, UEB y cuenta seleccionado.")
 */
class DatPartediarioEconomia extends DatParte
{

    /**
     * @var float
     *
     * @ORM\Column(name="saldo", type="float",  nullable=true)
     * @Assert\Range(
     *      min = "0",
     *      minMessage = "El valor mínimo del campo es 0.")
     *
     */
    private $saldo;

    /**
     * @ORM\ManyToOne(targetEntity="NomencladorBundle\Entity\NomCuentacontable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcuentacontable", referencedColumnName="idcuentacontable")
     * })
     */
    private $idcuentacontable;


    /**
     * Set saldo
     *
     * @param float $saldo
     * @return DatPartediarioEconomia
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return float 
     */
    public function getSaldo()
    {
        return $this->saldo;
    }

    /**
     * Set idcuentacontable
     *
     * @param \NomencladorBundle\Entity\NomCuentacontable $idcuentacontable
     * @return DatPartediarioEconomia
     */
    public function setIdcuentacontable(\NomencladorBundle\Entity\NomCuentacontable $idcuentacontable = null)
    {
        $this->idcuentacontable = $idcuentacontable;

        return $this;
    }

    /**
     * Get idcuentacontable
     *
     * @return \NomencladorBundle\Entity\NomCuentacontable 
     */
    public function getIdcuentacontable()
    {
        return $this->idcuentacontable;
    }

    public function getNombreEntidad()
    {
        return ' el parte de economía: Cuenta: ' . $this->getIdcuentacontable()->getNombre();
    }

}
