# 🚗 SmartGuard Parking System (IoT & Microservices)

![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![PowerShell](https://img.shields.io/badge/PowerShell-5391FE?style=for-the-badge&logo=powershell&logoColor=white)

**SmartGuard Parking System** adalah sistem pemantauan parkir real-time
berbasis Microservices. Proyek ini dibuat oleh **Alfonsus Sitanggang**.

------------------------------------------------------------------------

## 📋 Daftar Isi

1.  Arsitektur Sistem
2.  Teknologi yang Digunakan
3.  Struktur Folder
4.  Prasyarat
5.  Instalasi & Cara Menjalankan
6.  API Endpoint
7.  Troubleshooting

------------------------------------------------------------------------

## 🏗 Arsitektur Sistem

1.  Sensor Simulator (PowerShell)
2.  API Gateway (PHP Container)
3.  Database (MySQL Container)
4.  Dashboard Monitoring
5.  Keycloak Container

------------------------------------------------------------------------

## 🛠 Teknologi

Docker, PHP, MySQL, PowerShell, PhpMyAdmin, Keycloak

------------------------------------------------------------------------

## 📂 Struktur Folder

/server-docker\
├── docker-compose.yml\
├── Dockerfile\
├── simulasi_sensor.ps1\
├── mysql-dump/init.sql\
└── www/ (index.php, api_sensor.php, db_connect.php)

------------------------------------------------------------------------

## ⚙ Prasyarat

Docker Desktop, PowerShell, Browser

------------------------------------------------------------------------

## 🚀 Cara Menjalankan

### 1. Jalankan Docker

    docker-compose up -d --build

### 2. Akses Layanan

-   Dashboard: http://localhost:8080\
-   PhpMyAdmin: http://localhost:8888\
-   Keycloak: http://localhost:8180

### 3. Jalankan Sensor

    ./simulasi_sensor.ps1

------------------------------------------------------------------------

## 📡 API Endpoint

POST → http://localhost:8080/api_sensor.php\
Body:

    {
      "slot_name": "A1",
      "status": "OCCUPIED"
    }

------------------------------------------------------------------------

## 🔧 Troubleshooting

ExecutionPolicy, DB Connection, Port Conflict

------------------------------------------------------------------------

## 👤 Author

**Alfonsus Sitanggang**
