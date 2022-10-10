<?php

namespace App\Controller;

use App\Entity\Category;
use App\Helper\ListResponseHelper;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: "/category/api", name: "categories_api_")]
class CategoryController extends AbstractController
{

    public function __construct(
        private CategoryRepository $categoryRepo,
        private SerializerInterface $serializer,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/list', name: 'list')]
    public function all(
        Request $request,
        ListResponseHelper $listResponseHelper): Response
    {

        $categories = $this->entityManager->getRepository(Category::class)->findCategories();

        return $this->json(
            $categories,
            Response::HTTP_OK,
            [],
            ['groups' => 'user']
        );
    }

    #[Route('/get/{id}', name: 'get', methods: ["GET"])]
    public function get($id): Response
    {
        $categories = $this->entityManager->getRepository(Category::class)->findSubcategoriesByCategory($id);

        return $this->json(
            $categories,
            Response::HTTP_OK,
            [],
            ['groups' => 'user']
        );
    }
}
