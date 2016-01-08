<?php

namespace Jrk\Portfolio\CoreBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GlobalConfiguration extends \Twig_Extension
{
    protected $entityManager;
    protected $locale;
    protected $translationMap;
    protected $container;
    protected $securityContext;
    public $globals;
    public $user = null;

    /*
     * Bad practice : Container Injection
     * /!\ Special Case : http://symfony.com/fr/doc/current/cookbook/service_container/scopes.html
     */
    public function __construct($translationMap,$entityManager,ContainerInterface $container,$securityContext)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->securityContext = $securityContext;
        $this->translationMap = $translationMap['front_map'];
        $this->globals = array();
    }



    public function getGlobals()
    {
        $this->processLocales();
        $this->{'processUser'. (is_object($this->securityContext->getToken()) ? 'In' :'Out')}();
        return $this->globals;
    }


    public function processLocales() {
        $this->locale = $this->container->get('request')->getLocale();
        $configs = $this->entityManager->getRepository('JrkPortfolioCoreBundle:Configuration')->getConfigurationsByLocale($this->locale);

        // Inject each db configurations in globals
        foreach($configs as $config) {
            $this->globals['CFG_DESC_'.$config->getName()] = $config->getDescription();
            $this->globals['CFG_HELP_'.$config->getName()] = $config->getHelp();
            $this->globals['CFG_FILE_'.$config->getName()] = $config->getFile();
            $this->globals['CFG_VALUE_'.$config->getName()] =  $config->getValue();
            $this->globals['CFG_TITLE_'.$config->getName()] = $config->getTitle();
            $this->globals['CFG_SLUG_'.$config->getName()] = $config->getSlug();
        }

        $this->globals['_locales'] = $this->entityManager->getRepository('JrkPortfolioCoreBundle:Locale')->findBy(array('active' => true),array('position' => 'ASC'));

        foreach($this->globals['_locales'] as $locale) {
            if ($locale->getLocale() == $this->locale) {
                $this->globals['_locale'] = $locale;
            }
        }

        // Inject translation map's name in globals
        $this->globals['_transmap'] = $this->translationMap;
    }



    public function processUserIn() {
        $this->user = $this->securityContext->getToken()->getUser();
        $this->globals['base'] = array('user' => $this->user);
    }


    public function processUserOut() {
        $this->globals['base'] = array('user' => null);
    }


    public function getName()
    {
        return 'off.global_configuration';
    }

}