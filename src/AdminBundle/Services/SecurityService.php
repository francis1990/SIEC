<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 11/03/2016
 * Time: 03:12
 */

namespace AdminBundle\Services;


use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityService
{
    private $em;
    private $segurity;

    public function __construct()
    {
        $this->em = $GLOBALS['kernel']->getContainer()->get('doctrine');
        $this->segurity = $GLOBALS['kernel']->getContainer()->get('security.context');
    }

    public function ChequearModulo($modulos, $desModulo)
    {
        $noPermiso = true;
        foreach ($modulos as $modulo) {
            if ($modulo["descModulo"] == $desModulo) {
                $noPermiso = false;
                break;
            }
        }
        return $noPermiso;
    }

    public function ChequearSession($session)
    {
        $user = $this->em->getRepository("AdminBundle:Usuario")->findOneBy(array(
            'idUsuario' => $this->segurity->getToken()->getUser()->getIdUsuario()
        ));
        if ($user->getIdsession() != $session->getId()) {
            return false;
        }
        return true;
    }


}