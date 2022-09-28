<?php

namespace App\Helper;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use LogicException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ListResponseHelper
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PAGE_SIZE = 10;
    private const MAX_PAGE_SIZE = 250;

    /** @var PaginatorInterface */
    private $paginator;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param PaginatorInterface $paginator
     * @param SerializerInterface $serializer
     */
    public function __construct(PaginatorInterface $paginator, SerializerInterface $serializer)
    {
        $this->paginator  = $paginator;
        $this->serializer = $serializer;
    }

    /**
     * @param Request $request
     * @param QueryBuilder $query
     * @param string $groups
     * @return JsonResponse
     */
    public function getResponse(Request $request, QueryBuilder $query, string $groups = 'user'): JsonResponse
    {
        $page     = $request->query->getInt('page', self::DEFAULT_PAGE);
        $pageSize = $request->query->getInt('pageSize', self::DEFAULT_PAGE_SIZE);

        try {
            $result = $this->paginator->paginate(
                $query,
                $page,
                $pageSize <= self::MAX_PAGE_SIZE ? $pageSize : self::MAX_PAGE_SIZE,
                ['wrap-queries' => true]
            );
        } catch (LogicException $exception) {
            return $this->json(['message' => $exception->getMessage(), 'code' => Response::HTTP_BAD_REQUEST]);
        }

        return $this->json(
            [
                'result'     => $result,
                'pagination' => [
                    'pageSize' => $pageSize,
                    'page'     => $page,
                    'total'    => $result->getTotalItemCount(),
                ],
            ],
            Response::HTTP_OK,
            [],
            [
                'groups' => $groups,
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            ]
        );
    }

    /**
     * @param $data
     * @param int $status
     * @param array $headers
     * @param array $context
     * @return JsonResponse
     */
    private function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $json = $this->serializer->serialize($data, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], $context));

        return new JsonResponse($json, $status, $headers, true);
    }
}