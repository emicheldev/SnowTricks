<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManager;
use App\Services\PicturesUploader;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TrickController extends AbstractController
{

   /**
	 * @var TrickRepository
	 */
	private $repository;
	/**
	 * @var EntityManagerInterface
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $manager;

	public function __construct(TrickRepository $repository, EntityManagerInterface $manager)
	{
		$this->repository = $repository;
		$this->manager = $manager;
	}
    
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
    public function trick(TrickRepository $repository, Request $request, $slug)
    {
        $trick = $repository->findOneBySlug($slug);

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

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

      /**
     * @Route("/trick/add", name="add_tricks")
     * @param Request $request
     * @param PicturesUploader $uploadImage
     * @param Security $username
     * @param Security $user
     * @return Response
     * @throws Exception
     */
    public function add(Request $request, PicturesUploader $uploadImage, Security $username, Security $user): Response
    {


            $trick = new Trick();

            $form = $this->createForm(TrickType::class, $trick);

            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $date = new \DateTime();

                foreach($trick->getImages() as $image)
            {
                // Assignation du trick à l'image
                $image->setTrick($trick);
                // Enregistrement de l'image sur le disque dur et en BDD
                $image = $uploadImage->saveImage($image);
                // On persiste l'entité Image une fois bien remplie dans la BDD
                $manager->persist($image);
                
             
                // Enregistrement sur le disque de l'image redimensionnée à la taille d'un thumbnail (!!! A partir de l'image 16x9 !!!)
                $thumbnailResizer->resize($image);
            }

            foreach($trick->getVideos() as $video)
            {
                $video->setTrick($trick);
                $manager->persist($video);
            }
            
            
                $trick->setCreatedAt($date);
                $trick->setUpdatedAt($date);
                $trick->setUser($this->getUser());

                $this->manager->persist($trick);
                $this->manager->flush();
                $this->addFlash('success', 'La trick a bien été créée');
                return $this->redirectToRoute('home');

                    return $this->redirectToRoute('app_home');
                }

            return $this->render('trick/add.html.twig', [
                'form' => $form->createView()
            ]);

        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/trick/delete/{slug}", name="trick_delete")
     * @IsGranted("ROLE_USER")
     */
    public function delete(TrickRepository $repo, EntityManagerInterface $manager, $slug)
    {
        $trick = $repo->findOneBySlug($slug);

        $fileSystem = new Filesystem();

        foreach($trick->getImages() as $image)
        {
            $fileSystem->remove($image->getPath() . '/' . $image->getName());
            $fileSystem->remove($image->getPath() . '/cropped/' . $image->getName());
            $fileSystem->remove($image->getPath() . '/thumbnail/' . $image->getName());
        }

        $manager->remove($trick);
        $manager->flush();

        $this->addflash(
            'success',
            "Le trick {$trick->getName()} a été supprimé avec succès !"
        );

        return $this->redirectToRoute('home');
    }




}
