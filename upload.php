<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file0']) && $_FILES['file0']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file0']['tmp_name'];
        $fileName = $_FILES['file0']['name'];
        $fileSize = $_FILES['file0']['size'];
        $fileType = $_FILES['file0']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $fileData = curl_file_create($fileTmpPath, $fileType, $fileName);
            $postData = array('file' => $fileData);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://telegra.ph/upload');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if (isset($result[0]['src'])) {
                $imageUrl = 'https://telegra.ph' . $result[0]['src'];
                echo json_encode(array("src" => $imageUrl));
            } else {
                echo json_encode(array("error" => "Error al subir la imagen a Telegra.ph."));
            }
        } else {
            echo json_encode(array("error" => "Tipo de archivo no permitido."));
        }
    } else {
        echo json_encode(array("error" => "Error al subir la imagen."));
    }
    exit();
}
?>
