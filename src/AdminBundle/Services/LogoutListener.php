<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 28/5/2018
 * Time: 12:33
 */

namespace AdminBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class LogoutListener implements LogoutHandlerInterface
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /**
     * Constructor
     *
     * @param SecurityContext $securityContext
     * @param Doctrine $doctrine
     */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine)
    {
        $this->securityContext = $securityContext;
        $this->em = $doctrine->getManager();
    }

    public function logout(Request $Request, Response $Response, TokenInterface $Token)
    {
        /*$user = $this->securityContext->getToken()->getUser();
        $user->setLogueado(false);
        $this->em->persist($user);
        $this->em->flush();*/
    }

}