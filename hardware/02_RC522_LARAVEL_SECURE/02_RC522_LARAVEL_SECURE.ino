/*
 * PATCH 002 - Firmware ESP32 DevKit + RC522, komunikasi HMAC-SHA256 over HTTPS.
 * Kirim heartbeat tiap 30 dtk; tap mengirim scan UID. Tidak meminjam buku otomatis.
 * Dependency: ESP32 Arduino Core + MFRC522 library (Miguel Balboa, 1.4.12).
 * Board DevKit classic pins: SS5 SCK18 MISO19 MOSI23 RST22, VCC 3V3 ONLY.
 * UID RC522 dapat diduplikasi. Ini BUKAN autentikasi smartcard AES.
 */
#include <Arduino.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <mbedtls/md.h>
#include <esp_system.h>
#include <time.h>
#include "device_config.h"

constexpr uint8_t PIN_SS = 5;
constexpr uint8_t PIN_RST = 22;
constexpr unsigned long HEARTBEAT_INTERVAL_MS = 30000;
constexpr unsigned long WIFI_RETRY_INTERVAL_MS = 12000;
constexpr unsigned long LOCAL_SCAN_COOLDOWN_MS = 2500;
static const char *PATH_HEARTBEAT = "/api/v1/reader/heartbeat";
static const char *PATH_SCAN = "/api/v1/reader/scan";

MFRC522 reader(PIN_SS, PIN_RST);
unsigned long lastHeartbeatMs = 0;
unsigned long lastWiFiTryMs = 0;
unsigned long lastScanMs = 0;
String lastUid = "";
bool clockConfigured = false;

String bytesToHex(const uint8_t *data, size_t size) {
  const char *alphabet = "0123456789abcdef";
  String result; result.reserve(size * 2);
  for (size_t i=0; i<size; ++i) {
    result += alphabet[(data[i] >> 4) & 15];
    result += alphabet[data[i] & 15];
  }
  return result;
}

String mdSha256(const String &message) {
  const mbedtls_md_info_t *info = mbedtls_md_info_from_type(MBEDTLS_MD_SHA256);
  uint8_t digest[32];
  if (!info || mbedtls_md(info, (const uint8_t *)message.c_str(), message.length(), digest) != 0) return "";
  return bytesToHex(digest, 32);
}

String signHmac(const String &message) {
  const mbedtls_md_info_t *info = mbedtls_md_info_from_type(MBEDTLS_MD_SHA256);
  uint8_t digest[32];
  if (!info || mbedtls_md_hmac(info, (const uint8_t *)READER_SECRET, strlen(READER_SECRET),
                             (const uint8_t *)message.c_str(), message.length(), digest) != 0) return "";
  return bytesToHex(digest, 32);
}

String nonceHex() {
  uint8_t buf[16];
  for (size_t i=0; i<16; i+=4) {
    uint32_t value = esp_random();
    memcpy(buf+i, &value, 4);
  }
  return bytesToHex(buf, sizeof(buf));
}

bool hasValidConfig() {
  if (strlen(WIFI_SSID) < 1 || strstr(WIFI_SSID, "ISI_") ||
      strstr(API_ORIGIN, "example.sch.id") || strstr(API_ORIGIN, "http://") ||
      strlen(READER_ID) < 1 || strlen(READER_SECRET) != 64 ||
      strstr(READER_SECRET, "ISI_") || strstr(ROOT_CA_PEM, "ISI_ROOT_CA")) return false;
  for (const char *p = READER_SECRET; *p; ++p) {
    if (!isxdigit((unsigned char)*p)) return false;
  }
  return true;
}

bool clockReady() {
  return time(nullptr) > 1700000000;
}

bool readerOk() {
  byte version = reader.PCD_ReadRegister(MFRC522::VersionReg);
  return version != 0x00 && version != 0xFF;
}

