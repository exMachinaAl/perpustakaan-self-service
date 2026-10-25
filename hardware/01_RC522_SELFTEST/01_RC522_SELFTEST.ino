/*
 * PATCH 002 - Tahap 1: tes SPI + pembacaan kartu tanpa Wi-Fi/API.
 * Board: ESP32 DevKit (classic), module RC522 3.3 V.
 * Arduino Library Manager: MFRC522 (Miguel Balboa), 1.4.12.
 * RC522 SDA->GPIO5, SCK->18, MOSI->23, MISO->19, RST->22, 3.3V->3V3, GND->GND.
 */
#include <Arduino.h>
#include <SPI.h>
#include <MFRC522.h>

static constexpr uint8_t PIN_SS = 5;
static constexpr uint8_t PIN_RST = 22;
MFRC522 reader(PIN_SS, PIN_RST);

void setup() {
  Serial.begin(115200);
  delay(1200);
  Serial.println("\nPATCH 002 - RC522 OFFLINE SELFTEST");
  SPI.begin(18, 19, 23, PIN_SS);
  reader.PCD_Init();
  delay(50);
  byte version = reader.PCD_ReadRegister(MFRC522::VersionReg);
  Serial.printf("RC522 VersionReg = 0x%02X\n", version);
  if (version == 0x00 || version == 0xFF) {
    Serial.println("GAGAL: periksa wiring, 3.3V, GND, atau modul.");
  } else {
    Serial.println("Reader terdeteksi; tap satu kartu 13.56 MHz...");
  }
}

void loop() {
  if (!reader.PICC_IsNewCardPresent() || !reader.PICC_ReadCardSerial()) {
    delay(80); return;
  }
  Serial.print("UID [HANYA TEST LAB / jangan bagikan sebagai password]: ");
  for (byte i=0; i<reader.uid.size; ++i) {
    if (reader.uid.uidByte[i] < 0x10) Serial.print('0');
    Serial.print(reader.uid.uidByte[i], HEX);
  }
  Serial.print(" | ukuran "); Serial.println(reader.uid.size);
  reader.PICC_HaltA();
  reader.PCD_StopCrypto1();
  delay(500);
}
