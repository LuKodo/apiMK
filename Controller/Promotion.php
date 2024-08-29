<?php
namespace API\Controller;

use API\Config\Database;

class Promotion
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $limit = null;
        $offset = null;

        if (isset($data['limit']) && $data['limit']) {
            $limit = $data['limit'];
            $offset = $data['offset'] - 1;
        }

        $res = $this->db->select('promocion', '*', null, $limit, $offset);
        $total = $this->db->count('promocion', null, $limit, $offset);
        $pages = ceil($total / $limit);

        if ($res) {
            return json_encode(['results' => $res, 'total' => $total, 'pages' => $pages]);
        } else {
            return json_encode([]);
        }

        if ($res) {
            return json_encode($res);
        } else {
            return json_encode([]);
        }
    }

    public function upsert()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $exists = $this->db->select(
            'promocion',
            '*',
            ['id' => $data['id']]
        );
        $res = null;

        !empty($exists) ?
            $res = $this->db->update('promocion', ['rowIndex' => $data['rowIndex'], 'columnIndex' => $data['columnIndex'], 'imageName' => $data['imageName']], ['id' => $data['id']]) :
            $res = $this->db->insert('promocion', ['rowIndex' => $data['rowIndex'], 'columnIndex' => $data['columnIndex'], 'imageName' => $data['imageName']]);

        if ($res) {
            return json_encode(['success' => true, 'message' => 'Se guardo correctamente']);
        } else {
            return json_encode(["error" => "No se pudo guardar"]);
        }
    }

    public function delete($id)
    {
        $res = $this->db->delete('promocion', ['id' => $id]);
        if ($res) {
            return json_encode(['success' => true, 'message' => 'Se elimino correctamente']);
        } else {
            return json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
