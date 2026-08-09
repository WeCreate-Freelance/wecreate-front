<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractMetaController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->renderPage(
            'front/index.html.twig',
            'WeCreate - Software, Website & Application Development',
            'WeCreate builds software, websites, and mobile applications for companies and individuals, from SaaS platforms to custom web apps. You dream it, we build it.',
            '/'
        );
    }

    #[Route('/about-us', name: 'about-us')]
    public function aboutUs(): Response
    {
        return $this->renderPage(
            'front/about-us.html.twig',
            'About Us - WeCreate',
            'Meet WeCreate: a team of developers and designers building software, websites, and mobile applications that move businesses forward.',
            '/about-us'
        );
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->renderPage(
            'front/contact.html.twig',
            'Contact Us - WeCreate',
            "Get in touch with WeCreate to discuss your software, website, or mobile app project. Tell us what you need and we'll get back to you.",
            '/contact'
        );
    }

    #[Route('/privacy', name: 'privacy')]
    public function privacy(): Response
    {
        return $this->renderPage(
            'front/privacy.html.twig',
            'Privacy Policy - WeCreate',
            'How WeCreate collects, uses, and protects your personal data across our websites and applications, including cookies and advertising.',
            '/privacy'
        );
    }

    #[Route('/terms', name: 'terms')]
    public function terms(): Response
    {
        return $this->renderPage(
            'front/terms.html.twig',
            'Terms & Conditions - WeCreate',
            "The terms and conditions that govern your use of WeCreate's websites, products, and services.",
            '/terms'
        );
    }
}
