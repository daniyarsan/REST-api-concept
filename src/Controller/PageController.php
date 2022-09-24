<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/pages', name: 'app_pages')]
    public function all(): JsonResponse
    {
        return $this->json([], 200, ["Content-Type" => "application/json"]);
    }
}
