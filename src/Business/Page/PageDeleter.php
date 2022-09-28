<?php


namespace App\Business\Page;


use App\Entity\Page;
use App\Form\PageType;
use App\Traits\ErrorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PageDeleter
{
    use ErrorTrait;

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $entityManager
    )
    {}

    public function delete(Page $page)
    {
        $this->entityManager->remove($page);
        $this->entityManager->flush();


        return $page;
    }
}