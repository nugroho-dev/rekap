# LKPM System - Implementation Summary

## Overview
Complete LKPM (Laporan Kegiatan Penanaman Modal) system with two separate tables for different report types:
- **LKPM UMK**: Semester reports for small businesses (27 columns)
- **LKPM Non-UMK**: Quarterly reports for larger businesses (33 columns)

## Database Tables

### 1. lkpm_umk (Semester Reports)
**Key Fields:**
- `id_laporan` (unique) - Report identifier
- `no_kode_proyek` - Project code
- `skala_risiko` - Risk scale (Rendah/Menengah/Tinggi)
- `kbli` - Business classification code
- `tanggal_laporan` - Report date
- `periode_laporan` - Semester 1 or 2
- `tahun_laporan` - Report year
- `nama_pelaku_usaha` - Business name
- `nomor_induk_berusaha` - Business registration number (NIB)

**Capital Fields:**
- Modal Kerja (Working Capital): triwulan_ini, triwulan_lalu, akumulasi
- Modal Tetap (Fixed Capital): triwulan_ini, triwulan_lalu, akumulasi

**Labor Fields:**
- `tambahan_tenaga_kerja_l` - Additional male workers
- `tambahan_tenaga_kerja_p` - Additional female workers

**Location Fields:**
- alamat, kelurahan, kecamatan, kab_kota, provinsi

**Status Fields:**
- `status_laporan` - Report status (Disetujui/Ditolak/Menunggu)
- `catatan_permasalahan` - Problem notes

**Officer Fields:**
- nama_petugas, jabatan_petugas, telp_petugas, email_petugas

### 2. lkpm_non_umk (Quarterly Reports)
**Key Fields:**
- `no_laporan` (unique) - Report number
- `no_kode_proyek` - Project code
- `tanggal_laporan` - Report date
- `periode_laporan` - Triwulan 1/2/3/4
- `tahun_laporan` - Report year
- `nama_pelaku_usaha` - Business name
- `kbli` - Business classification code
- `rincian_kbli` - Detailed KBLI description
- `status_penanaman_modal` - Investment status (PMDN/PMA)

**Investment Planning:**
- `nilai_modal_tetap_rencana` - Planned fixed capital
- `nilai_total_investasi_rencana` - Planned total investment

**Investment Realization:**
- `nilai_tambahan_investasi_realisasi` - Additional investment realization
- `nilai_akumulasi_investasi_realisasi` - Accumulated investment realization
- `nilai_modal_tetap_realisasi` - Fixed capital realization
- `nilai_total_investasi_realisasi` - Total investment realization

**Labor (TKI - Indonesian Workers):**
- `tki_rencana`, `tki_tambahan`, `tki_realisasi`

**Labor (TKA - Foreign Workers):**
- `tka_rencana`, `tka_tambahan`, `tka_realisasi`

**Other Fields:**
- kewenangan, tahap_laporan, status_laporan, catatan_permasalahan
- Location: alamat, kelurahan, kecamatan, kab_kota, provinsi
- Contact: kontak_nama, kontak_hp, jabatan, kontak_email

## Files Created

### Migrations
1. `database/migrations/2025_12_05_000001_create_lkpm_umk_table.php`
2. `database/migrations/2025_12_05_000002_create_lkpm_non_umk_table.php`

### Models
1. `app/Models/LkpmUmk.php`
2. `app/Models/LkpmNonUmk.php`

### Import Classes
1. `app/Imports/LkpmUmkImport.php`
2. `app/Imports/LkpmNonUmkImport.php`

### Controller
1. `app/Http/Controllers/LkpmController.php`

### Views
1. `resources/views/admin/lkpm/index.blade.php` - Main view with tabs
2. `resources/views/admin/lkpm/partials/table-umk.blade.php` - UMK data table
3. `resources/views/admin/lkpm/partials/table-non-umk.blade.php` - Non-UMK data table

### Routes
Added to `routes/web.php`:
- GET `/lkpm` - View LKPM data (with tabs)
- POST `/lkpm/import-umk` - Import UMK Excel file
- POST `/lkpm/import-non-umk` - Import Non-UMK Excel file
- DELETE `/lkpm/umk/{id}` - Delete UMK record
- DELETE `/lkpm/non-umk/{id}` - Delete Non-UMK record

## Features

### 1. Dual Import System
- **Upsert Strategy**: Prevents duplicate records using unique keys
  - UMK: Uses `id_laporan` as unique identifier
  - Non-UMK: Uses `no_laporan` as unique identifier
- **Smart Data Parsing**:
  - Date parsing: Handles Excel serial dates, d/m/Y, m/d/Y, Y-m-d formats
  - Decimal parsing: Removes currency symbols (Rp, $), thousands separators
  - Integer parsing: Safe conversion with default to 0

### 2. Tab-Based Interface
- **UMK Tab**: Shows semester reports with badge count
- **Non-UMK Tab**: Shows quarterly reports with badge count
- **Tab Preservation**: Import actions redirect back to correct tab

### 3. Search & Filter
- **Search**: By nama_pelaku_usaha, NIB, no_laporan, id_laporan, no_kode_proyek
- **Year Filter**: Dropdown of available years
- **Period Filter**:
  - UMK: Semester 1/2
  - Non-UMK: Triwulan 1/2/3/4

