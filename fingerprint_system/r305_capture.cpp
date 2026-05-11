#include <windows.h>
#include <iostream>
#include <string>
#include <vector>
#include <ctime>

std::vector<std::string> possible_ports = {"COM4", "COM3", "COM5", "COM6", "COM7", "COM8", "COM9", "COM10"};

HANDLE openSerialPort(const std::string& portName) {
    HANDLE hSerial = CreateFile(portName.c_str(), GENERIC_READ | GENERIC_WRITE, 0, 0, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, 0);
    if (hSerial == INVALID_HANDLE_VALUE) {
        return INVALID_HANDLE_VALUE;
    }

    DCB dcbSerialParams = {0};
    dcbSerialParams.DCBlength = sizeof(dcbSerialParams);
    if (!GetCommState(hSerial, &dcbSerialParams)) {
        CloseHandle(hSerial);
        return INVALID_HANDLE_VALUE;
    }
    dcbSerialParams.BaudRate = CBR_9600;
    dcbSerialParams.ByteSize = 8;
    dcbSerialParams.StopBits = ONESTOPBIT;
    dcbSerialParams.Parity = NOPARITY;
    if (!SetCommState(hSerial, &dcbSerialParams)) {
        CloseHandle(hSerial);
        return INVALID_HANDLE_VALUE;
    }

    COMMTIMEOUTS timeouts = {0};
    timeouts.ReadIntervalTimeout = 50;
    timeouts.ReadTotalTimeoutConstant = 50;
    timeouts.ReadTotalTimeoutMultiplier = 10;
    timeouts.WriteTotalTimeoutConstant = 50;
    timeouts.WriteTotalTimeoutMultiplier = 10;
    if (!SetCommTimeouts(hSerial, &timeouts)) {
        CloseHandle(hSerial);
        return INVALID_HANDLE_VALUE;
    }

    return hSerial;
}

std::string readLine(HANDLE hSerial) {
    std::string line;
    char buffer[1];
    DWORD bytesRead;
    while (ReadFile(hSerial, buffer, 1, &bytesRead, NULL) && bytesRead > 0) {
        if (buffer[0] == '\n') {
            break;
        }
        if (buffer[0] != '\r') {
            line += buffer[0];
        }
    }
    return line;
}

int main() {
    for (const auto& port : possible_ports) {
        HANDLE hSerial = openSerialPort("\\\\.\\" + port);
        if (hSerial == INVALID_HANDLE_VALUE) {
            continue;
        }

        // Wait for Arduino to reset
        Sleep(2000);
        PurgeComm(hSerial, PURGE_RXCLEAR | PURGE_TXCLEAR);

        // Send 'E' for enroll
        DWORD bytesWritten;
        char command = 'E';
        WriteFile(hSerial, &command, 1, &bytesWritten, NULL);

        time_t start_time = time(NULL);
        while (time(NULL) - start_time < 30) {  // Wait up to 30 seconds for enrollment
            std::string line = readLine(hSerial);
            if (!line.empty()) {
                if (line.find("ENROLLED ID:") == 0) {
                    std::string id_str = line.substr(12);
                    // Trim whitespace
                    size_t first = id_str.find_first_not_of(" \t\r\n");
                    size_t last = id_str.find_last_not_of(" \t\r\n");
                    if (first != std::string::npos) {
                        id_str = id_str.substr(first, (last - first + 1));
                    }
                    CloseHandle(hSerial);
                    std::cout << id_str << std::endl;
                    return 0;
                }
            }
        }
        CloseHandle(hSerial);
    }
    std::cout << "0" << std::endl;
    return 0;
}