<?php

namespace Jrk\Portfolio\FrontBundle\Controller;

use Jrk\Portfolio\CoreBundle\Controller\BaseController;
use Jrk\Portfolio\CoreBundle\Entity\Contact;
use Jrk\Portfolio\CoreBundle\Entity\Newsletter;
use Jrk\Portfolio\FrontBundle\Form\NewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends BaseController
{
    public function homepageAction(Request $request)
    {

        $projects = $this->getRepository('Project','JrkPortfolioFrontBundle')->findBy(array(
            'active' => true
        ));

        $contact = new Contact();
        $formContact = $this->createForm('contact', $contact);

        if ($request->getMethod() == 'POST') {
            $formContact->handleRequest($request);

            if ($formContact->isValid()) {
                $this->persistAndFlush($contact);
                $this->addFlash('contact_success', 'Your message has been sent successfully ! We\'ll contact you soon.');
            }
        }


        return $this->render('JrkPortfolioFrontBundle:Home:homepage.html.twig', array(
            'projects' => $projects,
            'formContact' => $formContact->createView(),
            'formContactSubmitted' => $formContact->isSubmitted(),
        ));
    }


}
