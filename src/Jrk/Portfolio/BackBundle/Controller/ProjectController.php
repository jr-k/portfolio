<?php

namespace Jrk\Portfolio\BackBundle\Controller;

use Jrk\Portfolio\BackBundle\Controller\BaseController;
use Jrk\Portfolio\BackBundle\Form\ProjectFilterType;
use Jrk\Portfolio\BackBundle\Form\ProjectType;
use Jrk\Portfolio\FrontBundle\Entity\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ProjectController extends BaseController
{

    public static $route_list = 'jrk_portfolio_back_project_list';
    public static $route_edit = 'jrk_portfolio_back_project_edit';
    public static $route_delete = 'jrk_portfolio_back_project_delete';
    public static $entity_fullbundle = 'JrkPortfolioFrontBundle';
    public static $entity_bundle = 'FrontBundle';
    public static $entity_name = 'Project';

    /**
     * Lists all Project entities.
     */
    public function listAction(Request $request, $page, $limit)
    {
        list($filterForm, $queryBuilder) = $this->filter($request);
        list($entities, $counter, $pager, $pagerHtml) = $this->get('jrk_easypagination')->paginate(
            $queryBuilder,
            array('args' => array('limit' => $limit), 'route' => self::$route_list),
            $limit,
            false,
            $page,
            true
        );

        return $this->render('JrkPortfolioBackBundle:'.self::$entity_name.':list.html.twig',array(
            'counter' => $counter,
            'entities' => $entities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'entity_name' => self::$entity_name,
            'entity_fullbundle' => self::$entity_fullbundle,
            'route_list' => self::$route_list,
            'route_edit' => self::$route_edit,
            'route_delete' => self::$route_delete
        ));
    }

    /**
     * Create filter form and process filter request.
     *
     */
    protected function filter($request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm(new ProjectFilterType());
        $queryBuilder = $this->getRepository(self::$entity_name,self::$entity_fullbundle)->createQueryBuilder('e');
        $queryBuilder->orderBy('e.title','ASC');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove(self::$entity_name.'ControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set(self::$entity_name.'ControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has(self::$entity_name.'ControllerFilter')) {
                $filterData = $session->get(self::$entity_name.'ControllerFilter');
                $filterForm = $this->createForm(new ProjectFilterType(), $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }



    public function editAction(Request $request, $id)
    {
        $new = false;
        $entity = $this->getRepository(self::$entity_name,self::$entity_fullbundle)->find($id);

        if (!is_object($entity)) {
            $new = true;
            $entity = new Project();
        }

        $form = $this->createForm(new ProjectType(),$entity);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                // Data management
                $em = $this->getEntityManager();
                $em->persist($entity);
                $em->flush();

                // Picture management
                $attachment = $form['attachment']->getData();

                if (is_object($attachment)) {
                    $attachment->move($entity->getUploadDirectoryPath(true), 'picture.jpg');
                    $this->get('liip_imagine.cache.manager')->remove($entity->getPicture());
                }

                // Picture management
                $attachment = $form['attachmentThumb']->getData();

                if (is_object($attachment)) {
                    $attachment->move($entity->getUploadDirectoryPath(true), 'picture-thumb.jpg');
                    $this->get('liip_imagine.cache.manager')->remove($entity->getPicture(true,'-thumb'));
                }

                // Multi buttons management
                if ($form->get('save_and_stay')->isClicked()) {
                    return $this->redirect($this->generateUrl(self::$route_edit,array('id' => $entity->getId())));
                } else  if ($form->get('save_and_add')->isClicked()) {
                    return $this->redirect($this->generateUrl(self::$route_edit,array()));
                } else {
                    return $this->redirect($this->generateUrl(self::$route_list));
                }
            }
        }

        return $this->render('JrkPortfolioBackBundle:'.self::$entity_name.':edit.html.twig',array(
                "entity" => $entity,
                "form" => $form->createView(),
                "new" => $new,
                'entity_name' => self::$entity_name,
                'route_list' => self::$route_list,
                'route_edit' => self::$route_edit
            )
        );
    }


    public function deleteAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getEntityManager();

            if ($id == -1) {
                $ids = $request->request->get('entity_triggers');
                $entities = $this->getEntitiesByIds($ids,$this->getRepository(self::$entity_name, self::$entity_fullbundle));
                foreach ($entities as $entity) {
                    $em->remove($entity);
                }
            } else {
                $entity = $this->getRepository(self::$entity_name, self::$entity_fullbundle)->find($id);
                if (is_object($entity)) {
                    $em->remove($entity);
                }
            }

            $em->flush();
            return new Response('ok',Response::HTTP_OK);
        }

        return new Response('error',Response::HTTP_BAD_REQUEST);
    }
}
