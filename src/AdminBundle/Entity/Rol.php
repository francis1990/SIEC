<?php

namespace AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Rol
 *
 * @ORM\Table(name="adm_rol")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\RolRepository")
 * @UniqueEntity(fields= {"descRol"}, message="Existe un elemento con la misma descripción.")
 */
class Rol implements RoleInterface
{
    const rolDefault = 'ROLE_USER';
    /**
     * @var int
     *
     * @ORM\Column(name="idRol", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $idRol;

    /**
     * @var string
     *
     * @ORM\Column(name="descRol", type="string", length=100, unique=true)
     */
    private $descRol;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;

    /**
     * @ORM\OneToMany(targetEntity="AdminBundle\Entity\Usuario", mappedBy="rol")
     */
    protected $usuarios;

    /**
     * @ORM\ManyToMany(targetEntity="Permiso", inversedBy="roles")
     * @ORM\JoinTable(name="adm_rol_permiso",
     * joinColumns={@ORM\JoinColumn(name="idRol", referencedColumnName="idRol")},
     * inverseJoinColumns={@ORM\JoinColumn(name="idPermiso", referencedColumnName="idPermiso")})
     */
    protected $permisos;


    public function __construct()
    {
        $this->activo = true;
        $this->usuarios = new ArrayCollection();
        $this->permisos = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->descRol;
    }

    public function __sleep()
    {
        return array('$id', 'descRol');
    }

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->descRol;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->idRol;
    }

    /**
     * Set descRol
     *
     * @param string $descRol
     *
     * @return Rol
     */
    public function setDescRol($descRol)
    {
        $this->descRol = $descRol;

        return $this;
    }

    /**
     * Get descRol
     *
     * @return string
     */
    public function getDescRol()
    {
        return $this->descRol;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Rol
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
     * Add permisos
     *
     * @param Permiso $permiso
     * @return Rol
     */
    public function addPermiso(Permiso $permiso)
    {
        $this->permisos[] = $permiso;
        return $this;
    }

    /**
     * Remove permisos
     *
     * @param Permiso $permiso
     */
    public function removePermiso(Permiso $permiso)
    {
        $this->permisos->removeElement($permiso);
    }

    /**
     * Get permisos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermisos()
    {
        return $this->permisos;
    }

    /**
     * @return array|string[]
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->getPermisos() as $permiso) {
            /** @var Permiso $permiso */
            $roles[] = $permiso->getAlias();
        }
        $roleDef = [self::rolDefault];
        $rolesRes = array_merge($roleDef, $roles);
        return array_unique($rolesRes);
    }

    public function getNombreEntidad()
    {
        return ' el rol: ' . $this->descRol;
    }

}

