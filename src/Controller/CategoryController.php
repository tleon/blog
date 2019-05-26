<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryType;
use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function add(Request $request)
    {
        

        $cat = new Category();
        $form = $this->createForm(CategoryType::class, $cat);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();
            return $this->redirectToRoute('category');            
        }


        return $this->render('category/index.html.twig', [
            'my_form' => $form->createView(),
        ]);
    }
}
