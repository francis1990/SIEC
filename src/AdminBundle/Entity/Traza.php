<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Traza
 *
 * @ORM\Table(name="adm_traza")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\TrazaRepository")
 */
class Traza
{
    /**
     * @var int
     *
     * @ORM\Column(name="idTraza", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $idTraza;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="trazas")
     * @ORM\JoinColumn(name="idUsuario", referencedColumnName="idUsuario", nullable=true)
     */
    private $usuario;

    /**
     * @var \NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumn(name="idueb", referencedColumnName="idueb")
     */
    private $ueb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaCreacion", type="datetime")
     */
    private $fechaCreacion;

    /**
     * @var string
     *
     * @ORM\Column(name="descTraza", type="string", length=255)
     */
    private $descTraza;


    /**
     * Get idTraza
     *
     * @return int
     */
    public function getId()
    {
        return $this->idTraza;
    }

    /**
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Traza
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set descTraza
     *
     * @param string $descTraza
     *
     * @return Traza
     */
    public function setDescTraza($descTraza)
    {
        $this->descTraza = $descTraza;

        return $this;
    }

    /**
     * Get descTraza
     *
     * @return string
     */
    public function getDescTraza()
    {
        return $this->descTraza;
    }

    /**
     * Set usuario
     *
     * @param Usuario $usuario
     * @return Traza
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @return int
     */
    public function getIdTraza()
    {
        return $this->idTraza;
    }

    /**
     * @param int $idTraza
     */
    public function setIdTraza($idTraza)
    {
        $this->idTraza = $idTraza;
    }

    /**
     * @return \NomUeb
     */
    public function getUeb()
    {
        return $this->ueb;
    }

    /**
     * @param \NomUeb $ueb
     */
    public function setUeb($ueb)
    {
        $this->ueb = $ueb;
    }

}

