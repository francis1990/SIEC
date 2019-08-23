<?php

namespace NomencladorBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * Nomconversion
 *
 * @ORM\Table(name="nom_conversion")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Repository\NomConversionRepository")
 * @UniqueEntity(fields= {"iduminicio","idumfin"}, message="Existe un elemento con las mismas Unidades de Medidas.")
 */
class NomConversion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idconversion", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idconversion;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var float
     *
     * @ORM\Column(name="factor", type="float", nullable=false)
     * @Assert\NotBlank(message = "El campo es requerido.")
     * @Assert\GreaterThan(
     * value = 0,
     * message = "El valor debe ser mayor que 0."
     * )
     *
     */
    private $factor;

    /**
     * @var \NomencladorBundle\Entity\NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idumfin", referencedColumnName="idunidadmedida")
     * })
     */
    private $idumfin;
    /**
     * @var \NomencladorBundle\Entity\NomUnidadmedida
     *
     * @ORM\ManyToOne(targetEntity="NomUnidadmedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iduminicio", referencedColumnName="idunidadmedida")
     * })
     */
    private $iduminicio;

    /**
     * Get idconversion
     *
     * @return integer
     */
    public function getIdconversion()
    {
        return $this->idconversion;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomConversion
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
     * Set factor
     *
     * @param float $factor
     * @return NomConversion
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
     * Set idumfin
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $idumfin
     * @return NomConversion
     */
    public function setIdumfin(\NomencladorBundle\Entity\NomUnidadmedida $idumfin = null)
    {
        $this->idumfin = $idumfin;

        return $this;
    }

    /**
     * Get idumfin
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getIdumfin()
    {
        return $this->idumfin;
    }

    /**
     * Set iduminicio
     *
     * @param \NomencladorBundle\Entity\NomUnidadmedida $iduminicio
     * @return NomConversion
     */
    public function setIduminicio(\NomencladorBundle\Entity\NomUnidadmedida $iduminicio = null)
    {
        $this->iduminicio = $iduminicio;

        return $this;
    }

    /**
     * Get iduminicio
     *
     * @return \NomencladorBundle\Entity\NomUnidadmedida
     */
    public function getIduminicio()
    {
        return $this->iduminicio;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getIduminicio()->getIdunidadmedida() == $this->getIdumfin()->getIdunidadmedida()) {
            $context->buildViolation('Las unidades de medidas no pueden ser las mismas.')
                ->atPath('iduminicio')
                ->addViolation();
        }
    }

    public function getNombreEntidad()
    {
        $umInicial = $this->iduminicio->getNombre();
        $umFinal = $this->idumfin->getNombre();
        return ' la conversión: ' . $umInicial . "-" . $umFinal;
    }

}
