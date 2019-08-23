<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NomTipoPortador
 *
 * @ORM\Table(name="nom_tipo_portador")
 * @ORM\Entity(repositoryClass="NomencladorBundle\Entity\ComunRepository")
 */
class NomTipoPortador
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idtipoportador", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtipoportador;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message = "El valor no puede estar en blanco.")
     */
    private $nombre;
    /**
     * Get idtipoportador
     *
     * @return integer 
     */
    public function getIdtipoportador()
    {
        return $this->idtipoportador;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomTipoPortador
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
     * @return NomTipoPortador
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

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
}
