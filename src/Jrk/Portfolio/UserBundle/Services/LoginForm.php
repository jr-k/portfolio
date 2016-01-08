<?php

namespace Jrk\Portfolio\UserBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;


class LoginForm extends \Twig_Extension
{
    protected $requestStack;
    protected $formFactory;
    protected $translationMap;
    protected $entityManager;
    protected $session;
    protected $templating;

    /*
     * Bad practice : Container Injection
     * /!\ Special Case : http://symfony.com/fr/doc/current/cookbook/service_container/scopes.html
     */
    public function __construct($translationMap,$requestStack, ContainerInterface $container)
    {
        $this->requestStack = $requestStack;
        $this->session = $container->get('session');
        $this->container = $container;
        $this->translationMap = $translationMap["front_map"];;
    }



    public function getGlobals()
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $this->session;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContextInterface::LAST_USERNAME);

        $csrfToken = $this->container->has('form.csrf_provider')
            ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;

        if ($error == 'The user provider must return a UserInterface object.') {
            $error = 'auth_bad_credentials';
        }


        return array(
            'fos_last_username' => $lastUsername,
            'fos_error'         => $error,
            'fos_csrf_token' => $csrfToken,
        );
    }



    public function getName()
    {
        return "off.login_form";
    }

}