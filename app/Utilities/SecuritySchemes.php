<?php

namespace App\Utilities;

class SecuritySchemes
{
    /**
     * @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="Bearer Token Authentication",
     *     securityScheme="bearer_token"
     * )
     */
    public static function bearerToken()
    {
        // Empty method, used only for annotation
    }
}
