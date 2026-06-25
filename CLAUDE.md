# Ladapala Bill - Project Documentation

## Project Context
- **Type**: Web Application
- **Framework**: Laravel + Vue (Inertia.js)
- **Environment**: Development server at 172.18.20.136

## Active Work/Features

### Customer Coordinates (Feature)
We have added a feature to manage and generate coordinates for customers on the map.
- Coordinates are stored in `App\Models\CustomerCoordinate`.
- Grid-based dummy coordinates generation is handled by the `GenerateCustomerCoordinates` artisan command.
- Frontend binds to `customer.coordinate.latitude` and `longitude` in `CustomerForm.vue`.
- See `CHANGELOG.md` for a complete list of changes related to this feature.
- Deployment instructions are available in `deploy/DEPLOY-COORDINATES.md`.

## Scripts
- **Deploy Coordinates**: `deploy\deploy-coordinates.ps1` (PowerShell script to upload and run the coordinate generation command on the test server).
