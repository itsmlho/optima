#!/usr/bin/env python3
"""
Comprehensive Translation Audit Script
Finds all hardcoded text and missing translation keys
"""

import os
import re
import json
from pathlib import Path
from collections import defaultdict

# Directories to scan
VIEW_DIRS = ['app/Views']
CONTROLLER_DIRS = ['app/Controllers']
LANG_DIRS = ['app/Language']

# Translation files
EN_LANG_FILE = 'app/Language/en/App.php'
ID_LANG_FILE = 'app/Language/id/App.php'

def extract_php_array_keys(file_path):
    """Extract all keys from PHP language array"""
    keys = set()
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            # Pattern: 'key' => 'value',
            pattern = r"'([^']+)'\s*=>\s*'([^']+)'"
            matches = re.findall(pattern, content)
            for key, value in matches:
                keys.add(key)
    except Exception as e:
        print(f"Error reading {file_path}: {e}")
    return keys

def find_lang_usage(file_path):
    """Find all lang() usage in a file"""
    lang_keys = []
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            # Pattern: lang('App.key') or lang("App.key")
            pattern = r"lang\(['\"]App\.([^'\"]+)['\"]\)"
            matches = re.findall(pattern, content)
            lang_keys.extend(matches)
    except Exception as e:
        print(f"Error reading {file_path}: {e}")
    return lang_keys

def find_hardcoded_text_in_views(file_path):
    """Find potential hardcoded text in view files"""
    hardcoded = []
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            lines = f.readlines()
            for line_num, line in enumerate(lines, 1):
                # Skip comments
                if re.match(r'^\s*//', line) or re.match(r'^\s*\*', line):
                    continue
                
                # Find text within HTML tags that's not using lang()
                # Pattern 1: >Text</tag>
                pattern1 = r'>([A-Z][A-Za-z\s]{3,})</'
                matches1 = re.findall(pattern1, line)
                for match in matches1:
                    if 'lang(' not in line and match.strip() and len(match.strip()) > 3:
                        hardcoded.append({
                            'line': line_num,
                            'text': match.strip(),
                            'context': line.strip()[:100]
                        })
                
                # Pattern 2: <button>Text</button> or similar
                pattern2 = r'<(?:button|a|span|h\d|p|td|th|label|option)[^>]*>([A-Z][A-Za-z\s&;]{3,})</(?:button|a|span|h\d|p|td|th|label|option)>'
                matches2 = re.findall(pattern2, line)
                for match in matches2:
                    clean = re.sub(r'<[^>]+>', '', match).strip()
                    if 'lang(' not in line and clean and len(clean) > 3 and not match.startswith('<?'):
                        hardcoded.append({
                            'line': line_num,
                            'text': clean,
                            'context': line.strip()[:100]
                        })
                
                # Pattern 3: placeholder="Text"
                pattern3 = r'placeholder=["\']([A-Z][A-Za-z\s]{3,})["\']'
                matches3 = re.findall(pattern3, line)
                for match in matches3:
                    if 'lang(' not in line:
                        hardcoded.append({
                            'line': line_num,
                            'text': match.strip(),
                            'context': line.strip()[:100],
                            'type': 'placeholder'
                        })
                
                # Pattern 4: title="Text"
                pattern4 = r'title=["\']([A-Z][A-Za-z\s]{3,})["\']'
                matches4 = re.findall(pattern4, line)
                for match in matches4:
                    if 'lang(' not in line:
                        hardcoded.append({
                            'line': line_num,
                            'text': match.strip(),
                            'context': line.strip()[:100],
                            'type': 'title'
                        })
    except Exception as e:
        print(f"Error reading {file_path}: {e}")
    return hardcoded

def find_hardcoded_in_controllers(file_path):
    """Find hardcoded messages in controllers"""
    hardcoded = []
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            lines = f.readlines()
            for line_num, line in enumerate(lines, 1):
                # Look for return json with 'message' or 'error'
                if "'message'" in line or '"message"' in line or "'error'" in line or '"error"' in line:
                    if 'lang(' not in line:
                        # Extract the message
                        pattern = r"['\"](?:message|error)['\"]\s*=>\s*['\"]([^'\"]+)['\"]"
                        matches = re.findall(pattern, line)
                        for match in matches:
                            if len(match) > 5 and not match.startswith('$'):
                                hardcoded.append({
                                    'line': line_num,
                                    'text': match,
                                    'context': line.strip()[:100],
                                    'type': 'controller_message'
                                })
    except Exception as e:
        print(f"Error reading {file_path}: {e}")
    return hardcoded

def scan_directory(directory, scan_func):
    """Recursively scan directory"""
    results = {}
    base_path = Path(directory)
    if not base_path.exists():
        return results
    
    for file_path in base_path.rglob('*.php'):
        rel_path = str(file_path).replace('\\', '/')
        result = scan_func(str(file_path))
        if result:
            results[rel_path] = result
    
    return results

