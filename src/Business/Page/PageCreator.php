<?php


namespace App\Business\Page;


use App\Entity\Page;
use App\Service\Util;
use App\Traits\ErrorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PageCreator
{
    use ErrorTrait;

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private Util $util,
    )
    {}

    public function create(Request $request)
    {
        $page = new Page();

        $page = $this->serializer->deserialize(
                $request->getContent(),
                Page::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $page]);

        $errors = $this->validator->validate($page);

        if (count($errors) > 0) {
            return $this->prepareErrorResponse($errors);
        }

        $this->em->persist($page);
        $this->em->flush();

        return $page;
    }
}