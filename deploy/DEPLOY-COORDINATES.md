# Deployment Guide: Generate Customer Coordinates

## Status
- ✅ Artisan Command sudah dibuat: `app/Console/Commands/GenerateCustomerCoordinates.php`
- ✅ File siap di-upload ke server
- ⏳ Menunggu eksekusi via SSH

## Step 1: Upload Artisan Command ke Server

Jalankan dari PowerShell (lokal):
```powershell
$SERVER = "root@172.18.20.136"
$REMOTE_PATH = "/var/www/ladapala-bill"

scp "app\Console\Commands\GenerateCustomerCoordinates.php" `
  "${SERVER}:${REMOTE_PATH}/app/Console/Commands/"
```

## Step 2: Jalankan Command di Server

```powershell
ssh $SERVER "cd $REMOTE_PATH && php artisan customers:generate-coordinates"
```

### Output yang diharapkan:
```
Generating coordinates for 18 customers...
Location: Desa Jatitengah, Selopuro, Blitar
Spacing: ~0.3 km (~277 meters)

 18/18 [████████████████████████████████] 100%

✅ Coordinates generated successfully!

┌─────────┬───────┐
│ Metric  │ Count │
├─────────┼───────┤
│ Created │ 18    │
│ Updated │ 0     │
│ Total   │ 18    │
└─────────┴───────┘

Coordinate range:
  Latitude:  -8.145 to -8.1225
  Longitude: 112.285 to 112.295
```

## Step 3: Verifikasi Koordinat di Database

```powershell
ssh $SERVER "cd /var/www/ladapala-bill && php artisan tinker --execute='
\$coords = App\\\Models\\\CustomerCoordinate::all();
foreach (\$coords as \$c) {
    echo \"Customer ID: {\$c->customer_id} | Lat: {\$c->latitude} | Lng: {\$c->longitude}\n\";
}
'"
```

## Opsi: Custom Coordinates

Jika ingin mengubah base location atau spacing:

```powershell
ssh $SERVER "cd /var/www/ladapala-bill && php artisan customers:generate-coordinates `
  --base-lat=-8.145000 `
  --base-lng=112.285000 `
  --spacing=0.0025 `
  --cols=4"
```

**Parameter:**
- `--base-lat`: Base latitude (default: -8.145000)
- `--base-lng`: Base longitude (default: 112.285000)
- `--spacing`: Jarak antar pelanggan dalam derajat (default: 0.0025 = ~277m)
- `--cols`: Kolom per baris grid (default: 4)

## Catatan Teknis

**Jarak antar pelanggan:**
- 0.0025 derajat = ~277 meter
- 0.0020 derajat = ~222 meter  
- 0.0030 derajat = ~333 meter

**Lokasi referensi (Desa Jatitengah, Selopuro, Blitar):**
- Latitude: -8.145°
- Longitude: 112.285°
- Region: Desa Jatitengah, Kecamatan Selopuro, Kabupaten Blitar, Jawa Timur

---

**Status file:**
- Lokal: `app/Console/Commands/GenerateCustomerCoordinates.php`
- Server: `/var/www/ladapala-bill/app/Console/Commands/GenerateCustomerCoordinates.php` (belum di-upload)
