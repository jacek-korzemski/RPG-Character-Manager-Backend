<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class TokenController extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        $this->request = service('request');
        $this->response = service('response');

        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS, PUT');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public function verify()
    {
        $response = service('response');
        $response->setHeader('Access-Control-Allow-Origin', '*');

        // Get the token from the Authorization header
        $authHeader = $this->request->getHeader("Authorization");
        $token = null;
        if ($authHeader) {
            $token = str_replace('Bearer ', '', $authHeader->getValue());
        }

        if (!$token) {
            return $this->respond(['error' => 'Token not provided.'], 401);
        }

        $key = getenv('JWT_SECRET');

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            // If the token is decoded successfully, create a new token
            $iat = time();
            $exp = $iat + 3600; // Token valid for 1 hour

            $payload = array(
                "iss" => "Card Manager still in progresss",
                "aud" => "I dunno",
                "sub" => "Hello card manage for me",
                "iat" => $iat,
                "exp" => $exp,
                "email" => $decoded->email,
                "user_id" => $decoded->user_id,
            );

            $newToken = JWT::encode($payload, $key, 'HS256');

            $response = [
                'message' => 'Token is valid and has been renewed.',
                'token' => $newToken
            ];

            return $this->respond($response, 200);

        } catch (Exception $e) {
            return $this->respond(['error' => 'Invalid token.'], 401);
        }
    }

    public function options()
    {
        return $this->response;
    }
}
