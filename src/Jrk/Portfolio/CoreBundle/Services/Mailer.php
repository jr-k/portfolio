<?php

namespace Jrk\Portfolio\CoreBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Jrk\Portfolio\CoreBundle\Enum\EnumTicketType;

class Mailer {


    protected $entityManager;
    protected $request;
    protected $locale;
    protected $translationMap;
    protected $emailFrom;
    protected $mailer;
    protected $translator;
    protected $templating;
    protected $router;

    public function __construct($translationMap,$emailFrom,$entityManager,$requestStack,$mailer,$translator,$templating,$router)
    {
        $this->entityManager = $entityManager;
        $this->translationMap = $translationMap["front_map"];
        $this->request = $requestStack->getCurrentRequest();
        $this->locale = $this->request->getLocale();
        $this->mailer = $mailer;
        $this->router = $router;
        $this->translator = $translator;
        $this->templating = $templating;
        $this->emailFrom = $emailFrom;
    }

    public function send($to,$subject,$message,$parameters = array(), $render = true)
    {
        if ($this->emailFrom) {
            $body = $render ? $this->templating->render($message, $parameters) : $message;
            $message = \Swift_Message::newInstance()
                ->setSubject($subject)
                ->setFrom($this->emailFrom)
                ->setTo($to)
                ->setContentType("text/html")
                ->setBody($body);
        } else {
            throw new \InvalidArgumentException("Please set an 'email_from' value in config.yml");
        }

        $this->mailer->send($message);
    }

    public function sendContactValidationMail($contact)
    {
        $to = $contact->getEmail();
        $subject = 'Portfolio - Contact';
        $message = 'JrkPortfolioFrontBundle:Contact:validation-mail.html.twig';
        $parameters = array('contact' => $contact, 'subject' => $subject);
        $this->send($to,$subject,$message,$parameters);
    }



    public function getName()
    {
        return 'off.mailer';
    }



}
