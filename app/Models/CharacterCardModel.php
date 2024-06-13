<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CharacterCardModel extends Model
{
    protected $table = 'character_cards';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'name', 'description', 'content'];

    protected $useAutoIncrement = true;
    protected $returnType = 'object';

    protected $useSoftDeletes = false;

    protected $validationRules = [
        'name' => 'required|max_length[255]',
        'description' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Name is required.',
            'unique' => 'There is already a card with that name. It should be unique',
            'max_length' => 'Name cannot exceed 255 characters.'
        ],
        'description' => [
            'required' => 'Description is required.',
            'max_length' => 'Description cannot exceed 255 characters.'
        ],
        'content' => [
            'required' => 'It seems, that the rest of the form hasnt been sent.'
        ]
    ];

    protected $skipValidation = false;

    use ResponseTrait;

    public function addCharacterCard(array $data, $token)
    {
        $response = service('response');
        $response->setHeader('Access-Control-Allow-Origin', '*');

        // First - I extract metadata about the card from the form
        $meta = [];
        $meta['user_id'] = $this->getUserIdFromToken($token);
        $meta['name'] = $data['name'];
        $meta['description'] = $data['description'];

        // Then I clear unused data to pass it further
        unset($data['name']);
        unset($data['description']);
        unset($data['token']);

        // The rest of the data in form should be encoded as JSON
        // and stored in db for use in frontend
        $meta['content'] = json_encode($data);

        return $this->insert($meta);
    }

    public function putCharacterCard(array $data, $token)
    {
        // Validation, passed userID has to be the same as in Token
        // Otherwise, it means that somebody modify not owned card.
        if ($data['user_id'] != $this->getUserIdFromToken($token)) {
            throw new Exception('You are not allowed to do that!');
            return false;
        }

        $meta = [];
        $meta['id'] = $data['id'];
        $meta['user_id'] = $data['user_id'];
        $meta['name'] = $data['name'];
        $meta['description'] = $data['description'];
        
        unset($data['name']);
        unset($data['description']);
        unset($data['user_id']);
        unset($data['id']);
        unset($data['token']);

        $meta['content'] = json_encode($data);
        return $this->where('id', $meta['id'])->set($meta)->update();
    }

    public function findCharacterCard(int $id)
    {
        return $this->find($id);
    }

    public function deleteCharacterCard($id)
    {
        return $this->delete($id);
    }

    public function findAllCharacterCardsForUserId($token)
    {
        $user_id = $this->getUserIdFromToken($token);
        return $this->where('user_id', $user_id)->findAll();
    }

    public function findSingleCharacterCardForUserId($id, $token)
    {
        $user_id = $this->getUserIdFromToken($token);
        return $this->where('user_id', $user_id)->where('id', $id)->first();
    }

    protected function getUserIdFromToken($token)
    {
        $key = getenv('JWT_SECRET');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return $decoded->user_id;
    }
}
