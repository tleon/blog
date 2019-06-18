<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\Slugify;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swift_Mailer;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllWithCategories(),
        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     * @param Request $request
     * @param Slugify $slugify
     * @param Swift_Mailer $mailer
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function new(Request $request, Slugify $slugify, Swift_Mailer $mailer, ValidatorInterface $validator): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $article->setSlug($slugify->generate($article->getTitle()));
            $author = $this->getUser();
            $article->setAuthor($author);
            $entityManager->persist($article);
            $entityManager->flush();

            // To use the ArrayLogger
            $logger = new \Swift_Plugins_Loggers_ArrayLogger();
            $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
            
            $content = $this->renderView('article/email/notifications.html.twig', ['article' => $article, 'error' => ""]);
            $message = (new \Swift_Message('Un nouvel article vient d\'être publié !'))
            ->setTo('eithos41@gmail.com')
            ->setBody($content, 'text/html');
            $error_messages = $validator->validate($article);
            try{
                $mailer->send($message);
            }catch(Exception $e){
                return $this->render('article/email/notifications.html.twig', ['article' => $article, 'error' => $e . $logger->dump()]);
            }
            if (count($error_messages) > 0 ){
                return $this->render('article/new.html.twig', ['article' => $article, "error_messages" => $error_messages]);
            }
            $this->addFlash('success', 'The new article has been created');
            return $this->redirectToRoute('article_index'); 
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     * @param Article $article
     * @return Response
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'isFavorite' => $this->getUser()->isFavorite($article),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     * @param  Request $request
     * @param Article $article
     * @return Response
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $slugify = new Slugify;

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug($slugify->generate($article->getTitle()));    
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }
        $this->addFlash('success', 'The article ' . $article->getId() . ' has been updated');

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function delete(Request $request, Article $article): Response
    {
        $this->addFlash('danger', 'The article '. $article->getId() . ' has been deleted');
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }


    /**
     * @Route("/{id}/favorite", name="article_favorite", methods={"GET","POST"})
     * @param Request $request
     * @param Article $article
     * @param ObjectManager $manager
     * @return Response
     */
    public function favorite(Request $request, Article $article, ObjectManager $manager): Response
    {
        if ($this->getUser()->getFavoris()->contains($article)) {
            $this->getUser()->removeFavori($article)   ;
        }
        else {
            $this->getUser()->addFavori($article);
        }

        $manager->flush();

        return $this->json([
            'isFavorite' => $this->getUser()->isFavorite($article)
        ]);
    }

}
