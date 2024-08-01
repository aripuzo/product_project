<?php

namespace App\Requests;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ProductRequest extends BaseRequest
{
    #[Type('string')]
    #[NotBlank()]
    protected string $name;

    #[Type('float')]
    #[NotBlank([])]
    protected float $price;
}
