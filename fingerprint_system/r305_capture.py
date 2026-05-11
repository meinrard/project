import serial
import time
import sys

# List of possible COM ports to try
possible_ports = ['COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9', 'COM10']

def find_fingerprint():
    for port in possible_ports:
        try:
            ser = serial.Serial(port, 9600, timeout=1)
            print(f"Trying port {port}...", file=sys.stderr)
            time.sleep(2)  # Wait for Arduino to reset
            ser.flushInput()
            start_time = time.time()
            while time.time() - start_time < 10:  # Wait up to 10 seconds
                if ser.in_waiting > 0:
                    line = ser.readline().decode('utf-8').strip()
                    print(f"Received: {line}", file=sys.stderr)
                    if line.startswith("FOUND ID:"):
                        fingerprint_id = line.split(":")[1].strip()
                        ser.close()
                        return fingerprint_id
            ser.close()
        except serial.SerialException:
            continue
    return None

if __name__ == "__main__":
    fingerprint_id = find_fingerprint()
    if fingerprint_id:
        print(fingerprint_id)
        sys.exit(0)
    else:
        print("0")
        sys.exit(1)</content>
<parameter name="filePath">c:\xampp\htdocs\fingerprint_system\r305_capture.py