# Daftar Akun SIMAS

> Ditemukan dari `simas.sql` · Password tersimpan dalam format **bcrypt hash** (tidak dapat di-decode).
> Gunakan fitur _reset password_ atau update langsung via database jika lupa password.

---

## 🔑 Akun Pusat / Admin

| Username       | Nama                | Email                  |
| -------------- | ------------------- | ---------------------- |
| `admin`        | admin               | admin@gmail.com        |
| `perlengkapan` | Davit Rahman, S.Kom | admin@gmail.com        |
| `subdit`       | Sugito, S.Sos       | admin@gmail.com        |
| `kasilogistik` | Kasi Logistik       | kasilogistik@gmail.com |
| `kepalagudang` | Kepala Gudang       | kepalagudang@gmail.com |
| `akuntansi`    | Akuntansi           | akuntansi@gmail.com    |

---

## 🏛️ Akun Rektorat

| Username          | Nama                   | Email               |
| ----------------- | ---------------------- | ------------------- |
| `oprektorat`      | Operator Rektorat      | rektorat@gmail.com  |
| `pim_unit_rektor` | Pimpinan Unit Rektorat | akuntansi@gmail.com |

---

## 🏥 Akun Rumah Sakit

| Username | Nama                 | Email            |
| -------- | -------------------- | ---------------- |
| `oprs`   | Operator Rumah Sakit | oprs@unand.ac.id |

---

## 🎓 Akun Operator Fakultas

| Username               | Nama                                                   | Email                            |
| ---------------------- | ------------------------------------------------------ | -------------------------------- |
| `oppertanian`          | Operator Fakultas Pertanian                            | oppertanian@unand.ac.id          |
| `oppeternakan`         | Operator Fakultas Peternakan                           | oppeternakan@unand.ac.id         |
| `opkedokteran`         | Operator Fakultas Kedokteran                           | opkedokteran@unand.ac.id         |
| `opekonomi`            | Operator Fakultas Ekonomi                              | opekonomi@unand.ac.id            |
| `ophukum`              | Operator Fakultas Hukum                                | ophukum@unand.ac.id              |
| `opisip`               | Operator Fakultas Ilmu Sosial dan Politik              | opisip@unand.ac.id               |
| `opmipa`               | Operator Fakultas Matematika dan Ilmu Pengetahuan Alam | opmipa@unand.ac.id               |
| `opteknik`             | Operator Fakultas Teknik                               | opteknik@unand.ac.id             |
| `opilmubudaya`         | Operator Fakultas Ilmu Budaya                          | opilmubudaya@unand.ac.id         |
| `opfarmasi`            | Operator Fakultas Farmasi                              | opfarmasi@unand.ac.id            |
| `opteknologipertanian` | Operator Fakultas Teknologi Pertanian                  | opteknologipertanian@unand.ac.id |
| `oppasca`              | Operator Fakultas Pasca Sarjana                        | oppasca@unand.ac.id              |
| `opperawat`            | Operator Fakultas Keperawatan                          | opperawat@unand.ac.id            |
| `opkesmas`             | Operator Fakultas Kesehatan Masyarakat                 | opkesmas@unand.ac.id             |
| `opfti`                | Operator Fakultas Teknologi Informasi                  | opfti@unand.ac.id                |
| `opdoktergigi`         | Operator Fakultas Kedokteran Gigi                      | opdoktergigi@unand.ac.id         |
| `opbahasa`             | Operator Bahasa                                        | karfindo@gmail.com               |

---

## 👤 Akun Pimpinan Fakultas

| Username           | Nama             | Email                      |
| ------------------ | ---------------- | -------------------------- |
| `pimpinan_ekonomi` | Pimpinan Ekonomi | pimpinan_ekonomi@gmail.com |
| `kesmas`           | Pimpinan Kesmas  | aset@unand.ac.id           |

---

## 🧪 Akun Testing

| Username | Nama     | Email         |
| -------- | -------- | ------------- |
| `te`     | karfindo | tes@gmail.com |

---

## 🔧 Reset Password via Database

Jika perlu reset password akun tertentu, jalankan perintah berikut (ganti `username` dan `password_baru`):

```bash
docker exec -i db-simas psql -U simas -d simas -c \
  "UPDATE users SET password = '\$(php -r \"echo password_hash('password_baru', PASSWORD_BCRYPT);\")\' WHERE username = 'username';"
```

Atau lebih mudah lewat container PHP:

```bash
# 1. Generate hash dulu
docker exec -i be-simas php -r "echo password_hash('password_baru', PASSWORD_BCRYPT);"

# 2. Copy hasil hash, lalu update di database
docker exec -i db-simas psql -U simas -d simas -c \
  "UPDATE users SET password = '\$HASH_HASIL' WHERE username = 'username';"
```
