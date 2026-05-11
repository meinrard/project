#include <Adafruit_Fingerprint.h>
#include <SoftwareSerial.h>

SoftwareSerial mySerial(2, 3);

Adafruit_Fingerprint finger = Adafruit_Fingerprint(&mySerial);

int id;

void setup()
{
  Serial.begin(9600);
  finger.begin(57600);

  if (finger.verifyPassword()) {
    Serial.println("Fingerprint sensor detected!");
  } else {
    Serial.println("Sensor not found");
    while (1);
  }
}

void loop()
{
  Serial.println("Enter ID number to save fingerprint:");
  
  while (!Serial.available());

  id = Serial.parseInt();

  if (id > 0) {
    enrollFingerprint(id);
  }
}

void enrollFingerprint(int id)
{
  int p = -1;

  Serial.print("Enrolling ID #");
  Serial.println(id);

  Serial.println("Place finger...");
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
  }

  p = finger.image2Tz(1);
  if (p != FINGERPRINT_OK) {
    Serial.println("Error");
    return;
  }

  Serial.println("Remove finger");
  delay(2000);

  p = 0;
  while (p != FINGERPRINT_NOFINGER) {
    p = finger.getImage();
  }

  Serial.println("Place same finger again");

  p = -1;
  while (p != FINGERPRINT_OK) {
    p = finger.getImage();
  }

  p = finger.image2Tz(2);
  if (p != FINGERPRINT_OK) {
    Serial.println("Error");
    return;
  }

  p = finger.createModel();
  if (p != FINGERPRINT_OK) {
    Serial.println("Fingerprints did not match");
    return;
  }

  p = finger.storeModel(id);

  if (p == FINGERPRINT_OK) {
    Serial.println("Fingerprint saved successfully!");
  } else {
    Serial.println("Failed to save fingerprint");
  }
}
