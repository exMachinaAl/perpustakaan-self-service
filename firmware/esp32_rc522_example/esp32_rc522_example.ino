/*
 * Contoh OPSIONAL: ESP32 DevKit + RC522 (SPI 13.56 MHz UID identification).
 * TIDAK anti-kloning. Hanya kirim event; tidak melakukan transaksi peminjaman.
 * Dependency Arduino Library Manager: MFRC522 (Miguel Balboa RFID).
 * API ini mengharuskan HTTPS + CA certificate yang benar.
 * Pin ESP32: RC522 SDA(SS)=GPIO5, SCK=18, MOSI=23, MISO=19, RST=22, 3.3V, GND.
 */
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <mbedtls/md.h>
#include <mbedtls/sha256.h>
#include <esp_system.h>
#include <time.h>

#define RFID_SS 5
#define RFID_RST 22

// Isi seluruh konfigurasi di bawah secara lokal. JANGAN commit credential.
static const char *WIFI_SSID = "GANTI_SSID";
static const char *WIFI_PASSWORD = "GANTI_PASSWORD";
static const char *API_URL = "https://perpus.example.sch.id/api/v1/reader/scan";
static const char *READER_ID = "ESP32-01";
static const char *READER_SECRET = "GANTI_SECRET_DARI_PHP_ARTISAN";
static const char ROOT_CA[] = R"PEM(-----BEGIN CERTIFICATE-----
GANTI_PEM_CA_YANG_VALID
-----END CERTIFICATE-----
)PEM";

MFRC522 rfid(RFID_SS, RFID_RST);
unsigned long lastScanAt = 0;

String toHex(const unsigned char *buffer, size_t length) {
  const char *chars = "0123456789abcdef";
  String output;
  output.reserve(length * 2);
  for (size_t i = 0; i < length; i++) {
    output += chars[(buffer[i] >> 4) & 0x0F];
    output += chars[buffer[i] & 0x0F];
  }
  return output;
}

String sha256Hex(const String &message) {
  unsigned char digest[32];
  if (mbedtls_sha256_ret((const unsigned char*)message.c_str(), message.length(), digest, 0) != 0) return "";
  return toHex(digest, sizeof(digest));
}

String hmacHex(const String &message) {
  unsigned char digest[32];
  mbedtls_md_context_t ctx;
  mbedtls_md_init(&ctx);
  const mbedtls_md_info_t *info = mbedtls_md_info_from_type(MBEDTLS_MD_SHA256);
  if (mbedtls_md_setup(&ctx, info, 1) != 0) return "";
  int rc = mbedtls_md_hmac_starts(&ctx, (const unsigned char *)READER_SECRET, strlen(READER_SECRET));
  if (rc == 0) rc = mbedtls_md_hmac_update(&ctx, (const unsigned char *)message.c_str(), message.length());
  if (rc == 0) rc = mbedtls_md_hmac_finish(&ctx, digest);
  mbedtls_md_free(&ctx);
  return rc == 0 ? toHex(digest, 32) : "";
}

String makeNonce() {
  unsigned char bytes[16];
  for (int i = 0; i < 16; i += 4) {
    uint32_t randomValue = esp_random();
    memcpy(bytes + i, &randomValue, 4);
  }
  return toHex(bytes, 16);
}

bool timeReady() {
  time_t t = time(nullptr);
  return t > 1700000000; // NTP harus berhasil; jangan tandatangani epoch 1970.
}

void sendScan(const String &uid) {
  if (!WiFi.isConnected() || !timeReady()) {
    Serial.println("Wi-Fi/NTP belum siap. Scan tidak dikirim.");
    return;
  }
  String body = String("{\"uid\":\"") + uid + "\"}";
  String timestamp = String((unsigned long)time(nullptr));
  String nonce = makeNonce();
  String canonical = String("POST\n/api/v1/reader/scan\n") + timestamp + "\n" + nonce + "\n" + sha256Hex(body);
  String signature = hmacHex(canonical);
  if (signature.length() != 64) {
    Serial.println("Gagal menandatangani request.");
    return;
  }

  WiFiClientSecure client;
  client.setCACert(ROOT_CA); // DILARANG memakai setInsecure().
  HTTPClient http;
  if (!http.begin(client, API_URL)) {
    Serial.println("HTTPS begin gagal.");
    return;
  }
  http.setTimeout(8000);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-Reader-ID", READER_ID);
  http.addHeader("X-Reader-Time", timestamp);
  http.addHeader("X-Reader-Nonce", nonce);
  http.addHeader("X-Reader-Signature", signature);
  int status = http.POST(body);
  Serial.printf("API status: %d (202=event diterima)\n", status);
  http.end();
}

void setup() {
  Serial.begin(115200);
  delay(300);
  SPI.begin(18, 19, 23, RFID_SS);
  rfid.PCD_Init();
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  for (int i = 0; i < 40 && WiFi.status() != WL_CONNECTED; ++i) delay(500);
  configTime(0, 0, "pool.ntp.org", "time.google.com");
  Serial.println("Reader mulai. Mode UID = tidak anti-kloning.");
}

void loop() {
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    delay(60);
    return;
  }
  if (millis() - lastScanAt >= 1500) {
    String uid = toHex(rfid.uid.uidByte, rfid.uid.size);
    sendScan(uid);
    lastScanAt = millis();
  }
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  delay(200);
}
