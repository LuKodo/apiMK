<?php
namespace API\Controller;

use API\Config\FileManager;

class FileController
{
    private $fileManager;

    public function __construct()
    {
        $this->fileManager = new FileManager('uploads');
    }

    public function upload($name, $dir)
    {
        if (empty($_FILES)) {
            http_response_code(400);
            return json_encode(['error' => 'No se han enviado archivos.']);
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
            http_response_code(400);
            return json_encode(['error' => 'No se ha subido ningún archivo en el campo "file".']);
        }

        try {
            $fileName = $this->fileManager->upload($_FILES['file'], $name, $dir);
            return json_encode(['success' => true, 'fileName' => $fileName]);
        } catch (\Exception $e) {
            http_response_code(400);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public function download($fileName)
    {
        try {
            $this->fileManager->download($fileName);
        } catch (\Exception $e) {
            http_response_code(404);
            return json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getUrl($fileName, $dir)
    {
        try {
            return $this->fileManager->findFileAndGenerateUrl($fileName, $dir);
        } catch (\Throwable $th) {
            http_response_code(404);
            return json_encode(['error' => $th->getMessage()]);
        }
    }
}