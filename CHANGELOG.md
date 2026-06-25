# Changelog

Semua perubahan penting pada project ini didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added - Customer Coordinates Feature

#### Backend
- **New Artisan Command**: `GenerateCustomerCoordinates.php`
  - Generates mock GPS coordinates for customers in Desa Jatitengah, Selopuro, Blitar
  - Automatically creates/updates `CustomerCoordinate` records linked to customers
  - Supports customizable grid parameters (base latitude/longitude, spacing, columns)
  - Usage: `php artisan customers:generate-coordinates [options]`
  - Options:
    - `--base-lat`: Base latitude (default: -8.145000)
    - `--base-lng`: Base longitude (default: 112.285000)
    - `--spacing`: Spacing between customers in degrees (default: 0.0025 ≈ 277m)
    - `--cols`: Number of columns in grid (default: 4)

- **Model**: `App\Models\CustomerCoordinate`
  - Stores latitude/longitude for each customer
  - Uses decimal:7 cast for precise coordinate storage
  - Relationship: BelongsTo Customer
  - Tenant-scoped using BelongsToTenant trait

#### Frontend
- **Updated**: `resources/js/Pages/Cust/Customers/Components/CustomerForm.vue`
  - Modified coordinate field binding to read from `customer.coordinate.latitude` and `customer.coordinate.longitude`
  - Maintains fallback to direct `customer.latitude`/`customer.longitude` for backward compatibility
  - Form now properly displays coordinates from CustomerCoordinate relationship

#### Deployment
- **New**: `deploy/DEPLOY-COORDINATES.md`
  - Complete deployment guide for coordinate generation feature
  - Step-by-step SSH commands to upload and execute
  - Database verification instructions
  - Reference location details and distance calculations

- **New**: `deploy/deploy-coordinates.ps1`
  - PowerShell deployment script (Windows-friendly)
  - Automated 4-step deployment process:
    1. Create remote directories
    2. Upload GenerateCustomerCoordinates.php via SCP
    3. Execute artisan command on server
    4. Verify coordinates in database
  - Target server: `root@172.18.20.136` (dev/test environment)

- **New**: `deploy/generate-coords.php`
  - PHP helper script for coordinate generation
  - Can be run standalone or integrated with deployment

### Technical Details

**Database Schema**:
- Table: `customer_coordinates`
- Fields: `id`, `tenant_id`, `customer_id`, `latitude`, `longitude`, `created_at`, `updated_at`
- Relationship: Each customer has one CustomerCoordinate record

**Grid Generation Algorithm**:
- Uses grid-based placement with random micro-variations (±50m)
- Prevents exact overlaps while maintaining geographic clustering
- Latitude range: -8.145 to -8.1225 (≈27.5 km north-south)
- Longitude range: 112.285 to 112.295 (≈9 km east-west)
- Default spacing: 0.0025 degrees ≈ 277 meters between customers

**Coordinate Precision**:
- Stored as DECIMAL(10, 7) for 7 decimal places
- Precision: ~1.1cm at equator
- Sufficient for neighborhood-level customer mapping

### Deployment Status
- ✅ Code ready (local)
- ⏳ Pending deployment to dev server (172.18.20.136)
- Required: Run `deploy/deploy-coordinates.ps1` from Windows PowerShell

### Next Steps
1. Deploy script to dev server
2. Verify coordinates in database
3. Test map visualization
4. Deploy to production when ready
5. Consider API endpoint for retrieving customer coordinates

---

## [Previous Releases]

...history to be added...
