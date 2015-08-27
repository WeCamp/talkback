<?php

namespace Wecamp\TalkBack\Validate;

use Symfony\Component\Validator\Constraints as Assert;

/*  */
class TopicValidate
{

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateTopic($data)
    {
        $constraint = new Assert\Collection(array(
            'title' => array(new Assert\Length(array('max' => 120)), new Assert\NotBlank()),
            'excerpt' => array(new Assert\Length(array('max' => 255)), new Assert\NotBlank()),
            'details' => array(new Assert\NotBlank()),
            'owned_by_creator' => array(new Assert\NotBlank()),
        ));

        $errors = $this->app['validator']->validateValue($data, $constraint);
        if (count($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
    }

}