<?php

namespace Jrk\Portfolio\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Base Controller
 */
class BaseController extends Controller {

    public function persistAndFlush($object)
    {
        $em = $this->getEntityManager();
        $em->persist($object);
        $em->flush();

        return $object;
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

    public function filefyname($filename) {

        if ($filename == "::1" || $filename == "127.0.0.1")
            return 1;

        $bad = array_merge(
            array_map('chr', range(0,31)),
            array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
        return str_replace($bad, "", $filename);
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

    public function isURL($url){
        return (!empty($url) && preg_match('#^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?#i', $url));
    }

    protected function trans($path, $array = null, $file = 'JrkPortfolioCoreBundle') {
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

    protected function getRepository($class, $path = "JrkPortfolioCoreBundle") {
        return $this->getDoctrine()->getManager()->getRepository($path . ":" . $class);
    }

    protected function addFlash($type, $text, $clear = false) {
        if ($clear) {
            $this->get('session')->getFlashBag()->clear();
        }

        $this->get('session')->getFlashBag()->add($type, $text);
    }



    protected function guessExtension($file) {
        $extension = $file->guessExtension();

        if (!$extension) {
            $extension = 'bin';
        }

        return $extension;
    }

    protected function file_get_contents_utf8($fn) {
        $content = file_get_contents($fn);
        return mb_convert_encoding($content, 'UTF-8',
            mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }

    protected function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    protected function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
    }


    public function getEntitiesIn($identifiers,$repository) {
        $queryBuilder = $repository->createQueryBuilder('e');
        $queryBuilder->where($queryBuilder->expr()->in('e.id',$identifiers));
        return $queryBuilder->getQuery()->getResult();
    }

    public function getEntitiesNotId($identifiers,$repository) {
        if (!is_array($identifiers)) {
            $identifiers = array($identifiers);
        }
        $queryBuilder = $repository->createQueryBuilder('e');
        $queryBuilder->where($queryBuilder->expr()->notIn('e.id',$identifiers));
        return $queryBuilder->getQuery()->getResult();
    }

}
