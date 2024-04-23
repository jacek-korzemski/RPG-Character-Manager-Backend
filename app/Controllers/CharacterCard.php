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

        $postData = $request->getPost();

        $token = $request->getVar('token');

        $characterCardModel = new CharacterCardModel();

        $insertedId = $characterCardModel->addCharacterCard($postData, $token);

        if ($insertedId) {
            echo "Character card added successfully. ID: $insertedId";
        } else {
            echo "Failed to add character card.";
        }
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