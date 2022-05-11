<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function index(Request $request , \Swift_Mailer $mailer)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        $contact = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

            $user->setRoles(['ROLE_USER']);
            $message= (new \Swift_Message('FootPlus INFO'))
                ->setTo($user->getEmail())
                ->setFrom('khaled.majdoub@esprit.tn')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',compact('user')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}