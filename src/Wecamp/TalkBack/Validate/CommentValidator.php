<?php
/**
 * Created by PhpStorm.
 * User: dennis
 * Date: 8/27/15
 * Time: 3:42 PM
 */

namespace Wecamp\TalkBack\Validate;


use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Constraints as Assert;

class CommentValidator extends AbstractValidator
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
     * @param array $data
     * @return bool
     */
    public function isNewCommentValid(array $data) {
        $constraint = new Assert\Collection(
            [
                'topic'            => [new Assert\Type(['type' => 'int']), new Assert\NotBlank()],
                'content'          => [new Assert\NotBlank()],
            ]
        );

        $this->lastErrors = $this->validator->validateValue($data, $constraint);

        return (0 === count($this->lastErrors));
    }

}