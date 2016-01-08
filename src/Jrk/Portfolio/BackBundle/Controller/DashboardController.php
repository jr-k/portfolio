<?php

namespace Jrk\Portfolio\BackBundle\Controller;

use Jrk\Portfolio\BackBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends BaseController
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('jrk_portfolio_back_blog_post_list'));

    }

}
