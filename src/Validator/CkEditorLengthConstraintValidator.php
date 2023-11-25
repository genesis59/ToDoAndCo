<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CkEditorLengthConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($constraint instanceof CkEditorLengthConstraint) {
            $cleanValue = strip_tags($value);
            $length = mb_strlen($cleanValue);
            if ($length < $constraint->min) {
                $this->context->buildViolation(
                    $constraint->min === 1 ? $constraint->minMessageSingular : $constraint->minMessagePlural
                )
                    ->setParameter('{{ value }}', $value)
                    ->setParameter('{{ limit }}', (string) $constraint->min)
                    ->addViolation();
            }
            if ($length > $constraint->max) {
                $this->context->buildViolation(
                    $constraint->min === 1 ? $constraint->maxMessageSingular : $constraint->maxMessagePlural
                )
                    ->setParameter('{{ value }}', $value)
                    ->setParameter('{{ limit }}', (string) $constraint->max)
                    ->addViolation();
            }
        }
    }
}
