<?php

namespace Speelpenning\PostcodeNl\Services;

use Illuminate\Validation\ValidationException;
use Speelpenning\PostcodeNl\Address;
use Speelpenning\PostcodeNl\Exceptions\AccountSuspended;
use Speelpenning\PostcodeNl\Exceptions\AddressNotFound;
use Speelpenning\PostcodeNl\Exceptions\Unauthorized;
use Speelpenning\PostcodeNl\Http\PostcodeNlClient;
use Speelpenning\PostcodeNl\Validators\AutocompleteValidator;

class Autocomplete
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
     * @param AddressLookupValidator $validator
     * @param PostcodeNlClient $client
     */
    public function __construct(AutocompleteValidator $validator, PostcodeNlClient $client)
    {
        $this->validator = $validator;
        $this->client = $client;
    }

    /**
     * Performs an address lookup.
     *
     * @param string %sessionToken
     * @param string $context
     * @param string $term
     * @param null|string $language
     * @return Address
     * @throws ValidationException
     * @throws AccountSuspended
     * @throws AddressNotFound
     * @throws Unauthorized
     */
    public function autocomplete(string $sessionToken, string $context, string $term, string $language = null): Address
    {
        $this->validator->validate(array_filter(compact('sessionToken','context', 'term', 'language')));

        $uri = $this->getUri($context, $term, $language);
        $response = $this->client->get($uri, $sessionToken);
        $data = json_decode($response->getBody()->getContents(), true);
        return new Address($data);
    }

    /**
     * Returns the URI for the API request.
     *
     * @param string $context
     * @param string $term
     * @param null|string $language
     * @return string
     */
    public function getUri(string $context, string $term, string $language = null): string
    {
        return "https://api.postcode.eu/international/v1/autocomplete/$context/$term";
    }
}
