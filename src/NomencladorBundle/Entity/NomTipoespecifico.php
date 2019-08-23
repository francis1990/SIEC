<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomTipoespecifico
 *
 * @ORM\Table(name="nom_tipoespecifico")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias"},errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomTipoespecifico
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idtipoespecifico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtipoespecifico;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     * @Assert\Regex(pattern="/\d{2}/",message="El valor debe ser un dígito.")
     * @Assert\Length(max=2,min=2, exactMessage = "El valor debe tener exactamente {{ limit }} caracteres.")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco")
     */
    private $nombre;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="NomEspecifico", inversedBy="idtipoespecifico")
     * @ORM\JoinTable(name="nom_especifico_tipoespecifico",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idtipoespecifico", referencedColumnName="idtipoespecifico", onDelete="cascade")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idespecifico", referencedColumnName="idespecifico")
     *   }
     * )
     */
    private $idespecifico;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;



    public function __toString()
    {
        return $this->getNombre();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idespecifico = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get idtipoespecifico
     *
     * @return integer 
     */
    public function getIdtipoespecifico()
    {
        return $this->idtipoespecifico;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomTipoespecifico
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomTipoespecifico
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->alias =  Util::getSlug($nombre);
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }


    /**
     * Add idespecifico
     *
     * @param \NomencladorBundle\Entity\NomEspecifico $idespecifico
     * @return NomTipoespecifico
     */
    public function addIdespecifico(\NomencladorBundle\Entity\NomEspecifico $idespecifico)
    {
        $this->idespecifico[] = $idespecifico;

        return $this;
    }

    /**
     * Remove idespecifico
     *
     * @param \NomencladorBundle\Entity\NomEspecifico $idespecifico
     */
    public function removeIdespecifico(\NomencladorBundle\Entity\NomEspecifico $idespecifico)
    {
        $this->idespecifico->removeElement($idespecifico);
    }

    /**
     * Get idespecifico
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdespecifico()
    {
        return $this->idespecifico;
    }



    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomTipoespecifico
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

    public function getAsociados(){
        $resp='';
        $asoc=  $this->getIdespecifico();
        foreach($asoc as $a){
            if($resp=='')
                $resp =$a->getIdespecifico();
            else
                $resp =$resp.'-'.$a->getIdespecifico();
        }
        return $resp;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomTipoespecifico
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
        return ' el tipo específico: ' . $this->nombre;
    }

}
