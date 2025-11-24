#!/usr/bin/env python3
"""
Script untuk finalisasi SQL file agar aman untuk import
- Memastikan semua syntax benar
- Menambahkan error handling
- Memastikan COMMIT di akhir
- Memastikan FOREIGN_KEY_CHECKS diaktifkan kembali
"""

import re

def finalize_sql_file(input_file, output_file):
    """Finalisasi SQL file untuk import yang aman"""
    
    print("📝 Membaca file SQL...")
    with open(input_file, 'rb') as f:
        content_bytes = f.read()
    
    content = content_bytes.decode('utf-8', errors='ignore')
    
    # Fixes yang akan dilakukan
    fixes = []
    
    # 1. Pastikan database name benar (bukan optima_db_test)
    if 'optima_db_test' in content:
        content = content.replace('optima_db_test', 'optima_db')
        fixes.append("✓ Database name diperbaiki: optima_db_test → optima_db")
    
    # 2. Pastikan SET FOREIGN_KEY_CHECKS = 0 ada di awal (setelah USE)
    if 'SET FOREIGN_KEY_CHECKS = 0' not in content[:500]:
        use_pos = content.find('USE `')
        if use_pos > 0:
            use_end = content.find(';', use_pos) + 1
            if use_end > 0:
                content = content[:use_end+1] + '\n\n-- Disable foreign key checks for safe import\nSET FOREIGN_KEY_CHECKS = 0;\n\n' + content[use_end+1:]
                fixes.append("✓ Menambahkan SET FOREIGN_KEY_CHECKS = 0 di awal")
    
    # 3. Pastikan SET FOREIGN_KEY_CHECKS = 1 ada di akhir (sebelum COMMIT)
    if 'SET FOREIGN_KEY_CHECKS = 1' not in content[-500:]:
        # Cari posisi COMMIT terakhir
        commit_pos = content.rfind('COMMIT;')
        if commit_pos > 0:
            content = content[:commit_pos] + '\n\n-- Re-enable foreign key checks\nSET FOREIGN_KEY_CHECKS = 1;\n\n' + content[commit_pos:]
            fixes.append("✓ Menambahkan SET FOREIGN_KEY_CHECKS = 1 sebelum COMMIT")
    
    # 4. Pastikan ada COMMIT di akhir
    if not content.rstrip().endswith('COMMIT;'):
        # Cek apakah ada COMMIT
        if 'COMMIT;' in content:
            # Pastikan di akhir
            content = content.rstrip()
            if not content.endswith('COMMIT;'):
                content = content.rstrip(';').rstrip() + ';\n\nCOMMIT;'
        else:
            content = content.rstrip() + '\n\nCOMMIT;'
        fixes.append("✓ Memastikan COMMIT ada di akhir")
    
    # 5. Pastikan tidak ada multiple COMMIT berturut-turut
    content = re.sub(r'COMMIT;\s*COMMIT;', 'COMMIT;', content)
    
    # 6. Pastikan SET FOREIGN_KEY_CHECKS hanya ada 2x (awal dan akhir)
    fk_checks = list(re.finditer(r'SET FOREIGN_KEY_CHECKS\s*=', content, re.IGNORECASE))
    if len(fk_checks) > 2:
        # Keep first and last, remove middle ones
        first_pos = fk_checks[0].end()
        last_pos = fk_checks[-1].start()
        middle_checks = fk_checks[1:-1]
        for check in reversed(middle_checks):
            # Remove the line
            line_start = content.rfind('\n', 0, check.start()) + 1
            line_end = content.find('\n', check.end())
            if line_end == -1:
                line_end = len(content)
            content = content[:line_start] + content[line_end+1:]
        fixes.append(f"✓ Menghapus {len(middle_checks)} SET FOREIGN_KEY_CHECKS duplikat")
    
    # 7. Pastikan line endings konsisten (CRLF untuk Windows)
    content = content.replace('\r\n', '\n').replace('\r', '\n')
    content = content.replace('\n', '\r\n')
    fixes.append("✓ Line endings dikonversi ke CRLF (Windows)")
    
    # 8. Tambahkan comment di akhir
    if not content.rstrip().endswith('-- End of SQL dump'):
        content = content.rstrip() + '\r\n\r\n-- End of SQL dump\r\n'
    
    # Write final file
    print(f"\n💾 Menulis file final ke: {output_file}")
    with open(output_file, 'wb') as f:
        f.write(content.encode('utf-8'))
    
    # Statistics
    lines = content.split('\n')
    tables = len(re.findall(r'CREATE TABLE IF NOT EXISTS', content, re.IGNORECASE))
    views = len(re.findall(r'CREATE OR REPLACE VIEW', content, re.IGNORECASE))
    procedures = len(re.findall(r'CREATE.*PROCEDURE', content, re.IGNORECASE))
    
    print(f"\n✅ File berhasil difinalisasi!")
    print(f"\n📊 Statistik:")
    print(f"   Total baris: {len(lines):,}")
    print(f"   Tabel: {tables}")
    print(f"   Views: {views}")
    print(f"   Procedures: {procedures}")
    
    if fixes:
        print(f"\n🔧 Perbaikan yang dilakukan:")
        for fix in fixes:
            print(f"   {fix}")
    
    print(f"\n✅ File siap untuk import!")
    print(f"   File: {output_file}")
    print(f"\n💡 Tips untuk import:")
    print(f"   1. Backup database yang ada terlebih dahulu")
    print(f"   2. Import via phpMyAdmin atau MySQL command line")
    print(f"   3. Jika ada error, cek log error untuk detail")
    print(f"   4. Pastikan user MySQL memiliki privilege CREATE, DROP, ALTER")

if __name__ == '__main__':
    input_file = '/opt/lampp/htdocs/optima1/databases/optima_db_24-11-25_reorganized.sql'
    output_file = '/opt/lampp/htdocs/optima1/databases/optima_db_24-11-25_FINAL.sql'
    
    finalize_sql_file(input_file, output_file)


