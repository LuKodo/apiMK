<?php
namespace API\Controller;

use API\Config\Database;

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['query'])) {
            $whereIn = [
                'prefijo' => $data['sede'],
                'nuevo' => '> 0',
                'estado' => 'true',
                'OR' => [
                    [
                        'field' => 'codigo',
                        'value' => '%' . $data['query'] . '%',
                    ],
                    [
                        'field' => 'nombre',
                        'value' => '%' . $data['query'] . '%',
                    ]
                ]
            ];
        } else {
            $whereIn = [
                'prefijo' => $data['sede'],
                'nuevo' => '> 0',
                'estado' => 'true',
            ];
        }

        if (isset($data['categoria']) && $data['categoria'] != 'all') {
            $where = [
                'categoria' => $data['categoria'],
            ];

            $whereIn = array_merge($whereIn, $where);
        }


        $limit = null;
        $offset = null;

        if (isset($data['limit']) && $data['limit']) {
            $limit = $data['limit'];
            $offset = $data['offset'] - 1;
        }

        $res = $this->db->select('productofinal', 'codigo, categoria, nombre, marca, nuevo, usado, prefijo, precioventageneral', $whereIn, $limit, $offset);
        if ($res) {
            return json_encode($res);
        } else {
            return json_encode([]);
        }
    }
}
