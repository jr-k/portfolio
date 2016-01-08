<?php

namespace Jrk\Portfolio\BackBundle\Controller;

use Jrk\Portfolio\BackBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ToolsController extends BaseController
{
    public function switchAction(Request $request, $id,$fullbundle,$entity,$attribute)
    {

        if ($request->isXmlHttpRequest()) {
            $entity = $this->getRepository($entity,$fullbundle)->find($id);

            if (is_object($entity)) {
                $em = $this->getEntityManager();
                $booleanValue = !$entity->{'get'.ucfirst($attribute)}();
                $entity->{'set'.ucfirst($attribute)}($booleanValue);
                $em->persist($entity);
                $em->flush();
                return new Response($this->renderView('JrkPortfolioBackBundle:Tools:switch.html.twig',array('result' => $booleanValue)),Response::HTTP_OK);
            }
        }

        return new Response('error',Response::HTTP_BAD_REQUEST);
    }

    public function switchUniqueAction(Request $request, $id,$fullbundle,$entity,$attribute)
    {

        if ($request->isXmlHttpRequest()) {
            $baseEntity = $this->getRepository($entity,$fullbundle)->find($id);

            if (is_object($baseEntity)) {
                $entities = $this->getRepository($entity,$fullbundle)->findAll();
                $em = $this->getEntityManager();
                foreach($entities as $entity) {
                    if ($entity->getId() == $baseEntity->getId()) {
                        $entity->{'set'.ucfirst($attribute)}(true);
                        $em->persist($entity);
                    } else {
                        $entity->{'set'.ucfirst($attribute)}(false);
                        $em->persist($entity);
                    }
                }
                $em->flush();
                return new Response(json_encode(array(
                    'true' => $this->renderView('JrkPortfolioBackBundle:Tools:switch.html.twig',array('result' => true)),
                    'false' => $this->renderView('JrkPortfolioBackBundle:Tools:switch.html.twig',array('result' => false))
                )),Response::HTTP_OK);
            }
        }

        return new Response('error',Response::HTTP_BAD_REQUEST);
    }



    public function editInplaceAction(Request $request, $_locale, $entityName, $entityFullBundle)
    {
        $user = $this->getSecuredUser();
        $id = $request->request->get('pk');
        $value = $request->request->get('value');
        $field = explode(';',$request->request->get('name'));
        $name = $field[0];
        $type = $field[1];

        $entity = $this->getRepository($entityName,$entityFullBundle)->find($id);

        if (is_object($entity)) {
            $methodName = 'set'.ucfirst($name);
            $em = $this->getEntityManager();

            if ($type == 'translation') {
                $entity->getCurrentTranslation()->{$methodName}($value);
            } else if (method_exists($entity,$methodName)) {
                $entity->{$methodName}($value);
            }

            $em->persist($entity);
            $em->flush();
            return new Response('ok');

        }

        return new Response('error',Response::HTTP_BAD_REQUEST);
    }
    
}
