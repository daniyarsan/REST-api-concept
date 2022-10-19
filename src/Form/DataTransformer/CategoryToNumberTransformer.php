<?php


namespace App\Form\DataTransformer;


use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CategoryToNumberTransformer implements DataTransformerInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function transform(mixed $entity)
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getId();
    }

    public function reverseTransform(mixed $entityId)
    {
        // no issue number? It's optional, so that's ok
        if (!$entityId) {
            return null;
        }

        $entity = $this->entityManager
            ->getRepository(Category::class)
            ->find($entityId);

        if (null === $entity) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(
                sprintf(
                    'An issue with number "%s" does not exist!',
                    $entityId
                )
            );
        }

        return $entity;
    }
}