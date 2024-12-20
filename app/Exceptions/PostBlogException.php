<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\JsonResponseTrait; // Include the trait


class PostBlogException extends Exception
{
    use JsonResponseTrait; // Use the JsonResponseTrait

    protected $validator;

    public function __construct(Validator $validator,$errorMessage)
    {
        parent::__construct($errorMessage);
        $this->validator = $validator;
    }

    public function render()
    {
        return redirect()->back()->with('error', $this->getMessage());

        //return $this->errorResponse($this->message);

    }
}
