@echo off
title StudyNotes - React Server Diagnostics
color 0B

echo.
echo ==========================================
echo    StudyNotes React Server Diagnostics
echo ==========================================
echo.

echo [1/6] Checking current directory...
if not exist "package.json" (
    echo ❌ Error: package.json not found
    echo    Please run this from c:\wamp64\www\main\
    pause
    exit /b 1
)
echo ✅ In correct directory: %CD%

echo.
echo [2/6] Checking Node.js installation...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Node.js not found or not in PATH
    echo    Please install Node.js from https://nodejs.org/
    pause
    exit /b 1
)
echo ✅ Node.js is installed
node --version

echo.
echo [3/6] Checking npm installation...
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ npm not found
    pause
    exit /b 1
)
echo ✅ npm is available
npm --version

echo.
echo [4/6] Checking dependencies...
if not exist "node_modules" (
    echo ⚠️  node_modules folder not found
    echo    Installing dependencies...
    npm install
    if %errorlevel% neq 0 (
        echo ❌ Failed to install dependencies
        pause
        exit /b 1
    )
) else (
    echo ✅ Dependencies installed
)

echo.
echo [5/6] Checking port availability...
netstat -ano | findstr ":3002" >nul 2>&1
if %errorlevel% equ 0 (
    echo ⚠️  Port 3002 is already in use
    echo    Trying to use port 3003 instead...
    set PORT=3003
) else (
    echo ✅ Port 3002 is available
    set PORT=3002
)

echo.
echo [6/6] Starting React development server...
echo    Server will start on http://localhost:%PORT%
echo    Press Ctrl+C to stop the server
echo.
echo Starting in 3 seconds...
timeout /t 3 /nobreak >nul

set PORT=%PORT%
npm start

pause
