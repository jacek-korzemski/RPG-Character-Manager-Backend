<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CharacterCardModel;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CharacterCard extends BaseController
{
    protected $response;
    protected $request;
    protected $token;

    function __construct() {
        $this->request = service('request');
        $this->response = service('response');

        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS, PUT');
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

    public function put()
    {
        $postData = $this->request->getPost();

        $characterCardModel = new CharacterCardModel();

        try 
        {
            $characterCardModel->putCharacterCard($postData, $this->token);
        }
        catch (Exception)
        {
            $this->response->setBody('Failed to update character card');
            return $this->response->setStatusCode(507);
        }
        $this->response->setBody('Card updated');
        return $this->response->setStatusCode(200);
    }

    public function delete()
    {
        $postData = $this->request->getJSON(true);
    
        if (!isset($postData['id'])) {
            return $this->response->setStatusCode(400)->setBody('Card ID is required.');
        }
    
        $id = $postData['id'];
    
        $characterCardModel = new CharacterCardModel();
    
        $card = $characterCardModel->findCharacterCard($id);
    
        if (!$card) {
            return $this->response->setStatusCode(404)->setBody('Card not found.');
        }
    
        $userIdFromToken = $this->getUserIdFromToken($this->token);
    
        if ($card->user_id != $userIdFromToken) {
            return $this->response->setStatusCode(403)->setBody('Unauthorized action.');
        }
    
        if ($characterCardModel->deleteCharacterCard($id)) {
            return $this->response->setStatusCode(200)->setBody('Card deleted successfully.');
        } else {
            return $this->response->setStatusCode(500)->setBody('Failed to delete card.');
        }
    }

    public function options()
    {
        return $this->response;
    }

    public function view()
    {
        $requestData = $this->request->getJSON();
        $id = $requestData->id;
        $characterCardModel = new CharacterCardModel();
        $this->response->setBody(json_encode($characterCardModel->findSingleCharacterCardForUserId($id, $this->token)));
        return $this->response->setStatusCode(200);
    }

    public function viewAll()
    {
        $characterCardModel = new CharacterCardModel();
        $this->response->setBody(json_encode($characterCardModel->findAllCharacterCardsForUserId($this->token)));
        return $this->response->setStatusCode(200);
    }

    // TODO: Remove the duplicate from CharacterCardModel
    protected function getUserIdFromToken($token)
    {
        $key = getenv('JWT_SECRET');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return $decoded->user_id;
    }
}