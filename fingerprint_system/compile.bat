@echo off
REM Compile and Test Fingerprint Capture
REM This script compiles the C++ program and optionally tests it

echo ======================================
echo Fingerprint System - Compile & Test
echo ======================================
echo.

REM Check if r305_capture.cpp exists
if not exist r305_capture.cpp (
    echo ERROR: r305_capture.cpp not found!
    echo Make sure you're in the fingerprint_system directory
    pause
    exit /b 1
)

REM Compile
echo Compiling r305_capture.cpp...
g++ r305_capture.cpp -o r305_capture.exe

if not exist r305_capture.exe (
    echo ERROR: Compilation failed!
    echo Make sure g++ is installed and in your PATH
    echo Download MinGW from: https://www.mingw-w64.org/
    pause
    exit /b 1
)

echo Successfully compiled r305_capture.exe!
echo.

REM Ask if user wants to test
set /p TEST="Do you want to test the fingerprint capture now? (Y/N): "
if /i "%TEST%"=="Y" (
    echo.
    echo Testing fingerprint capture...
    echo Place your finger on the sensor when prompted.
    echo Waiting for 15 seconds...
    echo.
    r305_capture.exe
    if errorlevel 1 (
        echo No fingerprint detected
    ) else (
        echo Test completed!
    )
)

pause
