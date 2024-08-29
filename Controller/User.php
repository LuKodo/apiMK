<?php
namespace API\Controller;

use API\Config\Database;
use API\Helper\EncryptHelper;

class User
{
    private $db;
    private $helper;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->helper = new EncryptHelper();
    }

    public function getOne()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $password = $data['password'];
        $response = $this->helper->encryptData($password);
        return json_encode([
            'password' => $response,
        ]);
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $res = $this->db->select('user', '*', ['name' => $data['username']]);
        if ($res) {
            return json_encode($res);
        } else {
            return json_encode([
                'error' => 'Username not found'
            ]);
        }
    }
}
