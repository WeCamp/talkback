<?php

namespace Wecamp\TalkBack\Controller;

use Silex\Application;
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
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param ConstraintViolationListInterface $lastErrors
     *
     * @return JsonResponse
     */
    protected function getInvalidDataResponse(ConstraintViolationListInterface $lastErrors)
    {
        $errors = [];
        foreach ($lastErrors as $validationError) {
            $field            = $validationError->getPropertyPath();
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
            ], 400
        );
    }
}