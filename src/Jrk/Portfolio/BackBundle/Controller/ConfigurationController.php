<?php

namespace Jrk\Portfolio\BackBundle\Controller;

use Jrk\Portfolio\BackBundle\Controller\BaseController;
use Jrk\Portfolio\BackBundle\Form\ConfigurationAddType;
use Jrk\Portfolio\BackBundle\Form\ConfigurationFilterType;
use Jrk\Portfolio\BackBundle\Form\ConfigurationType;
use Jrk\Portfolio\CoreBundle\Entity\Configuration;
use Jrk\Portfolio\CoreBundle\Enum\EnumConfigurationVartype;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ConfigurationController extends BaseController
{


    public static $route_list = 'jrk_portfolio_back_configuration_list';
    public static $route_edit = 'jrk_portfolio_back_configuration_edit';
    public static $route_add= 'jrk_portfolio_back_configuration_add';
    public static $entity_fullbundle = 'JrkPortfolioCoreBundle';
    public static $entity_bundle = 'CoreBundle';
    public static $entity_name = 'Configuration';

    /**
     * Lists all Configuration entities.
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
            'route_list' => self::$route_list,
            'route_edit' => self::$route_edit,
        ));
    }

    /**
     * Create filter form and process filter request.
     *
     */
    protected function filter($request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm(new ConfigurationFilterType());
        $queryBuilder = $this->getRepository(self::$entity_name,self::$entity_fullbundle)->createQueryBuilder('e');
        $queryBuilder->orderBy('e.position','ASC');

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
                $filterForm = $this->createForm(new ConfigurationFilterType(), $filterData);
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
            return $this->redirect($this->generateUrl(self::$route_list));
        }

        $localeManager = $this->get('off.locale_manager');
        $form = $this->createForm(new ConfigurationType($entity->getVartype(),$entity->getHastitle(),$localeManager),$entity);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // Data management
                $em = $this->getEntityManager();
                $em->persist($entity);
                $em->flush();

                if ($entity->getVartype() == EnumConfigurationVartype::FILEUPLOAD) {
                    // Manage attachment
                    foreach($form['translations'] as $locale => $translation) {
                        $attachment = $translation['attachment']->getData();

                        if (is_object($attachment)) {
                            $dir = $entity->getUploadDirectory(true, $locale);
                            $entity->deleteFiles($dir);
                            $extension = $this->guessExtension($attachment);
                            $attachment->move($dir, rand(1, 99999).'.'.$extension);
                        }
                    }
                }

                // Multi buttons management
                if ($form->get('save_and_stay')->isClicked()) {
                    return $this->redirect($this->generateUrl(self::$route_edit,array('id' => $entity->getId())));
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
                'route_edit' => self::$route_edit,
                'FILEUPLOAD' => EnumConfigurationVartype::FILEUPLOAD
            )
        );
    }

    public function addAction(Request $request, $name)
    {
        $new = false;
        $entity = null;

        if (preg_match('#^([0-9]+)$#',$name)) {
            $entity = $this->getRepository(self::$entity_name,self::$entity_fullbundle)->find($name);
        } else {
            $entity = $this->getRepository(self::$entity_name,self::$entity_fullbundle)->findOneByName($name);
        }

        if (!is_object($entity)) {
            $new = true;
            $entity = new Configuration();
        }

        $form = $this->createForm(new ConfigurationAddType(),$entity);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                // Data management
                $em = $this->getEntityManager();
                $em->persist($entity);
                $em->flush();

                // Multi buttons management
                if ($form->get('save_and_stay')->isClicked()) {
                    return $this->redirect($this->generateUrl(self::$route_add,array('name' => $entity->getName())));
                } else if ($form->get('save_and_add')->isClicked()) {
                    return $this->redirect($this->generateUrl(self::$route_add));
                } else if ($form->get('save_and_edit')->isClicked()) {
                    return $this->redirect($this->generateUrl(self::$route_edit,array('id' => $entity->getId())));
                } else {
                    return $this->redirect($this->generateUrl(self::$route_list));
                }
            }
        }

        return $this->render('JrkPortfolioBackBundle:'.self::$entity_name.':add.html.twig',array(
                "entity" => $entity,
                "form" => $form->createView(),
                "new" => $new,
                'entity_name' => self::$entity_name,
                'route_list' => self::$route_list,
                'route_edit' => self::$route_edit
            )
        );
    }


    public function deleteAction(Request $request, $name)
    {
        $em = $this->getEntityManager();
        $entity = null;

        if (preg_match('#^([0-9]+)$#',$name)) {
            $entity = $this->getRepository(self::$entity_name,self::$entity_fullbundle)->find($name);
        } else {
            $entity = $this->getRepository(self::$entity_name,self::$entity_fullbundle)->findOneByName($name);
        }

        if (is_object($entity)) {
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl(self::$route_list));
    }
}
