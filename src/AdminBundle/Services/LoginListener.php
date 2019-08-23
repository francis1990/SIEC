<?php
/**
 * Created by PhpStorm.
 * User: edilio
 * Date: 28/5/2018
 * Time: 11:40
 */

namespace AdminBundle\Services;

use Doctrine\Common\Util\Debug;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Validator\Constraints\DateTime;

class LoginListener
{
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    private $userLog;

    private $session;

    /**
     * Constructor
     *
     * @param SecurityContext $securityContext
     * @param Doctrine $doctrine
     */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine, Router $router)
    {
        $this->securityContext = $securityContext;
        $this->router = $router;
        $this->em = $doctrine->getManager();
        $this->session = $GLOBALS['kernel']->getContainer()->get('session');
    }

    /**
     * Do the magic.
     *
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $this->userLog = $user;
        $user->setIdsession($this->session->getId());
        $user->setContbloqueo(0);
        //$user->setLogueado(false);
        $datconfig = $this->em->getRepository('AdminBundle:DatConfig')->find(1);
        $datconfig->setFechaTrabajo(new \DateTime());
        $this->em->persist($datconfig);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        return;
    }

}