### 4. Data Display
- **Compact Tables**: Shows key information in tabular format
- **Detail Modals**: Click eye icon to view complete record details
- **Color-Coded Badges**:
  - Risk levels (UMK): Rendah (green), Menengah (yellow), Tinggi (red)
  - Investment status (Non-UMK): PMDN (blue), PMA (green)
  - Report status: Disetujui (green), Ditolak (red), Menunggu (yellow)

### 5. CRUD Operations
- **Create**: Import from Excel files (.xlsx, .xls, .csv)
- **Read**: View list with filters, view details in modal
- **Delete**: Soft delete with confirmation dialog
- **Update**: Automatic upsert on re-import

### 6. Data Validation
- **File Upload**: Only accepts Excel formats
- **Required Fields**: Controller validates file presence
- **Error Handling**: User-friendly error messages

## Excel Import Requirements

### LKPM UMK Columns
Expected column headers (case-insensitive, can use underscore or space):
```
id_laporan, no_kode_proyek, skala_risiko, kbli, tanggal_laporan, 
periode_laporan, tahun_laporan, nama_pelaku_usaha, nomor_induk_berusaha,
modal_kerja_triwulan_ini, modal_kerja_triwulan_lalu, modal_kerja_akumulasi,
modal_tetap_triwulan_ini, modal_tetap_triwulan_lalu, modal_tetap_akumulasi,
tambahan_tenaga_kerja_l, tambahan_tenaga_kerja_p,
alamat, kelurahan, kecamatan, kab_kota, provinsi,
status_laporan, catatan_permasalahan,
nama_petugas, jabatan_petugas, telp_petugas, email_petugas
```

### LKPM Non-UMK Columns
```
no_laporan, tanggal_laporan, periode_laporan, tahun_laporan, 
nama_pelaku_usaha, kbli, rincian_kbli, status_penanaman_modal,
alamat, kelurahan, kecamatan, kab_kota, provinsi,
no_kode_proyek, kewenangan, tahap_laporan, status_laporan,
nilai_modal_tetap_rencana, nilai_total_investasi_rencana,
nilai_tambahan_investasi_realisasi, nilai_akumulasi_investasi_realisasi,
nilai_modal_tetap_realisasi, nilai_total_investasi_realisasi,
tki_rencana, tki_tambahan, tki_realisasi,
tka_rencana, tka_tambahan, tka_realisasi,
catatan_permasalahan, kontak_nama, kontak_hp, jabatan, kontak_email
```

## Usage Instructions

### 1. Access LKPM Page
Navigate to: `http://your-domain/lkpm`

### 2. Import Data
1. Click "Import UMK" or "Import Non-UMK" button
2. Select Excel file (.xlsx, .xls, or .csv)
3. Click "Import" button
4. System will:
   - Parse dates and numbers automatically
   - Create new records or update existing ones (based on unique key)
   - Show success/error message

### 3. View Data
- Switch between tabs to view UMK vs Non-UMK data
- Use search box to find specific businesses/reports
- Use filters to narrow down by year and period
- Click eye icon to view complete record details

### 4. Delete Data
- Click trash icon next to record
- Confirm deletion (soft delete - can be restored via database)

## Technical Details

### Database Indexes
**LKPM UMK:**
- `no_kode_proyek`
- `tanggal_laporan`
- `tahun_laporan`
- `nomor_induk_berusaha`

**LKPM Non-UMK:**
- `no_laporan`
- `tanggal_laporan`
- `tahun_laporan`
- `no_kode_proyek`

### Soft Deletes
Both tables use Laravel's SoftDeletes trait:
- Records are not permanently deleted
- `deleted_at` timestamp marks deletion
- Can be restored via Eloquent: `Model::withTrashed()->find($id)->restore()`

### Data Types
- **Decimal(20,2)**: All monetary values (modal, investasi fields)
- **Date**: tanggal_laporan
- **Integer**: Year, worker counts (TKI, TKA, tenaga kerja)
- **String**: Text fields (names, addresses, codes)

### Pagination
- 20 records per page
- Preserves search and filter parameters in pagination links

## Migration Status
✅ Migrations have been successfully run:
- `2025_12_05_000001_create_lkpm_umk_table` - DONE
- `2025_12_05_000002_create_lkpm_non_umk_table` - DONE

## Next Steps (Optional Enhancements)

1. **Export Functionality**
   - Add Excel export for filtered data
   - Add PDF export for reports

2. **Statistics Page**
   - Summary by year/period
   - Charts for investment trends
   - Worker statistics (TKI vs TKA)

3. **Batch Operations**
   - Bulk delete
   - Bulk status updates

4. **Advanced Filters**
   - By kecamatan/kelurahan
   - By KBLI category
   - By investment range

5. **Data Validation**
   - Add form for manual entry/editing
   - Validate business logic (e.g., akumulasi should equal sum)

6. **Audit Trail**
   - Track who imported files
   - Log all changes to records

## Support
For issues or questions about the LKPM system, check:
1. Laravel logs: `storage/logs/laravel.log`
2. Import errors: Displayed in flash messages on redirect
3. Database queries: Use Laravel Telescope or Clockwork for debugging
