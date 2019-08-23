<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * NomCuentacontable
 *
 * @ORM\Table(name="nom_cuentacontable")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 * @UniqueEntity(fields= {"numero"}, message="Existe un elemento con el mismo número.")
 * @UniqueEntity(fields= {"alias"}, errorPath="nombre",message="Existe un elemento con el mismo nombre.")
 */
class NomCuentacontable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idcuentacontable", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcuentacontable;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;
    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=16, nullable=false)
     *
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", nullable=false)
     *
     */
    private $alias;

    /**
     * @var bool
     *
     * @ORM\Column(name="ctaxcobrar", type="boolean")
     */
    private $porcobrar;

    /**
     * @var bool
     *
     * @ORM\Column(name="finanzas", type="boolean")
     */
    private $finanzas;

    /**
     * Get idcuentacontable
     *
     * @return integer 
     */
    public function getIdcuentacontable()
    {
        return $this->idcuentacontable;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return NomCuentacontable
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NomCuentacontable
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


    public function __toString()
    {
        return $this->getNombre();
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return NomCuentacontable
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
     * Set alias
     *
     * @param string $alias
     * @return NomCuentacontable
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
        return ' la cuenta contable: ' . $this->nombre;
    }

    /**
     * Set porcobrar
     *
     * @param boolean $porcobrar
     * @return NomCuentacontable
     */
    public function setPorcobrar($porcobrar)
    {
        $this->porcobrar = $porcobrar;
    
        return $this;
    }

    /**
     * Get porcobrar
     *
     * @return boolean 
     */
    public function getPorcobrar()
    {
        return $this->porcobrar;
    }

    /**
     * Set finanzas
     *
     * @param boolean $finanzas
     * @return NomCuentacontable
     */
    public function setFinanzas($finanzas)
    {
        $this->finanzas = $finanzas;
    
        return $this;
    }

    /**
     * Get finanzas
     *
     * @return boolean 
     */
    public function getFinanzas()
    {
        return $this->finanzas;
    }
}
