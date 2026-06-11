# Panduan Instalasi — Windows XAMPP

## Langkah Cepat (jalankan install.bat)

1. Ekstrak zip ke folder, misal: `C:\xampp\htdocs\inventory-smk\`
2. Klik dua kali `install.bat`
3. Ikuti instruksi di layar

---

## Langkah Manual

### 1. Aktifkan ext-gd di PHP (XAMPP)
Buka `C:\xampp\php\php.ini`, cari baris:
```
;extension=gd
```
Hapus titik koma di depannya menjadi:
```
extension=gd
```
Simpan, lalu restart Apache di XAMPP Control Panel.

### 2. Install dependencies
```bash
composer update --ignore-platform-req=ext-gd
```

### 3. Setup environment
```bash
copy .env.example .env
php artisan key:generate
```

### 4. Buat database
- Buka XAMPP Control Panel → Start MySQL
- Buka `http://localhost/phpmyadmin`
- Buat database baru: `inventory_smk`

### 5. Konfigurasi .env
Edit file `.env`:
```
DB_DATABASE=inventory_smk
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Migrasi dan seed
```bash
php artisan migrate
php artisan db:seed
```

### 7. Storage link
```bash
php artisan storage:link
```

### 8. Jalankan aplikasi
```bash
php artisan serve
```
Buka browser: `http://localhost:8000`

---

## Akun Default

| Role          | Username | Password  |
|---------------|----------|-----------|
| Administrator | admin    | admin123  |
| User          | petugas  | user123   |

---

## Troubleshooting

**Error: ext-gd missing**
→ Aktifkan `extension=gd` di `C:\xampp\php\php.ini`

**Error: Could not connect to database**
→ Pastikan MySQL XAMPP berjalan dan database `inventory_smk` sudah dibuat

**Error: Please run composer update**
→ Jalankan: `composer update --ignore-platform-req=ext-gd`
