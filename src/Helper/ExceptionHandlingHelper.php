<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

trait ExceptionHandlingHelper
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    )
    {
        $this->translator = $translator;
    }

    public function exceptionHandle(\Exception $exception): JsonResponse
    {
        if(!empty($exception->getPrevious())){
            switch ($exception->getPrevious()->getCode()){
                case 23503: $body = ['message' => $this->translator->trans('Cannot be deleted Object status assigned to object')]; break;
                default: $body = ['message' => $exception->getMessage()];
            }
        }else{
            $body = ['message' => $exception->getMessage()];
        }
        return new JsonResponse($body, Response::HTTP_BAD_REQUEST);
    }
}