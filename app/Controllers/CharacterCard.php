<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CharacterCardModel;

class CharacterCard extends BaseController
{
    protected $response;
    protected $request;
    protected $token;

    function __construct() {
        $this->request = service('request');
        $this->response = service('response');

        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        $this->token = $this->request->getHeaderLine('Authorization');
        $this->token = str_replace('Bearer ', '', $this->token);
    }

    public function add()
    {
        $postData = $this->request->getPost();

        $characterCardModel = new CharacterCardModel();

        $insertedId = $characterCardModel->addCharacterCard($postData, $this->token);

        if ($insertedId) {
            $this->response->setBody("Character card added successfully. ID: $insertedId");
            $this->response->setStatusCode(200);
        } else {
            log_message('error', 'dupło tutaj');
            $this->response->setBody("Failed to add character card.");
            $this->response->setStatusCode(507);
        }
    }

    public function options()
    {
        return $this->response;
    }

    public function view($id)
    {
        // TODO
    }

    public function viewAll()
    {
        $characterCardModel = new CharacterCardModel();
        $this->response->setBody(json_encode($characterCardModel->findAllCharacterCardsForUserId($this->token)));
        return $this->response->setStatusCode(200);
    }
}