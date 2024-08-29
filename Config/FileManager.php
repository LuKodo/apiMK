<?php
namespace API\Config;

class FileManager
{
    private $uploadDir;

    public function __construct($uploadDir = 'uploads')
    {
        $this->uploadDir = rtrim($uploadDir, '/') . '/';

        if (!file_exists($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new \Exception('No se pudo crear el directorio de carga.');
            }
        }
    }

    public function upload($file, $newName, $dir)
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new \Exception('No se ha subido ningún archivo válido.');
        }

        $fileName = $this->generateUniqueFileName($file['name'], $newName);
        $destination = $this->uploadDir . $dir . '/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $fileName;
        } else {
            throw new \Exception('No se pudo mover el archivo subido.');
        }
    }

    public function download($fileName)
    {
        $filePath = $this->uploadDir . $fileName;

        if (!file_exists($filePath)) {
            throw new \Exception('El archivo no existe.');
        }

        $mimeType = mime_content_type($filePath);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    private function generateUniqueFileName($originalName, $newName)
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return $newName . '.' . $extension;
    }

    public function findFileAndGenerateUrl($fileName, $directory)
    {
        $files = glob($this->uploadDir . '/'. $directory . '/' . pathinfo($fileName, PATHINFO_FILENAME) . '.*');

        if (empty($files)) {
            throw new \Exception('No se encontró ningún archivo que coincida con el nombre proporcionado.');
        }

        $file = $files[0];
        $relativePath = str_replace($this->uploadDir, '', $file);
        $baseUrl = rtrim('http://localhost/apiMK/uploads', '/');

        return json_encode([
            "url" => $baseUrl . $relativePath
        ]);

    }

    private function getFileExtension($filePath)
    {
        return strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    }

    private function isImage($filePath)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        return in_array($this->getFileExtension($filePath), $imageExtensions);
    }
}