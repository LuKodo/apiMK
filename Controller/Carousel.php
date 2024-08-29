<?php
namespace API\Controller;

use API\Config\Database;

class Carousel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $res = $this->db->select('carousel', '*', null);
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
            'carousel',
            '*',
            ['id' => $data['id']]
        );
        $res = null;

        !empty($exists) ?
            $res = $this->db->update('carousel', ['imageName' => $data['imageName'], 'order' => $data['order']], ['id' => $data['id']]) :
            $res = $this->db->insert('carousel', ['imageName' => $data['imageName'], 'order' => $data['order']]);

        if ($res) {
            return json_encode(['success' => true, 'message' => 'Se guardo correctamente']);
        } else {
            return json_encode(["error" => "No se pudo guardar"]);
        }
    }

    public function delete($id)
    {
        $res = $this->db->delete('carousel', ['id' => $id]);

        if ($res) {
            return json_encode(['success' => true, 'message' => 'Se elimino correctamente']);
        } else {
            return json_encode(["error" => "No se pudo eliminar"]);
        }
    }
}
