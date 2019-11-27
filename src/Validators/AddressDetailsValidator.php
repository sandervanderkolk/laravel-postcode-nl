<?php

namespace Speelpenning\PostcodeNl\Validators;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

class AddressDetailsValidator
{
    /**
     * @var Factory
     */
    protected $validator;

    /**
     * @var array
     */
    protected $rules = [
        'sessionToken' => ['required', 'string', 'min:8', 'max:64'],
        'context' => ['required', 'string'],
    ];

    /**
     * AddressLookupValidator constructor.
     *
     * @param Factory $validator
     */
    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates the address lookup input.
     *
     * @param array $data
     * @throws ValidationException
     */
    public function validate(array $data = []): void
    {
        $validation = $this->validator->make($data, $this->rules);

        if ($validation->fails()) {
            throw new ValidationException($validation, new JsonResponse($validation->errors()));
        }
    }
}
