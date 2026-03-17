# Date Format Standard

## Overview

This document defines the standard for date handling across the Optima application.

## Rules

### Storage (Database)

- **Always store dates in `Y-m-d` format** (e.g. `2025-03-12`)
- Database columns use `DATE` type
- API requests and responses that pass dates to/from backend use `Y-m-d`

### Display (User-Facing)

- **Display dates to users in `DD/MM/YYYY` format** (e.g. `12/03/2025`)
- Indonesian locale: `d/m/Y`

### Implementation

#### Backend (PHP)

- Use `format_date_jakarta()` from `app/Helpers/date_helper.php` with `$format = 'd/m/Y'` for display
- API responses that need "display" values can add `*_display` fields (e.g. `tanggal_mulai_display` = d/m/Y)

#### Frontend (JavaScript)

- **Display**: Use `toLocaleDateString('id-ID')` or `Intl.DateTimeFormat` with `day: '2-digit', month: '2-digit', year: 'numeric'` for DD/MM/YYYY
- **Input**: 
  - `type="date"` inputs use Y-m-d internally; convert for display if needed
  - Or use a datepicker (e.g. Flatpickr) with locale `id` and format `d/m/Y`; submit value as Y-m-d to backend

### Summary

| Context   | Format   | Example    |
|----------|----------|------------|
| Database | Y-m-d    | 2025-03-12 |
| API I/O  | Y-m-d    | 2025-03-12 |
| Display  | DD/MM/YYYY (d/m/Y) | 12/03/2025 |
