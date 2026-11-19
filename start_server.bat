@echo off
title Canteen Dawn Management System (POS)
color 0b
echo ==============================================================
echo           CANTEEN MANAGEMENT SYSTEM (POS) - LAUNCHER
echo ==============================================================
echo.
echo Starting Laravel Web Server on http://127.0.0.1:8000 ...
echo Press Ctrl+C at any time to stop the server.
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause

