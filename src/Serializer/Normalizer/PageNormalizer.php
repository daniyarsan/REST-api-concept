<?php

namespace App\Serializer\Normalizer;

use App\Entity\Page;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PageNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
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
            if ($topic->getCategory()) {
                $data['category'] = $topic->getCategory()->getId();
            }
            if ($topic->getAuthor()) {
                $data['author'] = $topic->getAuthor()->getSlug();
            }
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {

        return $data instanceof Page;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
