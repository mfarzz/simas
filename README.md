# 🚀 SIMAS – Docker Setup & Database Import Guide

Dokumentasi ini menjelaskan cara **menjalankan SIMAS menggunakan Docker** serta **mengimpor database PostgreSQL dari file dump SQL**.

---

## 📦 Prasyarat

Pastikan sudah terpasang:

* **Docker**
* **Docker Compose**
* (Opsional) **Git**
* (Opsional) **DataGrip / pgAdmin** untuk akses database

Cek instalasi:

```bash
docker --version
docker compose version
```

---

## 📁 Struktur Project (Contoh)

```text
simas_baru/
│
├── docker-compose.yml
├── simas.sql
├── README.md
│
├── backend/        # Laravel / Backend
│   └── .env
│
└── frontend/       # Frontend (jika ada)
```

---

## 🐳 Menjalankan Docker

### 1️⃣ Jalankan Container

Dari root project:

```bash
docker compose up -d
```

Cek status container:

```bash
docker ps
```

Pastikan container **PostgreSQL berjalan** (misalnya `db-simas`).

---

## 🗄️ Konfigurasi Database PostgreSQL

### Kredensial Default

| Konfigurasi | Nilai                       |
| ----------- | --------------------------- |
| Database    | `simas`                     |
| Username    | `simas`                     |
| Password    | `simas`                     |
| Port        | `5432` (internal container) |

---

## 📥 Import Database (`simas.sql`)

### 🔹 Opsi Aman (Recommended)

**Drop & recreate database lalu import ulang**

#### 1️⃣ Masuk ke PostgreSQL container

```bash
docker exec -it db-simas psql -U simas
```

#### 2️⃣ Drop & buat ulang database

```sql
DROP DATABASE simas;
CREATE DATABASE simas;
\q
```

#### 3️⃣ Copy file SQL ke container

```bash
docker cp simas.sql db-simas:/simas.sql
```

#### 4️⃣ Import database

```bash
docker exec -it db-simas psql -U simas -d simas -f /simas.sql
```

⏳ Tunggu hingga proses selesai (dump besar bisa memakan waktu).

---

## ✅ Verifikasi Import

Masuk ke database:

```bash
docker exec -it db-simas psql -U simas -d simas
```

Cek tabel & view:

```sql
\dt
\dv
```

Jika objek database muncul → **IMPORT BERHASIL** ✅

---

## ⚙️ Konfigurasi Laravel (`.env`)

Sesuaikan file `.env` backend:

```env
DB_CONNECTION=pgsql
DB_HOST=db-simas
DB_PORT=5432
DB_DATABASE=simas
DB_USERNAME=simas
DB_PASSWORD=simas
```

Jalankan test koneksi:

```bash
docker exec -it be-simas php artisan migrate:status
```

---

## 🧰 Akses Database via GUI (Opsional)

Gunakan DataGrip / pgAdmin dengan konfigurasi:

| Field    | Value                           |
| -------- | ------------------------------- |
| Host     | `127.0.0.1`                     |
| Port     | `5433` *(sesuai expose docker)* |
| User     | `simas`                         |
| Password | `simas`                         |
| Database | `simas`                         |

---

## 🧹 Troubleshooting

### ❌ Password authentication failed

* Pastikan `pg_hba.conf` tidak bermasalah
* Restart PostgreSQL container:

```bash
docker restart db-simas
```

### ❌ Error table/view already exists

* Gunakan **drop database** atau **drop schema public cascade**
* Jangan import dump ke database yang sudah berisi data lama

---

## 📌 Catatan Penting

* Import SQL **hanya dilakukan sekali** setelah database bersih
* Untuk environment **production**, gunakan user & password berbeda
* Jangan commit file `.env` ke repository publik

---

## 👨‍💻 Maintainer

**SIMAS – Neo Telemetri**
Dokumentasi dibuat untuk kebutuhan development & deployment internal.

