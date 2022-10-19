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

class PageUpdater
{
    use ErrorTrait;

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $entityManager
    )
    {}

    public function update(Request $request, Page $page)
    {
        $page = $this->serializer
            ->deserialize(
                $request->getContent(),
                Page::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $page]
            );

        $errors = $this->validator->validate($page);

        if (count($errors) > 0) {
            return $this->prepareErrorResponse($errors);
        }

        $data = json_decode($request->getContent(), true);
        $form = $this->formFactory->create(PageType::class, $page);
        $form->submit($data, false);
        $page = $form->getData();

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page;
    }
}