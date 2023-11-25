<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CkEditorLengthConstraint extends Constraint
{
    public function __construct(
        public int $min = 10,
        public int $max = 5000,
        public string $maxMessageSingular = 'This value is too long. It should have {{ limit }} character or less.',
        public string $maxMessagePlural = 'This value is too long. It should have {{ limit }} characters or less.',
        public string $minMessageSingular = 'This value is too short. It should have {{ limit }} character or more.',
        public string $minMessagePlural = 'This value is too short. It should have {{ limit }} characters or more.'
    ) {
        parent::__construct();
    }
}
