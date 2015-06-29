<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\RegistrationType;
use AppBundle\Form\Model\Registration;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
	/**
     * @Route("/register", name="account_register")
     */
    public function registerAction()
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('account_create'),
        ));

        return $this->render(
            'AppBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/create", name="account_create")
     */
    public function createAction(Request $request)
	{
	    $em = $this->getDoctrine()->getManager();

	    $form = $this->createForm(new RegistrationType(), new Registration());

	    $form->handleRequest($request);

	    if ($form->isValid()) {
	        $registration = $form->getData();

	        $user = $registration->getUser();
	        $encoder = $this->container->get('security.password_encoder');
			$encoded = $encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($encoded);

	        $em->persist($user);
	        $em->flush();

	        return $this->redirectToRoute('homepage');
	    }

	    return $this->render(
	        'AppBundle:Account:register.html.twig',
	        array('form' => $form->createView())
	    );
	}

	/**
     * @Route("/account/loginone", name="account_loginone")
     */
	public function logintwoAction()
	{
		$password = 'admin1';
		$message = '';

		$manager = $this->getDoctrine()->getManager();
        $customerRepository = $manager->getRepository('AppBundle:User');
		$user = $customerRepository->find(4);

		if (!$user) return new Response('User not found');
		$encoder = $this->get('security.encoder_factory')->getEncoder($user);

		if (!($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt()))) return new Response('Password wrong!');

		return new Response('We are OK!');
	}

	/**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request)
    {
    	$authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return $this->render(
	        'AppBundle:Account:login.html.twig',
	        array(
	            // last username entered by the user
	            'last_username' => $lastUsername,
	            'error'         => $error,
	        )
	    );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }
}
