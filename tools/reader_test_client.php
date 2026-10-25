<?php
/**
 * Simulator perangkat untuk uji di laptop (bukan untuk server publik).
 * Environment: READER_URL, READER_ID, READER_SECRET, READER_UID.
 * HTTPS wajib kecuali localhost dan ALLOW_LOCAL_HTTP=1.
 */
if (PHP_SAPI !== 'cli') { exit(1); }
$url = getenv('READER_URL') ?: '';
$id = getenv('READER_ID') ?: '';
$secret = getenv('READER_SECRET') ?: '';
$uid = getenv('READER_UID') ?: '';
$scheme = parse_url($url, PHP_URL_SCHEME);
$host = parse_url($url, PHP_URL_HOST);
$path = parse_url($url, PHP_URL_PATH);
$localAllowed = getenv('ALLOW_LOCAL_HTTP') === '1' && in_array($host, ['localhost','127.0.0.1'], true);
if (!in_array($scheme, ['http','https'], true) || ($scheme !== 'https' && !$localAllowed)
    || $path !== '/api/v1/reader/scan'
    || !preg_match('/\A[A-Za-z0-9._-]{1,64}\z/D', $id)
    || !preg_match('/\A[a-f0-9]{64}\z/D', $secret)
    || !preg_match('/\A[A-Fa-f0-9]{8,32}\z/D', $uid)) {
    fwrite(STDERR, "Konfigurasi tidak valid. Isi environment dan pakai HTTPS (HTTP hanya localhost jika ALLOW_LOCAL_HTTP=1).\n");
    exit(1);
}
$body = json_encode(['uid' => strtoupper($uid)], JSON_UNESCAPED_SLASHES);
$time = (string) time();
$nonce = bin2hex(random_bytes(16));
$canonical = implode("\n", ['POST', $path, $time, $nonce, hash('sha256', $body)]);
$signature = hash_hmac('sha256', $canonical, $secret);
$headers = [
    'Content-Type: application/json', 'Accept: application/json',
    'X-Reader-ID: ' . $id, 'X-Reader-Time: ' . $time,
    'X-Reader-Nonce: ' . $nonce, 'X-Reader-Signature: ' . $signature,
];
$context = stream_context_create(['http' => [
    'method' => 'POST', 'header' => implode("\r\n", $headers),
    'content' => $body, 'timeout' => 10, 'ignore_errors' => true,
]]);
$response = @file_get_contents($url, false, $context);
$status = $http_response_header[0] ?? 'No HTTP response';
echo $status . "\n" . ($response === false ? '(Gagal terkoneksi)' : $response) . "\n";
