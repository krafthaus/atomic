<?php

namespace KraftHaus\Atomic\Exceptions;

/*
 * This file is part of the Atomic package.
 *
 * (c) KraftHaus <hello@krafthaus.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Contracts\Validation\Validator;

class PropValidationException extends \Exception
{

    /**
     * The validator instance.
     *
     * @var Validator
     */
    public $validator;

    /**
     * @param  Validator  $validator
     */
    public function __construct(Validator $validator)
    {
        parent::__construct(implode(', ', $validator->getMessageBag()->all()));

        $this->validator = $validator;
    }
}