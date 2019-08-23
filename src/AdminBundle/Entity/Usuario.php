<?php

namespace AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Usuario
 *
 * @ORM\Table(name="adm_usuario")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\UsuarioRepository")
 * @UniqueEntity(fields= {"usuario"}, message="Existe un elemento con el mismo usuario.")
 * @UniqueEntity(fields= {"correo"}, message="Existe un elemento con el mismo correo.")
 */
class Usuario implements AdvancedUserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="idUsuario", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $idUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=100, unique=true)
     */
    private $usuario;

    /**
     * @var string
     ** @Assert\Length(
     *      min = "6",
     *      minMessage = "El valor mínimo del campo es 6.")
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=255, nullable=true)
     */
    private $correo;

    /**
     * @ORM\ManyToOne(targetEntity="Rol", inversedBy="usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idRol", referencedColumnName="idRol")
     * })
     * @Assert\NotBlank(message="Debe seleccionar un rol.")
     */
    protected $rol;

    /**
     * @var \NomencladorBundle\Entity\NomUeb
     *
     * @ORM\ManyToOne(targetEntity="\NomencladorBundle\Entity\NomUeb")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idueb", referencedColumnName="idueb", nullable=true)
     * })
     */
    private $ueb;

    /**
     * @ORM\ManyToMany(targetEntity="\ParteDiarioBundle\Entity\DatAlerta", mappedBy="usuarios")
     */
    protected $alertas;

    /**
     * @var string
     *
     * @ORM\Column(name="idsession", type="string", nullable=true)
     */
    private $idsession;

    /**
     * @var boolean
     *
     * @ORM\Column(name="logueado", type="boolean", nullable=true)
     */
    protected $logueado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechalogueo", type="datetime", nullable=true)
     */
    protected $fechaLogueo;

    /**
     * @var integer
     *
     * @ORM\Column(name="contbloqueo", type="integer", nullable=true)
     */
    protected $contbloqueo;


    public function __construct()
    {
        $this->activo = true;
        $this->alertas = new ArrayCollection();
    }

    function __toString()
    {
        return $this->usuario;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->idUsuario;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return bool
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set ueb
     *
     * @param \AdminBundle\Entity\Rol $rol
     * @return Usuario
     */
    public function setRol(\AdminBundle\Entity\Rol $rol = null)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get ueb
     *
     * @return \AdminBundle\Entity\Rol
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Set correo
     *
     * @param string $correo
     *
     * @return Usuario
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /** devuelve un valor que se utiliza para encriptar el password **/
    public function getSalt()
    {
        return false;
    }

    /** se invoca cuando el usuario cierra sesión **/
    public function eraseCredentials()
    {
        return false;
    }

    /**
     * Set ueb
     *
     * @param \NomencladorBundle\Entity\NomUeb $ueb
     * @return Usuario
     */
    public function setUeb(\NomencladorBundle\Entity\NomUeb $ueb = null)
    {
        $this->ueb = $ueb;

        return $this;
    }

    /**
     * Get ueb
     *
     * @return \NomencladorBundle\Entity\NomUeb
     */
    public function getUeb()
    {
        return $this->ueb;
    }

    /**
     * @return array|string[]
     */
    public function getRoles()
    {
        $rol = $this->getRol();
        if (null !== $rol) {
            $roles = $rol->getRoles();
        }

        return $roles;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        if ($this->contbloqueo > 4) {
            return false;
        } else {
            return true;
        }
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->activo;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->idUsuario,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->idUsuario,
            ) = unserialize($serialized);
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->usuario;
    }


    /**
     * Get idUsuario
     *
     * @return integer
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * Add alertas
     *
     * @param \ParteDiarioBundle\Entity\DatAlerta $alertas
     * @return Usuario
     */
    public function addAlerta(\ParteDiarioBundle\Entity\DatAlerta $alertas)
    {
        $this->alertas[] = $alertas;

        return $this;
    }

    /**
     * Remove alertas
     *
     * @param \ParteDiarioBundle\Entity\DatAlerta $alertas
     */
    public function removeAlerta(\ParteDiarioBundle\Entity\DatAlerta $alertas)
    {
        $this->alertas->removeElement($alertas);
    }

    /**
     * Get alertas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlertas()
    {
        return $this->alertas;
    }

    public function getNombreEntidad()
    {
        return ' el usuario: ' . $this->usuario;
    }


    /**
     * Set idsession
     *
     * @param string $idsession
     * @return Usuario
     */
    public function setIdsession($idsession)
    {
        $this->idsession = $idsession;

        return $this;
    }

    /**
     * Get idsession
     *
     * @return string
     */
    public function getIdsession()
    {
        return $this->idsession;
    }

    /**
     * Set logueado
     *
     * @param boolean $logueado
     * @return Usuario
     */
    public function setLogueado($logueado)
    {
        $this->logueado = $logueado;

        return $this;
    }

    /**
     * Get logueado
     *
     * @return boolean
     */
    public function getLogueado()
    {
        return $this->logueado;
    }

    /**
     * Set fechaLogueo
     *
     * @param \DateTime $fechaLogueo
     * @return Usuario
     */
    public function setFechaLogueo($fechaLogueo)
    {
        $this->fechaLogueo = $fechaLogueo;

        return $this;
    }

    /**
     * Get fechaLogueo
     *
     * @return \DateTime
     */
    public function getFechaLogueo()
    {
        return $this->fechaLogueo;
    }

    /**
     * Set contbloqueo
     *
     * @param integer $contbloqueo
     * @return Usuario
     */
    public function setContbloqueo($contbloqueo)
    {
        $this->contbloqueo = $contbloqueo;

        return $this;
    }

    /**
     * Get contbloqueo
     *
     * @return integer
     */
    public function getContbloqueo()
    {
        return $this->contbloqueo;
    }
}
