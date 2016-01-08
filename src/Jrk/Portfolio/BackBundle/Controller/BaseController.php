<?php

namespace Jrk\Portfolio\BackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base Controller
 */
class BaseController extends Controller {

    public function findExceptionCode($exception, $baseCode) {

        while($exception != null) {
            $code = $exception->getCode();
            if ($code == $baseCode) {
                return true;
            }
            $exception = $exception->getPrevious();
        }

        return false;
    }

    public function getEntitiesByIds($ids,$repository) {
        $queryBuilder = $repository->createQueryBuilder('e');
        $queryBuilder->where($queryBuilder->expr()->in('e.id',$ids));
        return $queryBuilder->getQuery()->getResult();
    }

    public function getEntitiesExceptById($ids,$repository) {
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $queryBuilder = $repository->createQueryBuilder('e');
        $queryBuilder->where($queryBuilder->expr()->notIn('e.id',$ids));
        return $queryBuilder->getQuery()->getResult();
    }

    public function prefixRoute($route) {
        return $this->route_prefix.$route;
    }

    public function strToCamel($message, $separator = '_'){
        $camelVar = explode($separator,$message);
        $camelVarOut = '';

        foreach($camelVar as $camel) {
            $camelVarOut .= ucfirst(strtolower($camel));
        }

        return $camelVarOut;
    }


    public function getParameter($str){
        return $this->container->getParameter($str);
    }

    public function filefyname($filename) {

        if ($filename == "::1" || $filename == "127.0.0.1")
            return 1;

        $bad = array_merge(
            array_map('chr', range(0,31)),
            array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
        return str_replace($bad, "", $filename);
    }

    public function isURL($url){
        return (!empty($url) && preg_match('#^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?#i', $url));
    }


    public function mkpath($path){
        $tree = explode("/",$path);
        $targetFolder = "";
        foreach($tree as $t){
            $targetFolder .= $t;
            if (!is_dir($targetFolder)) {mkdir($targetFolder, 0777);}
            $targetFolder .= "/";
        }
    }

    public function rmfiles($paths){
        foreach($paths as $path){
            if (file_exists($path)) { unlink($path);}
        }
    }

    public static function deleteTree($dir) {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::deleteTree("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
        return null;
    }


    public function findOne($class) {
        $object = $this->getRepository($class)->findAll();
        $object = $object[0];
        return $object;
    }

    protected function isEmail($email) {
        return !empty($email) && preg_match(('/^[a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z\p{L}0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z\p{L}0-9]+[._a-z\p{L}0-9-]*\.[a-z0-9]+$/ui'), $email);
    }

    protected function trans($path, $array = null, $file = 'JrkPortfolioBackBundle') {
        if ($array == null)
            $array = array();
        return $this->get('translator')->trans($path, $array, $file);
    }


    protected function getEntityManager() {
        return $this->getDoctrine()->getManager();
    }

    protected function getSecuredUser() {
        return $this->container->get('security.context')->getToken()->getUser();
    }

    protected function getSecurity() {
        return $this->get('security.context');
    }

    protected function isGranted($state) {
        return $this->get('security.context')->isGranted($state);
    }

    protected function getRepository($class, $path = "JrkPortfolioBackBundle") {
        return $this->getDoctrine()->getManager()->getRepository($path . ":" . $class);
    }

    protected function addFlash($type, $text, $clear = false) {
        if ($clear) {
            $this->get('session')->getFlashBag()->clear();
        }

        $this->get('session')->getFlashBag()->add($type, $text);
    }

    protected function startswith($hay, $needle) {
        return substr($hay, 0, strlen($needle."")) === $needle."";
    }

    protected function endswith($hay, $needle) {
        return substr($hay, -strlen($needle."")) === $needle."";
    }


    public function dateBetween($from, $to, $date = 'now') {
        $date = is_int($date) ? $date : strtotime($date); // convert non timestamps
        $from = is_int($from) ? $from : strtotime($from->format('Y-m-d')); // ..
        $to = is_int($to) ? $to : strtotime($to->format('Y-m-d'));         // ..
        $to += 86400;
        return ($date >= $from) && ($date <= $to); // extra parens for clarity
    }

    protected function guessExtension($file) {
        $extension = $file->guessExtension();

        if (!$extension) {
            $extension = 'bin';
        }

        return $extension;
    }


}
