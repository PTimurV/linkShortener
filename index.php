<?php
// Файл для хранения данных
$dataFile = 'urls.json';

// Чтение данных из файла
function readData() {
    global $dataFile;
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode([]));
    }
    $json = file_get_contents($dataFile);
    return json_decode($json, true);
}

// Если параметр code присутствует в запросе, перенаправляем на оригинальный URL
if (isset($_GET['code'])) {
    $shortCode = $_GET['code'];
    $data = readData();

    // Поиск оригинального URL по короткому коду
    foreach ($data as $entry) {
        if ($entry['short_code'] === $shortCode) {
            header("Location: " . $entry['original_url']);
            exit;
        }
    }

    echo "URL not found";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener</title>
    <script>
        function shortenUrl() {
            const originalUrl = document.getElementById('originalUrl').value;
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'shortener.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    document.getElementById('shortenedUrl').innerText = response.shortUrl;
                }
            };
            xhr.send('url=' + encodeURIComponent(originalUrl));
        }
    </script>
</head>
<body>
    <h1>URL Shortener</h1>
    <input type="text" id="originalUrl" placeholder="Enter your URL here">
    <button onclick="shortenUrl()">Shorten</button>
    <p>Shortened URL: <span id="shortenedUrl"></span></p>
</body>
</html>
