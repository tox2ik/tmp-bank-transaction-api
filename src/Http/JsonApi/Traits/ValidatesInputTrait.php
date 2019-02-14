<?php

namespace App\Http\JsonApi\Traits;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @property ValidatorInterface $validator
 */
trait ValidatesInputTrait
{

    /**
     * http://jsonapi.org/format/#errors
     * @param mixed $validatable
     *
     * return JsonApi\Document\Error[]
     * @return array
     */
    private function validateInput($validatable): array
    {
        /** @var ConstraintVIolation $constraintViolation */
        $violations = $this->validator->validate($validatable);
        $errors = [];
        foreach ($violations as $i => $constraintViolation) {
            $paramss = '';
            $params = $constraintViolation->getParameters();
            if (is_array($params)) { $paramss = implode(', ', array_values($params)); }

            $errors[]= [
                'id' => $constraintViolation->getPropertyPath(),
                'title' => $constraintViolation->getMessage(),
                'code' => 'invalid_input',
                'detail' => "input: $paramss"
            ];
        }
        return $errors;
    }
}
