<?php
namespace API\Controller;

use API\Config\Database;

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $res = $this->db->select('categoria', '*', ['estado' => true]);
        if ($res) {
            return json_encode($res);
        } else {
            return json_encode([]);
        }
    }

    public function findWhere()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $limit = null;
        $offset = null;

        if (isset($data['limit']) && $data['limit']) {
            $limit = $data['limit'];
            $offset = ($data['offset'] - 1) * $limit;
        }

        $res = $this->db->select('categoria', '*', null, $limit, $offset);
        $total = $this->db->count('categoria', null, $limit, $offset);
        $pages = ceil($total / $limit);

        if ($res) {
            return json_encode(['results' => $res, 'total' => $total, 'pages' => $pages]);
        } else {
            return json_encode([]);
        }
    }

    public function upsert()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $exists = $this->db->select(
            'categoria',
            '*',
            ['incremento' => $data['id']]
        );
        $res = null;
        $estado = $data['estado'] ? 1 : 0;

        !empty($exists) ?
            $res = $this->db->update('categoria', ['descripcion' => $data['descripcion'], 'estado' => $estado], ['incremento' => $data['id']]) :
            $res = $this->db->insert('categoria', ['descripcion' => $data['descripcion'], 'estado' => $estado]);

        if ($res) {
            return json_encode(['success' => true, 'message' => 'Se guardo correctamente']);
        } else {
            return json_encode(["error" => "No se pudo guardar"]);
        }
    }

    public function delete()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $res = $this->db->delete('categoria', ['id' => $data['id']]);

        if ($res) {
            return json_encode(['success' => true, 'message' => 'Se elimino correctamente']);
        } else {
            return json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
