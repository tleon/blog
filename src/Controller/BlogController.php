<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{   
    /**
     * @route("/blog/show/{slug}", name="show_slug" , requirements={"slug"="[a-z0-9\-]+"}, defaults={"slug"="Article sans titre"})
    */
    public function show($slug)
    {
      {
        $slug = str_replace('-', ' ', $slug);
        $slug = ucwords($slug);
        return $this->render('blog/show.html.twig', ["title" => $slug]);
      }
    }
}