<?php

namespace NomencladorBundle\Entity;

use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * NomDpa
 *
 * @ORM\Table(name="nom_dpa")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"codigo"}, message="Existe un elemento con el mismo código.")
 * @UniqueEntity(fields= {"alias","idpadre"}, errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomDpa
{
    /**
     * @var integer
     *
     * @ORM\Column(name="iddpa", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iddpa;
   

    /**
     * @var integer
     *
     * @ORM\Column(name="nivel", type="integer", nullable=false)
     */
    private $nivel;
    /**
     * @var bool
     *
     * @ORM\Column(name="hoja", type="boolean")
     */
    private $hoja;
    /**
     * @var integer
     *
     * @ORM\Column(name="idpadre", type="integer")
     */
    private $idpadre;
    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=6, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     *
     */
    private $codigo;
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;


    /**
     * @ORM\OneToMany(targetEntity="NomDpa", mappedBy="padre")
     **/
    private $hijos;


    /**
     * @ORM\ManyToOne(targetEntity="NomDpa", inversedBy="hijos")
     * @ORM\JoinColumn(name="padre_id", referencedColumnName="iddpa")
     **/
    private $padre;


    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var integer
     *
     * @ORM\Column(name="prioridad", type="integer",nullable = true)
     */
    private $prioridad;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;



    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomDpa
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        $this->alias = Util::getSlug($nombre);
        if (is_null($this->getPadre())) {
            $this->setIdpadre(0);
        }
        return $this;
    }



    public function __toString()
    {
        return $this->getNombre();
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->hijos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get iddpa
     *
     * @return integer 
     */
    public function getIddpa()
    {
        return $this->iddpa;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return NomDpa
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
     * Set hoja
     *
     * @param boolean $hoja
     * @return NomDpa
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
     * Set idpadre
     *
     * @param integer $idpadre
     * @return NomDpa
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
     * Set codigo
     *
     * @param string $codigo
     * @return NomDpa
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
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomDpa
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
     * Set prioridad
     *
     * @param integer $prioridad
     * @return NomDpa
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return integer 
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return NomDpa
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

    /**
     * Add hijos
     *
     * @param \NomencladorBundle\Entity\NomDpa $hijos
     * @return NomDpa
     */
    public function addHijo(\NomencladorBundle\Entity\NomDpa $hijos)
    {
        $this->hijos[] = $hijos;

        return $this;
    }

    /**
     * Remove hijos
     *
     * @param \NomencladorBundle\Entity\NomDpa $hijos
     */
    public function removeHijo(\NomencladorBundle\Entity\NomDpa $hijos)
    {
        $this->hijos->removeElement($hijos);
    }

    /**
     * Get hijos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHijos()
    {
        return $this->hijos;
    }

    /**
     * Set padre
     *
     * @param \NomencladorBundle\Entity\NomDpa $padre
     * @return NomDpa
     */
    public function setPadre(\NomencladorBundle\Entity\NomDpa $padre = null)
    {
        $this->padre = $padre;
        return $this;
    }

    /**
     * Get padre
     *
     * @return \NomencladorBundle\Entity\NomDpa 
     */
    public function getPadre()
    {
        return $this->padre;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getPadre()!=null && $this->getPadre()->getIddpa()==$this->getIddpa()) {
            $context->buildViolation('No se puede asignar como Dpa Superior el mismo elemento')
                ->atPath('padre')
                ->addViolation();
        }
    }

    public function getNombreEntidad()
    {
        return ' el DPA: ' . $this->nombre;
    }
}
