<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryType;
use App\Entity\Category;

/**
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     * @IsGranted("ROLE_ADMIN")
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
