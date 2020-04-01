<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentFormType;
use App\Service\Cropper16x9;
use App\Service\UploadImage;
use App\Service\ThumbnailResizer;
use App\Repository\TrickRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick", name="trick")
     */
    public function index()
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
        ]);
    }

    /**
     * @Route("/trick/trick/{slug}", name="trick_trick")
     */
    public function trick(TrickRepository $repo, Request $request, $slug)
    {
        $trick = $repo->findOneBySlug($slug);

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été enregistré !'
            );

            return $this->redirectToRoute('trick_trick', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

}
