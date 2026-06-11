# Sistem Inventaris SMK Labschool Unesa 1 Surabaya

## Cara Menjalankan

### Syarat
- XAMPP (PHP 8.2+, MySQL)
- Composer

### Langkah Install

**1. Pastikan XAMPP berjalan — Apache ON, MySQL ON**

**2. Buat database di phpMyAdmin**
- Buka: http://localhost/phpmyadmin
- Klik New → nama database: `inventory_smk` → klik Create

**3. Buka terminal, masuk ke folder project:**
```
cd inventory-smk
```

**4. Install dependencies:**
```
composer install
```

**5. Buat file .env:**
```
copy .env.example .env
```

**6. Generate key:**
```
php artisan key:generate
```

**7. Edit file .env — ubah bagian database:**
```
DB_DATABASE=inventory_smk
DB_USERNAME=root
DB_PASSWORD=
```

**8. Jalankan migrasi fresh:**
```
php artisan migrate:fresh
```

**9. Isi data awal:**
```
php artisan db:seed
```

**10. Storage link:**
```
php artisan storage:link
```

**11. Jalankan server:**
```
php artisan serve
```

**12. Buka browser:**
```
http://localhost:8000
```

## Akun Login

| Role | Username | Password |
|------|----------|----------|
| Administrator | admin | admin123 |
| User | petugas | user123 |
