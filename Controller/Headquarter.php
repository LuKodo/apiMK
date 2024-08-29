<?php
namespace API\Controller;

use API\Config\Database;

class Headquarter
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getOne()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $prefijo = $data['prefijo'];

        $response = $this->db->select('sede', '*', ['prefijo' => $prefijo]);
        return json_encode($response);
    }

    public function getAll()
    {
        $whereIn = [
            'prefijo' => [
                'operator'=> 'IN',
                'values'=> ['LA', 'SB', 'SBE', 'SSC', 'SURT']
            ]
        ];
        
        $res = $this->db->select('sede', '*', $whereIn);
        if ($res) {
            return json_encode($res);
        } else {
            return json_encode([
                'error' => 'Not Found'
            ]);
        }
    }
}
