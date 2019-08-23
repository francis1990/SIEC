<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Modulo
 *
 * @ORM\Table(name="adm_modulo")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\ModuloRepository")
 */
class Modulo
{
    /**
     * @var int
     *
     * @ORM\Column(name="idModulo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $idModulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descModulo", type="string", length=255, unique=true)
     */
    private $descModulo;

    /**
     * @ORM\OneToMany(targetEntity="Permiso", mappedBy="modulo")
     */
    protected $permisos;


    public function __toString(){
        return $this->descModulo;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->idModulo;
    }

    /**
     * Set descModulo
     *
     * @param string $descModulo
     *
     * @return Modulo
     */
    public function setDescModulo($descModulo)
    {
        $this->descModulo = $descModulo;

        return $this;
    }

    /**
     * Get descModulo
     *
     * @return string
     */
    public function getDescModulo()
    {
        return $this->descModulo;
    }
}

