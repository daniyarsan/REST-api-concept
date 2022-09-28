<?php

namespace App\Traits;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ErrorTrait
{
    /**
     * @param array $errors
     * @return array
     */
    public function prepareErrorResponse(ConstraintViolationListInterface $errors, array $errorMapping = []): array
    {
        $result = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $propertyPath = $error->getPropertyPath();
            $errorLevel = count(explode('.', $propertyPath));

            if (str_contains($propertyPath, 'translations')) {
                $property   = null;

                if ($errorLevel === 3) {
                    $exploded = explode('.', $propertyPath);
                    $property = explode('[', $exploded[0])[0] ?? null;
                    unset($exploded[0]);
                    $propertyPath = implode('.', $exploded);
                }

                preg_match_all("/\[([^]]*)]/", $propertyPath, $matches);
                $lang = $matches[1][0] ?? null;
                $fieldName = explode('.', $propertyPath)[1] ?? null;

                if ($lang && $fieldName) {
                    if ($property) {
                        $result[$property][$lang][$fieldName][] = $error->getMessage();
                    } else {
                        $result[$lang][$fieldName][] = $error->getMessage();
                    }
                }
            } else {
                if ($errorLevel === 2) {
                    $exploded = explode('.', $propertyPath);
                    $property = explode('[', $exploded[0])[0] ?? null;
                    preg_match_all("/\[([^]]*)]/", $propertyPath, $matches);
                    $index = $matches[1][0] ?? null;
                    $result[$property][$index][$exploded[1]] = $error->getMessage();
                } else {
                    $result[$errorMapping[$propertyPath] ?? $propertyPath] = $error->getMessage();
                }
            }
        }

        return $result;
    }
}
