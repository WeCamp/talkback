<?php

namespace Wecamp\TalkBack\Validate;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\RecursiveValidator;

/**
 * Validates Topic data
 *
 * @package Wecamp\TalkBack
 */
class TopicValidator extends AbstractValidator
{
    /**
     * @var RecursiveValidator
     */
    private $validator;


    /**
     * @param RecursiveValidator $validator
     */
    public function __construct(RecursiveValidator $validator)
    {
        $this->validator = $validator;
    }


    /**
     * Checks new topic data is valid
     *
     * Also updates $lastErrors property with any errors (or none)
     *
     * @param array $data
     *
     * @return bool
     */
    public function isNewTopicValid(array $data)
    {
        $constraint = new Assert\Collection(
            [
                'title'            => [new Assert\Length(['max' => 120]), new Assert\NotBlank()],
                'excerpt'          => [new Assert\Length(['max' => 255]), new Assert\NotBlank()],
                'details'          => [new Assert\NotBlank()],
                'owned_by_creator' => [new Assert\Type(['type' => 'bool']), new Assert\NotBlank()],
            ]
        );

        $this->lastErrors = $this->validator->validateValue($data, $constraint);

        return (0 === count($this->lastErrors));
    }
}