<?php

namespace Jrk\Portfolio\BackBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * 
 */
class FiltersFront extends \Twig_Extension
{

    private $container;
    private $router;
    private $request;
    private $entityManager;
    private $environment;
    protected $translationMap;

    public function __construct($translationMap,ContainerInterface $container, EntityManager $entityManager, \Twig_Environment $environment) {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->entityManager = $entityManager;
        $this->environment = $environment;
        $this->translationMap = $translationMap["front_map"];
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'rav' => new \Twig_Filter_Method($this, 'rav', array('is_safe' => array('html') )),
            'wrap' => new \Twig_Filter_Method($this, 'wrap',  array('is_safe' => array('html') )),
            't' => new \Twig_Filter_Method($this, 'translate', array('is_safe' => array('html') )),
            'tj' => new \Twig_Filter_Method($this, 'translateJs',  array('is_safe' => array('html') )),
            'tc' => new \Twig_Filter_Method($this, 'translateChoice', array('is_safe' => array('html') )),
            'tcj' => new \Twig_Filter_Method($this, 'translateChoiceJs', array('is_safe' => array('html') )),
            'escapeQuotes' => new \Twig_Filter_Method($this, 'escapeQuotes', array('is_safe' => array('html') )),
            'isset' => new \Twig_Filter_Method($this, 'issetVar', array( 'needs_environment' => true )),
            'global' => new \Twig_Filter_Method($this, 'getGlobal', array( 'needs_environment' => true )),
            'dateCache' => new \Twig_Filter_Method($this, 'dateCache', array()),
            'basename' => new \Twig_Filter_Method($this, 'getBasename', array()),
        );
    }

    public function dateCache($txt) {
        return $txt.'?'.time();
    }

    public function getBasename($str) {
        return basename($str);
    }

    public function getGlobal(\Twig_Environment $environment, $varName) {
        if ($this->issetVar($environment,$varName)) {
            $globals = $environment->getGlobals();
            return $globals[$varName];
        }

        return null;
    }

    public function issetVar(\Twig_Environment $environment, $varName) {
        $globals = $environment->getGlobals();
        return array_key_exists($varName,$globals);
    }


    public function escapeQuotes($txt)
    {
        return str_replace("'","\\'",$txt);
    }

    public function wrap($txt, $wrapper, $injector = '%')
    {
        $wraps = explode($injector,$wrapper);
        return preg_replace("#(.*)%(.+)%(.*)#isU","$1".$wraps[0]."$2".$wraps[1]."$3",$txt);
    }


    public function translate($txt,$arr = array())
    {
        return $this->container->get('translator')->trans($txt, $arr, $this->translationMap);
    }

    public function translateChoice($txt,$count,$arr = array())
    {
        return $this->container->get('translator')->transChoice($txt, $count, $arr, $this->translationMap);
    }


    public function translateJs($txt,$arr = array())
    {
        return str_replace("'","&quot;",$this->translate($txt,$arr));
    }

    public function translateChoiceJs($txt,$count,$arr = array())
    {
        return str_replace("'","&quot;",$this->translateChoice($txt,$count,$arr));
    }


    /**
     * Created ago
     *
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function rav($txt)
    {
        return strip_tags($txt);
    }

    /**
     * Returns the name of the extension
     *
     * @return string
     */
    public function getName()
    {
        return 'filters_front';
    }
}
