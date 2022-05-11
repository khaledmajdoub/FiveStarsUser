<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/listp", name="listp")
     */
    public function index()
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $blogs = $this->getDoctrine()->getManager()->getRepository(Blog::class)->findAll();
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('blog/list.html.twig', [
            'b'=>$blogs
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
    }
    /**
     * @Route("/", name="display_blog")
     */
    public function indexs(PaginatorInterface $paginator, Request $request): Response
    {

        $blogs = $this->getDoctrine()->getManager()->getRepository(Blog::class)->findAll();
        $blogs = $paginator->paginate(
            $blogs,
            $request->query->getInt('page', 1),
            3);
        //config pagination
        return $this->render('blog/index.html.twig', [
            'b'=>$blogs
        ]);
    }



    /**
     * @Route("/admin", name="display_admin")
     */
    public function indexAdmin(): Response
    {

        return $this->render('Admin/index.html.twig'
        );
    }


    /**
     * @Route("/addBlog", name="addBlog")
     */
    public function addBlog(Request $request): Response
    {
        $blog = new Blog();

        $form = $this->createForm(BlogType::class,$blog);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);//Add
            $em->flush();

            return $this->redirectToRoute('display_blog');
        }
        return $this->render('blog/createBlog.html.twig',['f'=>$form->createView()]);




    }

    /**
     * @Route("/removeBlog/{id}", name="supp_blog")
     */
    public function suppressionBlog(Blog  $blog): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();

        return $this->redirectToRoute('display_blog');


    }
    /**
     * @Route("/modifBlog/{id}", name="modifBlog")
     */
    public function modifBlog(Request $request,$id): Response
    {
        $blog = $this->getDoctrine()->getManager()->getRepository(Blog::class)->find($id);

        $form = $this->createForm(BlogType::class,$blog);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('display_blog');
        }
        return $this->render('blog/updateBlog.html.twig',['f'=>$form->createView()]);




    }




    /**
     * @Route("/client", name="display_client")
     */
    public function indexClient(): Response
    {

        return $this->render('Client/index.html.twig');
    }



}
