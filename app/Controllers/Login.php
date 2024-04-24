<?php
 
namespace App\Controllers;
 
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
 
class Login extends BaseController
{
    use ResponseTrait;
     
    public function index()
    {        
        $response = service('response');
        $response->setHeader('Access-Control-Allow-Origin', '*');

        $userModel = new UserModel();
  
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
          
        $user = $userModel->where('email', $email)->first();
  
        if(is_null($user)) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }
  
        $pwd_verify = password_verify($password, $user['password']);
  
        if(!$pwd_verify) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }
 
        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
        $exp = $iat + 3600000;
 
        $payload = array(
            "iss" => "Card Manager still in progresss",
            "aud" => "I dunno",
            "sub" => "Hello card manage for me",
            "iat" => $iat,
            "exp" => $exp,
            "email" => $user['email'],
            "user_id" => $user['id'],
        );
         
        try {
            $token = JWT::encode($payload, $key, 'HS256');
        } catch (Exception $e) {
            return $this->respond(['error' => $e->getMessage()], 500);
        }
 
        $response = [
            'message' => 'Login Successful',
            'token' => $token
        ];
         
        return $this->respond($response, 200);
    }
 
}
