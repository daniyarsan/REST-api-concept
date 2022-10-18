<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Page;
use App\Helper\ListResponseHelper;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
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
    public function list(
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

    #[Route('/menu', name: 'menu')]
    public function menu(
        Request $request,
        ListResponseHelper $listResponseHelper): Response
    {
        $plainMenu = $this->entityManager->getRepository(Category::class)->getPlainMenu();

        /* HYDRATE menu with statuses */
        foreach ($plainMenu as $key => $rawMenuItem) {
            $statusesInCategory = $this->entityManager->getRepository(Page::class)->getMenuStatusByCategoryId($rawMenuItem['id']);
            $plainMenu[$key]['status'] = end($statusesInCategory);
        }

        /* FORMAT multidimensional menu */
        $result = array_reduce($plainMenu, function($agr, $cur) {
            if ($cur['parentId'] === null) {
                $agr[] = $cur;
            } else {
                $key = array_search($cur['parentId'], array_column($agr, 'id'));
                if ($key !== false) {
                    $agr[$key]['child'][] = $cur;
                }
            }

            return $agr;
        });


        return $this->json(
            $result,
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