int signedPost(const char *path, const String &body) {
  if (WiFi.status() != WL_CONNECTED) return -100;
  if (!clockReady()) return -101;
  String timestamp = String((unsigned long)time(nullptr));
  String nonce = nonceHex();
  String bodySha = mdSha256(body);
  if (bodySha.length() != 64) return -102;
  String canonical = String("POST\n") + path + "\n" + timestamp + "\n" + nonce + "\n" + bodySha;
  String signature = signHmac(canonical);
  if (signature.length() != 64) return -103;

  WiFiClientSecure transport;
  transport.setCACert(ROOT_CA_PEM); // Sertifikat diverifikasi; JANGAN pakai setInsecure().
  transport.setTimeout(8000);
  HTTPClient client;
  const String url = String(API_ORIGIN) + path;
  if (!client.begin(transport, url)) return -104;
  client.setTimeout(8000);
  client.setFollowRedirects(HTTPC_DISABLE_FOLLOW_REDIRECTS);
  client.addHeader("Content-Type", "application/json");
  client.addHeader("Accept", "application/json");
  client.addHeader("X-Reader-ID", READER_ID);
  client.addHeader("X-Reader-Time", timestamp);
  client.addHeader("X-Reader-Nonce", nonce);
  client.addHeader("X-Reader-Signature", signature);
  int status = client.POST(body);
  client.end();
  return status;
}

void heartbeat() {
  if (WiFi.status() != WL_CONNECTED || !clockReady()) return;
  const bool ok = readerOk();
  String body = String("{\"firmware\":\"2.0.0\",\"reader_ok\":") +
    (ok ? "true" : "false") + ",\"wifi_rssi\":" + String(WiFi.RSSI()) + "}";
  int code = signedPost(PATH_HEARTBEAT, body);
  Serial.printf("[HEARTBEAT] HTTP %d, RC522=%s, RSSI=%d dBm\n", code, ok ? "OK" : "ERROR", WiFi.RSSI());
  if (code == 401) Serial.println("Periksa waktu NTP, Reader ID/Secret, signature atau nonce.");
  if (code == 403) Serial.println("Periksa HTTPS/proxy: server menolak koneksi tidak aman.");
  if (code < 0) Serial.println("Periksa Wi-Fi, DNS, CA certificate, atau akses server.");
}

void sendUid(const String &uid) {
  // UID dikirim hanya ke endpoint bertanda tangan. Jangan cetak secret/UID ke log produksi.
  int code = signedPost(PATH_SCAN, String("{\"uid\":\"") + uid + "\"}");
  Serial.printf("[TAP] HTTP %d; 202 = scan diterima (mungkin cooldown).\n", code);
  if (code == 401) Serial.println("Autentikasi/nonce/timestamp reader gagal.");
  if (code == 422) Serial.println("UID tidak valid.");
  if (code == 429) Serial.println("Terlalu sering; tunggu beberapa detik.");
  if (code < 0) Serial.println("Gagal HTTPS/TLS atau koneksi.");
}

void setup() {
  Serial.begin(115200); delay(1000);
  Serial.println("\nPATCH 002 | ESP32 + RC522 | secure reader");
  if (!hasValidConfig()) {
    Serial.println("STOP: isi device_config.h dengan URL HTTPS, kredensial dan CA valid.");
    while (true) delay(3000);
  }
  SPI.begin(18, 19, 23, PIN_SS);
  reader.PCD_Init();
  delay(40);
  Serial.printf("RC522=%s\n", readerOk() ? "READY" : "NOT FOUND (check wiring)");
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  lastWiFiTryMs = millis();
  Serial.println("Menghubungkan Wi-Fi 2.4 GHz...");
}

void loop() {
  if (WiFi.status() != WL_CONNECTED) {
    if (millis() - lastWiFiTryMs > WIFI_RETRY_INTERVAL_MS) {
      Serial.println("Wi-Fi belum tersambung; mencoba ulang...");
      WiFi.disconnect(); WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
      lastWiFiTryMs = millis();
      clockConfigured = false;
    }
    delay(100); return;
  }
  if (!clockConfigured) {
    configTime(0, 0, NTP_HOST, "time.google.com"); // butuh akses NTP/host NTP lokal
    clockConfigured = true;
    Serial.print("Wi-Fi: "); Serial.println(WiFi.localIP());
    Serial.println("Sinkronisasi NTP... ");
  }
  if (!clockReady()) { delay(300); return; }
  if (lastHeartbeatMs == 0 || millis() - lastHeartbeatMs >= HEARTBEAT_INTERVAL_MS) {
    heartbeat(); lastHeartbeatMs = millis();
  }
  if (!reader.PICC_IsNewCardPresent() || !reader.PICC_ReadCardSerial()) {
    delay(70); return;
  }
  String uid = bytesToHex(reader.uid.uidByte, reader.uid.size);
  reader.PICC_HaltA();
  reader.PCD_StopCrypto1();
  if (uid != lastUid || millis() - lastScanMs > LOCAL_SCAN_COOLDOWN_MS) {
    sendUid(uid);
    lastUid = uid;
    lastScanMs = millis();
  }
  delay(100);
}
