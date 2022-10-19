<?php


namespace App\Form\DataTransformer;


use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AuthorToSlugTransformer implements DataTransformerInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function transform(mixed $entity)
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getSlug();
    }

    public function reverseTransform(mixed $entitySlug)
    {
        if (!$entitySlug) {
            return null;
        }

        $entity = $this->entityManager
            ->getRepository(Author::class)
            ->findOneBy([
                'slug' => $entitySlug
            ]);


        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(
                sprintf(
                    'An entity with slug "%s" does not exist!',
                    $entitySlug
                )
            );
        }

        return $entity;
    }
}