<?php

namespace Jrk\Portfolio\FrontBundle\Controller;

use Jrk\Portfolio\CoreBundle\Controller\BaseController;
use Jrk\Portfolio\CoreBundle\Entity\Contact;
use Jrk\Portfolio\FrontBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ContactController extends BaseController
{
    public function contactAction(Request $request)
    {

        $contact = new Contact();

        $form = $this->createForm('contact',$contact);
        $form->handleRequest($request);

        if ($request->getMethod() == "POST") {


            if ($form->isValid()){
                $em = $this->getEntityManager();
                $em->persist($contact);
                $em->flush();
                $this->addFlash('contact','Votre demande a bien été prise en compte, un mail de confirmation vous a été envoyé.');

                $this->get('off.mailer')->sendContactValidationMail($contact);
                $form = $this->createForm('contact',$contact);
            }
        }


        return $this->render('JrkPortfolioFrontBundle:Contact:contact.html.twig',array(
            'form' => $form->createView()
        ));
    }


}
