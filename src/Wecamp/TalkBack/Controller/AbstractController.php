<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 27/08/15
 * Time: 12:58
 */

namespace Wecamp\TalkBack\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class AbstractController
 *
 * @package Wecamp\TalkBack
 */
abstract class AbstractController
{

    /**
     * @param ConstraintViolationListInterface $lastErrors
     *
     * @return JsonResponse
     */
    protected function getInvalidDataResponse(ConstraintViolationListInterface $lastErrors)
    {
        $errors = [];
        foreach ($lastErrors as $validationError) {
            $field = $validationError->getPropertyPath();
            $errors[$field][] = $validationError->getMessage();
        }

        return new JsonResponse(
            [
                'errors' => [
                    [
                        'message' => 'Data is invalid',
                    ],
                ],
                'validation_errors' => $errors,
            ], 503
        );
    }
}