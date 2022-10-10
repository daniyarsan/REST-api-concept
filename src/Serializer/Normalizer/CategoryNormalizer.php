<?php

namespace App\Serializer\Normalizer;

use App\Entity\Category;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CategoryNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private ObjectNormalizer $normalizer)
    {
    }

    public function normalize($topic, string $format = null, array $context = []): array
    {

        $context[AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER] = function ($object, $format, $context) {
            return $object->getId();
        };

        $data = $this->normalizer->normalize($topic, $format, $context);
        if (is_array($data) && isset($context['groups']) && $context['groups'] == 'user') {
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {

        return $data instanceof Category;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
