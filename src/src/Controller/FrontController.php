<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractMetaController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $this->metaTags->setTitle('WeCreate');

        return $this->render('front/index.html.twig');
    }

    #[Route('/about-us', name: 'about-us')]
    public function aboutUs(): Response
    {
        $this->metaTags->setTitle('WeCreate - About Us');

        return $this->render('front/about-us.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        $this->metaTags->setTitle('WeCreate - Contact');

        return $this->render('front/contact.html.twig');
    }
}
