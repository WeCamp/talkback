<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 27/08/15
 * Time: 12:42
 */

namespace Wecamp\TalkBack\Validate;


use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractValidator
{

    /**
     * @var ConstraintViolationListInterface
     */
    protected $lastErrors;


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return ConstraintViolationListInterface|ConstraintViolationInterface[]
     */
    public function getLastErrors()
    {
        return $this->lastErrors;
    }
}