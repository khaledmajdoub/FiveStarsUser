<?php

namespace App\Controller;

use App\Form\ContactType;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(\Symfony\Component\HttpFoundation\Request $request , \Swift_Mailer $mailer): Response
    {   $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){

        $contact = $form->getData();

        $message= (new \Swift_Message('Five Stars Tours'))
         ->setTo('khaled.majdoub@esprit.tn')
            ->setFrom('khaled.majdoub@esprit.tn')
            ->setBody(
                $this->renderView(
                    'emails/contact.html.twig',compact('contact')
                ),
                'text/html'
            )
        ;
        $mailer->send($message);
        $this->addFlash('message','le message est bien envoyé');
        return $this->redirectToRoute('contact');
    }
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
