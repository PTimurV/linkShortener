<?php
// Файл для хранения данных
$dataFile = 'urls.json';

// Функция для генерации уникального короткого URL
function generateShortCode($length = 5) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Чтение данных из файла
function readData() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode([]));
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true);
}

// Запись данных в файл
function writeData($data) {
    global $dataFile;
    file_put_contents($dataFile, json_encode($data));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalUrl = $_POST['url'];
    $data = readData();

    // Проверка, существует ли уже этот URL
    foreach ($data as $entry) {
        if ($entry['original_url'] === $originalUrl) {
            echo json_encode(['shortUrl' => 'http://localhost:8000/index.php?code=' . $entry['short_code']]);
            exit;
        }
    }

    // Генерация нового короткого URL
    do {
        $shortCode = generateShortCode();
    } while (array_search($shortCode, array_column($data, 'short_code')) !== false);

    // Сохранение нового URL
    $data[] = ['original_url' => $originalUrl, 'short_code' => $shortCode];
    writeData($data);

    echo json_encode(['shortUrl' => 'http://localhost:8000/index.php?code=' . $shortCode]);
}
?>
