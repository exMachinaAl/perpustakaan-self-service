#pragma once
/*
  Copy this file to device_config.h next to 02_RC522_LARAVEL_SECURE.ino.
  Jangan masukkan device_config.h ke Git atau bagikan screenshot secret.
  API_ORIGIN: HTTPS hostname dapat dijangkau oleh ESP32, TANPA trailing slash/path.
  Sertifikat: isi ROOT_CA_PEM dengan CA yang memverifikasi server tersebut.
  Tidak boleh mengaktifkan setInsecure().
*/
static const char WIFI_SSID[] = "ISI_WIFI_2G4";
static const char WIFI_PASSWORD[] = "ISI_PASSWORD_WIFI";
static const char API_ORIGIN[] = "https://perpus.example.sch.id";
static const char READER_ID[] = "ESP32-01";
static const char READER_SECRET[] = "ISI_64_HEX_SECRET_DARI_LARAVEL";
static const char NTP_HOST[] = "pool.ntp.org";
static const char ROOT_CA_PEM[] = R"CERT(-----BEGIN CERTIFICATE-----
ISI_ROOT_CA_PEM_LENGKAP
-----END CERTIFICATE-----
)CERT";
