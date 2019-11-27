<?php

namespace Speelpenning\PostcodeNl\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller;
use Speelpenning\PostcodeNl\Exceptions\AccountSuspended;
use Speelpenning\PostcodeNl\Exceptions\AddressNotFound;
use Speelpenning\PostcodeNl\Exceptions\Unauthorized;
use Speelpenning\PostcodeNl\Services\AddressLookup;
use Speelpenning\PostcodeNl\Services\Autocomplete;
use Speelpenning\PostcodeNl\Services\AddressDetails;

class AddressController extends Controller
{
    /**
     * @var AddressLookup
     */
    protected $lookup;

    /**
     * @var Autocomplete
     */
    protected $autocomplete;

    /**
     * @var AddressDetails
     */
    protected $addressDetails;



    /**
     * AddressController constructor.
     *
     * @param AddressLookup $lookup
     * @param Autocomplete $autocomplete
     */
    public function __construct(AddressLookup $lookup, Autocomplete $autocomplete, AddressDetails $addressDetails)
    {
        $this->lookup = $lookup;
        $this->autocomplete = $autocomplete;
        $this->addressDetails = $addressDetails;
    }

    /**
     * Performs a Dutch address lookup and returns a JSON response.
     *
     * @param string $postcode
     * @param int|string $houseNumber
     * @param null|string $houseNumberAddition
     * @return JsonResponse
     */
    public function get(string $postcode, string $houseNumber, string $houseNumberAddition = null): JsonResponse
    {
        try {
            $address = $this->lookup->lookup(str_replace(' ', '', $postcode), (int)$houseNumber, $houseNumberAddition);
            return response()->json($address);
        } catch (ValidationException $e) {
            abort(400, 'Bad Request');
        } catch (Unauthorized $e) {
            abort(401, 'Unauthorized');
        } catch (AccountSuspended $e) {
            abort(403, 'Account suspended');
        } catch (AddressNotFound $e) {
            abort(404, 'Not Found');
        }
    }

    /**
     * Autocompletes address from international API.
     *
     * @param string $context
     * @param string $term
     * @param null|string $language
     * @return JsonResponse
     */
    public function autocomplete(string $context, string $term, string $language = null): JsonResponse
    {
        try {
            $address = $this->autocomplete->autocomplete($context, $term, $language);
            return response()->json($address);
        } catch (ValidationException $e) {
            abort(400, 'Bad Request');
        } catch (Unauthorized $e) {
            abort(401, 'Unauthorized');
        } catch (AccountSuspended $e) {
            abort(403, 'Account suspended');
        } catch (AddressNotFound $e) {
            abort(404, 'Not Found');
        }
    }

    /**
     * Gives address details based on given context
     *
     * @param string $context
     * @param string $term
     * @param null|string $language
     * @return JsonResponse
     */
    public function getDetails(string $sessionToken, string $context): JsonResponse
    {
        try {
            $address = $this->addressDetails->getDetails($sessionToken, $context);
            return response()->json($address);
        } catch (ValidationException $e) {
            abort(400, 'Bad Request');
        } catch (Unauthorized $e) {
            abort(401, 'Unauthorized');
        } catch (AccountSuspended $e) {
            abort(403, 'Account suspended');
        } catch (AddressNotFound $e) {
            abort(404, 'Not Found');
        }
    }

}
