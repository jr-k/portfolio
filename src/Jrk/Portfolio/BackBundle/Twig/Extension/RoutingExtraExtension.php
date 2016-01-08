<?php

namespace Jrk\Portfolio\BackBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Provides integration of the Routing component with Twig.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoutingExtraExtension extends \Twig_Extension {

    private $container;
    private $router;
    private $request;
    private $entityManager;
    private $environment;

    /**
     * Construct RoutingExtraExtension
     *
     * @param ContainerInterface $container     Container
     * @param EntityManager      $entityManager EntityManager
     * @param \Twig_Environment  $environment   Twig
     */
    public function __construct(ContainerInterface $container, EntityManager $entityManager, \Twig_Environment $environment) {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->entityManager = $entityManager;
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions() {
        return array(
            'select_lang' => new \Twig_Function_Method($this, 'getSelectLang', array('is_safe' => array('html'))),
            'current_url' => new \Twig_Function_Method($this, 'getPathCurrent'),
            'current_route' => new \Twig_Function_Method($this, 'getRouteCurrent'),
            'empty_dir' => new \Twig_Function_Method($this, 'isEmptyDir', array("folder")),
            'get_files' => new \Twig_Function_Method($this, 'getFiles', array("folder")),  
            'format_percent' => new \Twig_Function_Method($this, 'formatPercent', array("float", "total")),
            'created_ago' => new \Twig_Function_Method($this, 'createdAgo'),
            'http_host' => new \Twig_Function_Method($this, 'httpHost'),
            'json_decode' => new \Twig_Function_Method($this, 'jsonDecode'),
            'file_exists' => new \Twig_Function_Method($this, 'fileExists'),
            'route_exists' => new \Twig_Function_Method($this, 'routeExists'),
            'count_days_between' => new \Twig_Function_Method($this, 'countDaysBetween'),
            'num_format' => new \Twig_Function_Method($this, 'numberFormat'),
            'match' => new \Twig_Function_Method($this, 'pregMatch'),
            'form_invalid' => new \Twig_Function_Method($this, 'formInvalid'),
            'die' => new \Twig_Function_Method($this, 'dieDump'),
            'yesIcon' => new \Twig_Function_Method($this, 'yesIcon', array('is_safe' => array('html') )),
            'noIcon' => new \Twig_Function_Method($this, 'noIcon', array('is_safe' => array('html') )),
            'guessIcon' => new \Twig_Function_Method($this, 'guessIcon', array('is_safe' => array('html') )),
            'yearAndMonthBetween' => new \Twig_Function_Method($this, 'yearAndMonthBetween'),
            'explode' => new \Twig_Function_Method($this, 'explodeString'),
        );
    }

    function explodeString($message, $separator = ';') {
        return explode($separator, $message);
    }

    function yearAndMonthBetween($dateFrom, $dateTo, $today = false) {
        if ($today) {
            $dateTo = new \DateTime();
        }


        $delta = $dateTo->getTimestamp() - $dateFrom->getTimestamp();
        if ($delta < 0) {
            return null;
        }

        $monthDelay = (86400 * 30);
        $yearDelay = $monthDelay * 12;

        if ($delta < $monthDelay) {
            return array('month' => '0', 'year' => '0');
        } else if ($delta < $yearDelay) {
            return array('month' => floor($delta / $monthDelay), 'year' => '0');
        } else {
            $years = floor($delta / $yearDelay);
            $months = floor(($delta - ($years * $yearDelay)) / $monthDelay);
            return array('month' => $months, 'year' => $years);
        }
    }

    function yesIcon() {
        $img = 'yes';
        $sourceImg = $this->container->get('templating.helper.assets')->getUrl('images/inside-home/small-icons/png/'.$img.'.png');
        return sprintf('<img src="%s" alt="Oui" title="Oui" />',$sourceImg);
    }

    function noIcon() {
        $img = 'no';
        $sourceImg = $this->container->get('templating.helper.assets')->getUrl('images/inside-home/small-icons/png/'.$img.'.png');
        return sprintf('<img src="%s" alt="Non" title="Non" />',$sourceImg);
    }

    function guessIcon($param) {
        if ($param) {
            return $this->yesIcon();
        }

        return $this->noIcon();
    }

    function dieDump($str) {
        var_dump($str);
        die();
    }

    function formInvalid($field) {
        if (!isset($field->vars))
            return true;
        return (count($field->vars['errors']) > 0);
    }


    function pregMatch($regex,$str){
        return preg_match("#".$regex."#",$str);
    }

    function numberFormat($price,$precision = 2,$car_cent = ",", $car_triplet = " "){
        return number_format($price, $precision, $car_cent, $car_triplet);
    }

    function countDaysBetween($debut, $fin) {

        $tdeb = explode("-", $debut);
        $tfin = explode("-", $fin);

        $diff = mktime(0, 0, 0, $tfin[1], $tfin[2], $tfin[0]) -
            mktime(0, 0, 0, $tdeb[1], $tdeb[2], $tdeb[0]);

        return(($diff / 86400));

    }

    function routeExists($str){

        foreach ($this->router->getRouteCollection()->all() as $name => $route) {
            if ($this->endswith($name,$str))
                return true;
        }
        return false;
    }

    protected function endswith($hay, $needle) {
        return substr($hay, -strlen($needle."")) === $needle."";
    }


    function fileExists($file){
        return file_exists($file);
    }


    function jsonDecode($json){
        $f = json_decode($json);

        return $f;
    }



    public function httpHost() {
        return "http://".$_SERVER['HTTP_HOST'];
    }


    public function formatPercent($float, $total) {

        if ($total == 0)
            return "0";

        $float = ($float / $total) * 100;

        return str_replace('.00', '', round($float, 2));
    }

    /**
     * Get current path
     *
     * @param array $parameters
     *
     * @return string
     */
    public function getPathCurrent($parameters = array()) {
        $this->request = $this->container->get('request');
        // Where is the new route ?
        $name = $this->request->attributes->get('_route');

        if ($name) {
            $baseParameters = $this->request->attributes->all();

            foreach ($baseParameters as $key => $value) {
                if (substr($key, 0, 1) == '_' && $key != '_locale') {
                    unset($baseParameters[$key]);
                }
            }
            $parameters = array_merge($this->request->query->all(), $baseParameters, $parameters);

            return $this->router->generate($name, $parameters, true);
        }

        return '';
    }

    /**
     * Get current path
     *
     * @param array $parameters
     *
     * @return string
     */
    public function getRouteCurrent($parameters = array()) {
        $this->request = $this->container->get('request');
        // Where is the new route ?
        $name = $this->request->attributes->get('_route');

        return $name;
    }


    /**
     * Get select lang
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getSelectLang($parameters = array()) {
        return $this->generate($parameters, false);
    }


    public function getFiles($folder) {

        if (!is_dir($folder)) {
            return array();
        }

        $recursif = false;
        $files = array();
        $dir = opendir($folder);

        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..')
                continue;
            if (is_dir($folder . '/' . $file)) {
                $files[] = $folder . '/' . $file;
                if ($recursif == true)
                    $files = array_merge($files, getFiles($folder . '/' . $file));
            } else {
                $files[] = $file;
            }
        }
        closedir($dir);
        sort($files);
        return $files;
    }

    public function isEmptyDir($folder) {

        if (!is_dir($folder)) {
            return array();
        }
        
        $recursif = false;
        $files = array();

        $dir = opendir($folder);

        while ($file = readdir($dir)) {
            if ($file == '.' || $file == '..')
                continue;
            if (is_dir($folder . '/' . $file)) {
                $files[] = $folder . '/' . $file;
                if ($recursif == true)
                    $files = array_merge($files, isEmptyDir($folder . '/' . $file));
            } else {
                $files[] = $folder . '/' . $file;
            }
        }
        closedir($dir);

        if (count($files) <= 0)
            return true;

        return false;
    }

    
    public function createdAgo(\DateTime $dateTime,$short = false) {
        $delta = time() - $dateTime->getTimestamp();
        if ($delta < 0) {
            throw new \Exception("createdAgo is unable to handle dates in the future");
            $duration = array();
        }

        $length_key = $short ? 'no_ago' : 'ago';

        if ($delta < 60) {
            // Seconds
            $time = $delta;
            $duration['value'] = $time; 
            $duration['key'] = "date_".$length_key."_second" . (($time > 1) ? "s" : "") ;
        } else if ($delta <= 3600) {
            // Mins
            $time = floor($delta / 60);
            $duration['value'] = $time; 
            $duration['key'] = "date_".$length_key."_minute" . (($time > 1) ? "s" : "") ;
        } else if ($delta <= 86400) {
            // Hours
            $time = floor($delta / 3600);
            $duration['value'] = $time; 
            $duration['key'] = "date_".$length_key."_hour" . (($time > 1) ? "s" : "") ;
        } else {
            // Days
            $time = floor($delta / 86400);
            $duration['value'] = $time; 
            $duration['key'] = "date_".$length_key."_day" . (($time > 1) ? "s" : "") ;
        }

        return $duration;
    }

    /**
     * Generate select lang
     *
     * @param array   $parameters parameters
     * @param boolean $absolute   absolute
     *
     * @return string
     */
    private function generate($parameters, $absolute = false) {
        $this->request = $this->container->get('request');
        $name = $this->request->attributes->get('_route');
        return $name;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'routing_extra';
    }

}
