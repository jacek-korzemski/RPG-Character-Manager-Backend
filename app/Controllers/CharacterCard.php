<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CharacterCardModel;

class CharacterCard extends BaseController
{
    public function add()
    {
        $request = service('request');
        $response = service('response');
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        $token = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $token);

        $postData = $request->getPost();

        $characterCardModel = new CharacterCardModel();

        $insertedId = $characterCardModel->addCharacterCard($postData, $token);

        if ($insertedId) {
            $response->setBody("Character card added successfully. ID: $insertedId");
            $response->setStatusCode(200); // OK status code
        } else {
            log_message('error', 'dupło tutaj');
            $response->setBody("Failed to add character card.");
            $response->setStatusCode(500); // Internal Server Error status code
        }
    }

    public function options()
    {
        $response = service('response');
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS, PREFLIGHT');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $response;
    }

    public function view($id)
    {
        // TODO
    }

    public function viewAll()
    {
        // TODO
    }
}