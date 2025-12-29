# SIMAS - Sistem Informasi Manajemen (Dockerized)

Panduan ini menjelaskan cara instalasi dan menjalankan aplikasi SIMAS menggunakan Docker, mulai dari clone repository hingga import database.

## Prasyarat
Pastikan di komputer Anda sudah terinstall:
- [Git](https://git-scm.com/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop) (Pastikan Docker Engine berjalan)

---

## Langkah Instalasi

### 1. Clone Repository
Clone project ini ke komputer lokal Anda.

```bash
git clone <URL_REPOSITORY_ANDA>
cd SIMAS
```

### 2. Persiapkan Environment Variable
Aplikasi ini dikonfigurasi untuk menggunakan file `.env2` sebagai sumber konfigurasi environment di dalam Docker.

1. Pastikan file `.env2` ada di root project.
2. Cek konfigurasi database di dalam `.env2` agar sesuai dengan service Docker:

   ```ini
   DB_CONNECTION=pgsql
   DB_HOST=db
   DB_PORT=5432
   DB_DATABASE=simas
   DB_USERNAME=simas
   DB_PASSWORD="Simas2023##"
   ```
   > **Catatan:** `DB_HOST=db` merujuk pada nama service database di `compose.yaml`.

### 3. Build dan Jalankan Docker
Jalankan perintah berikut untuk membangun image dan menjalankan container:

```bash
docker compose up --build -d
```
- `--build`: Membangun ulang image jika ada perubahan di Dockerfile.
- `-d`: Menjalankan container di background (detached mode).

Tunggu hingga proses selesai. Anda bisa mengecek status container dengan:
```bash
docker compose ps
```
Pastikan status container `be-simas` dan `db-simas` adalah **Running**.

### 4. Install Dependency PHP (Jika belum terbawa di image)
Secara default `Dockerfile` sudah melakukan copy source code. Jika folder `vendor` belum ada atau perlu update, jalankan:

```bash
docker compose exec app composer install
```

---

## Import Database (PostgreSQL)

Setelah container berjalan, Anda perlu mengimport data dari file `simas.sql` ke dalam database PostgreSQL di Docker.

### Cara Import via Terminal

Jalankan perintah berikut di terminal (pastikan Anda berada di folder root project dimana file `simas.sql` berada):

```bash
docker exec -i db-simas psql -U simas -d simas < simas.sql
```

**Penjelasan Perintah:**
- `docker exec -i db-simas`: Menjalankan perintah di dalam container bernama `db-simas`.
- `psql -U simas -d simas`: Masuk ke PostgreSQL user `simas` dan database `simas`.
- `< simas.sql`: Menginput isi file `simas.sql` ke dalam perintah tersebut.

Jika diminta password, masukkan password sesuai di `.env2` / `compose.yaml` (Default: `Simas2023##`), namun biasanya perintah di atas langsung jalan jika konfigurasi trust/md5 sesuai.

---

## Akses Aplikasi

Jika semua langkah berhasil:
- **Aplikasi Web**: Buka browser dan akses [http://localhost:5000](http://localhost:5000)

## Troubleshooting Berguna

- **Masuk ke container App (Shell Apache/PHP)**:
  ```bash
  docker compose exec app bash
  ```
- **Masuk ke shell Database**:
  ```bash
  docker compose exec db bash
  ```
- **Lihat Logs**:
  ```bash
  docker compose logs -f
  ```
- **Restart Container**:
  ```bash
  docker compose restart
  ```
