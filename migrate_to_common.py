"""
Script to migrate App.xxx to Common.xxx for common keys
Automatically updates 5 translated files to use Common.php instead of App.php
"""

import re
from pathlib import Path

# Define common keys that should use Common.php instead of App.php
COMMON_KEYS = [
    'edit', 'save', 'delete', 'cancel', 'close', 'refresh', 'export', 
    'filter', 'status', 'active', 'completed', 'progress', 'all', 
    'actions', 'add', 'search', 'no', 'date', 'name', 'code', 
    'description', 'type', 'category', 'created_at', 'updated_at'
]

# Files to update
FILES = [
    'app/Views/purchasing/purchasing.php',
    'app/Views/warehouse/inventory/invent_unit.php',
    'app/Views/perizinan/silo.php',
    'app/Views/service/work_orders.php',
    'app/Views/service/area_employee_management.php'
]

def migrate_file(file_path):
    """Migrate lang('App.xxx') to lang('Common.xxx') for common keys"""
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        original_content = content
        replacements = 0
        
        # Create pattern for each common key
        for key in COMMON_KEYS:
            # Pattern: lang('App.key') -> lang('Common.key')
            pattern = rf"lang\('App\.{key}'\)"
            replacement = f"lang('Common.{key}')"
            
            count = len(re.findall(pattern, content))
            if count > 0:
                content = re.sub(pattern, replacement, content)
                replacements += count
        
        # Only write if there were changes
        if content != original_content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"✅ {file_path}: {replacements} replacements")
            return replacements
        else:
            print(f"⏭️  {file_path}: No changes needed")
            return 0
            
    except Exception as e:
        print(f"❌ {file_path}: Error - {e}")
        return 0

def main():
    print("🔄 Migrating App.xxx to Common.xxx for common keys...\n")
    
    total_replacements = 0
    
    for file_path in FILES:
        replacements = migrate_file(file_path)
        total_replacements += replacements
    
    print(f"\n✨ Migration Complete!")
    print(f"📊 Total replacements: {total_replacements}")
    print(f"📁 Files processed: {len(FILES)}")
    print("\n💡 Now all common words use Common.php instead of App.php!")

if __name__ == '__main__':
    main()