def main():
    print("=" * 80)
    print("COMPREHENSIVE TRANSLATION AUDIT")
    print("=" * 80)
    
    report = {
        'summary': {},
        'missing_keys': {},
        'hardcoded_views': {},
        'hardcoded_controllers': {},
        'unused_keys': {},
        'lang_usage': {},
        'key_comparison': {}
    }
    
    # 1. Load translation keys
    print("\n[1] Loading translation keys...")
    en_keys = extract_php_array_keys(EN_LANG_FILE)
    id_keys = extract_php_array_keys(ID_LANG_FILE)
    
    print(f"   - English keys: {len(en_keys)}")
    print(f"   - Indonesian keys: {len(id_keys)}")
    
    # Find keys only in one language
    only_en = en_keys - id_keys
    only_id = id_keys - en_keys
    
    if only_en:
        report['key_comparison']['only_in_english'] = sorted(list(only_en))
        print(f"   ⚠ Keys only in English: {len(only_en)}")
    
    if only_id:
        report['key_comparison']['only_in_indonesian'] = sorted(list(only_id))
        print(f"   ⚠ Keys only in Indonesian: {len(only_id)}")
    
    # 2. Find all lang() usage
    print("\n[2] Scanning lang() usage in views...")
    all_used_keys = set()
    view_lang_usage = {}
    
    for view_dir in VIEW_DIRS:
        if os.path.exists(view_dir):
            for root, dirs, files in os.walk(view_dir):
                for file in files:
                    if file.endswith('.php'):
                        file_path = os.path.join(root, file)
                        keys = find_lang_usage(file_path)
                        if keys:
                            rel_path = file_path.replace('\\', '/')
                            view_lang_usage[rel_path] = keys
                            all_used_keys.update(keys)
    
    print(f"   - Total unique keys used: {len(all_used_keys)}")
    report['lang_usage'] = view_lang_usage
    
    # 3. Find missing keys
    print("\n[3] Finding missing translation keys...")
    missing_en = all_used_keys - en_keys
    missing_id = all_used_keys - id_keys
    
    if missing_en:
        report['missing_keys']['missing_in_english'] = sorted(list(missing_en))
        print(f"   ⚠ Missing in English: {len(missing_en)}")
        for key in sorted(list(missing_en))[:10]:
            print(f"      - {key}")
        if len(missing_en) > 10:
            print(f"      ... and {len(missing_en) - 10} more")
    
    if missing_id:
        report['missing_keys']['missing_in_indonesian'] = sorted(list(missing_id))
        print(f"   ⚠ Missing in Indonesian: {len(missing_id)}")
        for key in sorted(list(missing_id))[:10]:
            print(f"      - {key}")
        if len(missing_id) > 10:
            print(f"      ... and {len(missing_id) - 10} more")
    
    # 4. Find unused keys
    print("\n[4] Finding unused translation keys...")
    common_keys = en_keys & id_keys
    unused = common_keys - all_used_keys
    if unused:
        report['unused_keys']['unused'] = sorted(list(unused))
        print(f"   ℹ Unused keys: {len(unused)} (might be used in controllers)")
    
    # 5. Find hardcoded text in views
    print("\n[5] Scanning for hardcoded text in views...")
    hardcoded_views = {}
    total_hardcoded = 0
    
    for view_dir in VIEW_DIRS:
        results = scan_directory(view_dir, find_hardcoded_text_in_views)
        hardcoded_views.update(results)
        for file, items in results.items():
            total_hardcoded += len(items)
    
    report['hardcoded_views'] = hardcoded_views
    print(f"   ⚠ Files with hardcoded text: {len(hardcoded_views)}")
    print(f"   ⚠ Total hardcoded instances: {total_hardcoded}")
    
    # Show top offenders
    if hardcoded_views:
        sorted_files = sorted(hardcoded_views.items(), key=lambda x: len(x[1]), reverse=True)
        print("\n   Top 10 files with most hardcoded text:")
        for file, items in sorted_files[:10]:
            print(f"      - {file}: {len(items)} instances")
    
    # 6. Find hardcoded in controllers
    print("\n[6] Scanning for hardcoded messages in controllers...")
    hardcoded_controllers = {}
    total_controller_hardcoded = 0
    
    for controller_dir in CONTROLLER_DIRS:
        results = scan_directory(controller_dir, find_hardcoded_in_controllers)
        hardcoded_controllers.update(results)
        for file, items in results.items():
            total_controller_hardcoded += len(items)
    
    report['hardcoded_controllers'] = hardcoded_controllers
    print(f"   ⚠ Controllers with hardcoded messages: {len(hardcoded_controllers)}")
    print(f"   ⚠ Total hardcoded instances: {total_controller_hardcoded}")
    
    # Summary
    report['summary'] = {
        'total_en_keys': len(en_keys),
        'total_id_keys': len(id_keys),
        'total_used_keys': len(all_used_keys),
        'missing_in_english': len(missing_en),
        'missing_in_indonesian': len(missing_id),
        'only_in_english': len(only_en),
        'only_in_indonesian': len(only_id),
        'unused_keys': len(unused),
        'hardcoded_in_views': total_hardcoded,
        'hardcoded_in_controllers': total_controller_hardcoded,
        'files_with_hardcoded_views': len(hardcoded_views),
        'files_with_hardcoded_controllers': len(hardcoded_controllers)
    }
    
    # Save report
    output_file = 'translation_audit_comprehensive.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2, ensure_ascii=False)
    
    print("\n" + "=" * 80)
    print("AUDIT SUMMARY")
    print("=" * 80)
    print(f"Translation Keys (EN): {len(en_keys)}")
    print(f"Translation Keys (ID): {len(id_keys)}")
    print(f"Keys Used in Code: {len(all_used_keys)}")
    print(f"Missing in English: {len(missing_en)}")
    print(f"Missing in Indonesian: {len(missing_id)}")
    print(f"Inconsistent Keys (only EN): {len(only_en)}")
    print(f"Inconsistent Keys (only ID): {len(only_id)}")
    print(f"Unused Keys: {len(unused)}")
    print(f"Hardcoded Text in Views: {total_hardcoded} instances in {len(hardcoded_views)} files")
    print(f"Hardcoded Messages in Controllers: {total_controller_hardcoded} instances")
    print("\n" + "=" * 80)
    print(f"Full report saved to: {output_file}")
    print("=" * 80)
    
    return report

if __name__ == '__main__':
    main()
