@echo off
REM Batch script for starting the application on Windows
REM This script provides a simple way to launch the application using PowerShell

echo Starting the application using PowerShell...

REM Check if PowerShell is available
where powershell >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo PowerShell is not installed or not in PATH. Please install PowerShell and try again.
    exit /b 1
)

REM Execute the PowerShell script with execution policy bypass
powershell.exe -ExecutionPolicy Bypass -File "%~dp0start.ps1"

REM Check if the PowerShell script executed successfully
if %ERRORLEVEL% neq 0 (
    echo An error occurred while running the PowerShell script.
    echo Please check the logs for more information.
    exit /b 1
)

echo Application started successfully!
