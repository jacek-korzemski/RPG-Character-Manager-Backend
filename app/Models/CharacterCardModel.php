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
        unset($data['token']);
        unset($data['description']);

        // The rest of the data in form should be encoded as JSON
        // and stored in db for use in frontend
        $meta['content'] = json_encode($data);

        return $this->insert($meta);
    }

    public function findCharacterCard(int $id)
    {
        return $this->find($id);
    }

    public function findAllCharacterCardsForUserId(int $id)
    {
        return $this->where('user_id', $id)->findAll();
    }

    // Some helpers - TODO: Move it outside:
    protected function getUserIdFromToken($token)
    {
        $key = getenv('JWT_SECRET');
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return $decoded->user_id;
    }
}
