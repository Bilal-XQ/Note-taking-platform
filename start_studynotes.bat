@echo off
title StudyNotes - Development Environment
color 0A

echo.
echo ========================================
echo    StudyNotes Development Environment
echo ========================================
echo.

echo [1/4] Checking WAMP services...
tasklist /FI "IMAGENAME eq wampmanager.exe" 2>NUL | find /I /N "wampmanager.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo ✅ WAMP is running
) else (
    echo ❌ WAMP is not running - please start WAMP64
    echo    Look for the green WAMP icon in your system tray
    pause
    exit /b 1
)

echo [2/4] Checking directory...
if not exist "package.json" (
    echo ❌ Not in StudyNotes directory
    echo    Please run this from c:\wamp64\www\main\
    pause
    exit /b 1
)
echo ✅ In correct directory

echo [3/4] Starting React development server...
echo    This will open on http://localhost:3002
echo.
start /B npm start

echo [4/4] Opening application links...
timeout /t 5 /nobreak >nul
start http://localhost/main/system_status.php

echo.
echo ========================================
echo    StudyNotes is now running!
echo ========================================
echo.
echo 🎯 React App: http://localhost:3002
echo 🏠 Static Site: http://localhost/main/
echo 👤 Student Login: http://localhost/main/src/views/student/login.php
echo 🔧 Admin Panel: http://localhost/main/admin/dashboard.php
echo 📊 Status Page: http://localhost/main/system_status.php
echo.
echo Press any key to exit...
pause >nul
