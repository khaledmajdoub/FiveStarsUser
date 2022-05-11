<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Stripe;


class DefaultController extends AbstractController
{
    /**
     * @Route("/index", name="default")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [

        ]);
    }


    /**
     * @Route("/success", name="success")
     */
    public function success()
    {
        return $this->render('default/success.html.twig', [

        ]);
    }
    /**
     * @Route("/error", name="error")
     */
    public function error()
    {
        return $this->render('default/error.html.twig', [

        ]);
    }
    /**
     * @Route("/create-checkout-session" ,name="checkout")
     */
    public function checkout()
    {
        \Stripe\Stripe::setApiKey('sk_test_51IlFkSJj0y1Rumrbtsgp7uJpb9HmUTP8gIWqbpYEsaHVrdXBZRb8jtSPyqjeEgOvrziaGzZgoWDYTRWejRvX4d0V00bhYiMbvM');
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'T-shirt',
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success',[],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('error',[],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new JsonResponse([ 'id' => $session->id ]) ;
    }



}
