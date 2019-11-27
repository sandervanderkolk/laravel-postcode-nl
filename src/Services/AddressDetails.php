<?php

namespace Speelpenning\PostcodeNl\Services;

use Illuminate\Validation\ValidationException;
use Speelpenning\PostcodeNl\Address;
use Speelpenning\PostcodeNl\Exceptions\AccountSuspended;
use Speelpenning\PostcodeNl\Exceptions\AddressNotFound;
use Speelpenning\PostcodeNl\Exceptions\Unauthorized;
use Speelpenning\PostcodeNl\Http\PostcodeNlClient;
use Speelpenning\PostcodeNl\Validators\AddressDetailsValidator;
use Speelpenning\PostcodeNl\Validators\AutocompleteValidator;

class AddressDetails
{
    /**
     * @var AddressLookupValidator
     */
    protected $validator;

    /**
     * @var PostcodeNlClient
     */
    protected $client;

    /**
     * AddressLookup constructor.
     *
     * @param AddressDetailsValidator $validator
     * @param PostcodeNlClient $client
     */
    public function __construct(AddressDetailsValidator $validator, PostcodeNlClient $client)
    {
        $this->validator = $validator;
        $this->client = $client;
    }

    /**
     * Get details based on context from autocomplete
     *
     * @param string %sessionToken
     * @param string $context
     * @return Address
     * @throws ValidationException
     * @throws AccountSuspended
     * @throws AddressNotFound
     * @throws Unauthorized
     */
    public function getDetails(string $sessionToken, string $context): Address
    {
        $this->validator->validate(array_filter(compact('sessionToken','context')));

        $uri = $this->getUri($context);
        $response = $this->client->get($uri, $sessionToken);
        $data = json_decode($response->getBody()->getContents(), true);
        return new Address($data);
    }

    /**
     * Returns the URI for the API request.
     *
     * @param string $context
     * @return string
     */
    public function getUri(string $context): string
    {
        return "https://api.postcode.eu/international/v1/address/$context";
    }
}
