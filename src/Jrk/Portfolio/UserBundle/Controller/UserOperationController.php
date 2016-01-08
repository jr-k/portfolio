<?php

namespace Jrk\Portfolio\UserBundle\Controller;


use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Jrk\Portfolio\CoreBundle\Controller\BaseController;
use Jrk\Portfolio\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserOperationController extends BaseController
{
    public function authAction()
    {
        return $this->forward('FOSUserBundle:Security:login');
    }

    public function forgottenPasswordAction(Request $request)
    {
        if (is_object($this->getSecuredUser())){
            return $this->redirect($this->generateUrl('jrk_portfolio_PROFILELOGGED'));
        }

        $session = $this->get('session');
        $email = $request->request->get('email');
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($email);
        $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');

        if (null === $user) {
            $data['forgottenInvalidEmail'] = $email;
            return $this->render('JrkPortfolioFrontBundle:Home:homepage.html.twig',$data);
        }

        if ($user->isPasswordRequestNonExpired($ttl)) {
            $data['forgottenWaitingEmail'] = array('email' => $email,'ttl' => $ttl/60);
            return $this->render('JrkPortfolioFrontBundle:Home:homepage.html.twig',$data);
        }

        if (null === $user->getConfirmationToken()) {
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('off.mailer')->resettingPassword($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);


        $data['forgottenValidEmail'] = $email;
        return $this->render('JrkPortfolioFrontBundle:Home:homepage.html.twig',$data);
    }


    public function forgottenPasswordConfirmationAction(Request $request,$token)
    {
        if (is_object($this->getSecuredUser())){
            return $this->redirect($this->generateUrl('jrk_portfolio_PROFILELOGGED'));
        }

        $data['token'] = $token;

        $userManager = $this->container->get('fos_user.user_manager');
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        $form = $this->createForm('resetting_form',$user);
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);
                $data['forgottenValidPassword'] = true;

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $response = $this->render('JrkPortfolioFrontBundle:Home:homepage.html.twig',$data);
                }

                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                return $response;
            }
        }

        $data['forgottenForm'] = $form->createView();
        return $this->render('JrkPortfolioFrontBundle:Home:homepage.html.twig',$data);
    }



    public function validEmailConfirmationAction(Request $request, $token)
    {

        if (is_object($this->getSecuredUser())){
            return $this->redirect($this->generateUrl('jrk_portfolio_PROFILELOGGED'));
        }

        $data['confirmationValidEmail'] = true;

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $dispatcher = $this->container->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $response = $this->render('JrkPortfolioFrontBundle:Home:homepage.html.twig',$data);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }



}
