<?php

namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use NomencladorBundle\Util\Util;

/**
 * Permiso
 *
 * @ORM\Table(name="adm_permiso")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\PermisoRepository")
 */
class Permiso
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPermiso", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $idPermiso;

    /**
     * @var string
     *
     * @ORM\Column(name="descPermiso", type="string", length=255, unique=true)
     */
    private $descPermiso;

    /**
     * @ORM\ManyToMany(targetEntity="Rol", mappedBy="permisos")
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    private $alias;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->idPermiso;
    }

    /**
     * Set descPermiso
     *
     * @param string $descPermiso
     *
     * @return Permiso
     */
    public function setDescPermiso($descPermiso)
    {
        $this->descPermiso = $descPermiso;
        $this->alias = 'ROLE_' . Util::getSlugUpper($this->descPermiso,$separador = '_');
        return $this;
    }

    /**
     * Get descPermiso
     *
     * @return string
     */
    public function getDescPermiso()
    {
        return $this->descPermiso;
    }

    /**
     * Add roles
     *
     * @param \AdminBundle\Entity\Rol $roles
     * @return Permiso
     */
    public function addRole(Rol $roles)
    {
        $this->roles[] = $roles;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \AdminBundle\Entity\Rol $roles
     */
    public function removeRole(Rol $roles)
    {
        $this->roles->removeElement($roles);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getDescPermiso();
    }

    /**
     * Get idPermiso
     *
     * @return integer
     */
    public function getIdPermiso()
    {
        return $this->idPermiso;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Permiso
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
}
