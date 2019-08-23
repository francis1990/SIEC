<?php

namespace NomencladorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NomTipoLeche
 *
 * @ORM\Table(name="nom_tipo_leche")
 * @ORM\Entity
 */
class NomTipoLeche
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idtipoleche", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtipoleche;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, nullable=true)
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
     * Get idtipoleche
     *
     * @return integer 
     */
    public function getIdtipoleche()
    {
        return $this->idtipoleche;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return NomTipoLeche
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
     * @return NomTipoLeche
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
