#!/usr/bin/env python3
"""
Generate actionable translation fix report
"""

import json
from collections import defaultdict

def generate_markdown_report():
    with open('translation_audit_comprehensive.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    report = []
    report.append("# COMPREHENSIVE TRANSLATION AUDIT REPORT\n")
    report.append(f"Generated: December 23, 2025\n\n")
    
    # Executive Summary
    report.append("## 🚨 EXECUTIVE SUMMARY\n")
    summary = data['summary']
    report.append(f"- **Total Hardcoded Text**: {summary['hardcoded_in_views']:,} instances in views\n")
    report.append(f"- **Total Hardcoded Messages**: {summary['hardcoded_in_controllers']:,} instances in controllers\n")
    report.append(f"- **Missing Translation Keys**: {summary['missing_in_english']} (EN), {summary['missing_in_indonesian']} (ID)\n")
    report.append(f"- **Inconsistent Keys**: {summary['only_in_english']} only in EN, {summary['only_in_indonesian']} only in ID\n\n")
    
    # Priority Actions
    report.append("## 🎯 PRIORITY ACTIONS\n\n")
    report.append("### 1. Add Missing Translation Keys (HIGH PRIORITY)\n\n")
    
    if 'missing_in_english' in data['missing_keys']:
        report.append("**Missing in English (app/Language/en/App.php):**\n```php\n")
        for key in data['missing_keys']['missing_in_english']:
            suggested = key.replace('_', ' ').title()
            report.append(f"'{key}' => '{suggested}',\n")
        report.append("```\n\n")
    
    if 'missing_in_indonesian' in data['missing_keys']:
        report.append("**Missing in Indonesian (app/Language/id/App.php):**\n```php\n")
        translations = {
            'and': 'dan',
            'or': 'atau',
            'data': 'data',
            'delivered': 'Terkirim',
            'department': 'Departemen',
            'in_progress': 'Sedang Proses',
            'privacy_policy': 'Kebijakan Privasi',
            'report': 'Laporan',
            'terms_conditions': 'Syarat & Ketentuan',
            'area': 'Area',
            'pic': 'PIC'
        }
        for key in data['missing_keys']['missing_in_indonesian']:
            translation = translations.get(key, key.replace('_', ' ').title())
            report.append(f"'{key}' => '{translation}',\n")
        report.append("```\n\n")
    
    # Top 20 Files
    report.append("### 2. Top 20 Files Needing Translation (URGENT)\n\n")
    if data['hardcoded_views']:
        sorted_files = sorted(data['hardcoded_views'].items(), key=lambda x: len(x[1]), reverse=True)
        report.append("| # | File | Instances | Action |\n")
        report.append("|---|------|-----------|--------|\n")
        for idx, (file, items) in enumerate(sorted_files[:20], 1):
            file_short = file.replace('app/Views/', '')
            report.append(f"| {idx} | `{file_short}` | {len(items)} | 🔴 Critical |\n")
        report.append("\n")
    
    # Sample Hardcoded Text
    report.append("### 3. Sample Hardcoded Text Found\n\n")
    if data['hardcoded_views']:
        report.append("#### From Views (showing first 30 unique examples):\n\n")
        all_texts = set()
        for file, items in data['hardcoded_views'].items():
            for item in items:
                all_texts.add(item['text'])
                if len(all_texts) >= 30:
                    break
            if len(all_texts) >= 30:
                break
        
        for idx, text in enumerate(sorted(list(all_texts))[:30], 1):
            report.append(f"{idx}. \"{text}\"\n")
        report.append("\n")
    
    # Controllers
    report.append("### 4. Controllers with Hardcoded Messages\n\n")
    if data['hardcoded_controllers']:
        sorted_controllers = sorted(data['hardcoded_controllers'].items(), key=lambda x: len(x[1]), reverse=True)
        report.append("| # | Controller | Messages | Priority |\n")
        report.append("|---|------------|----------|----------|\n")
        for idx, (file, items) in enumerate(sorted_controllers[:15], 1):
            file_short = file.replace('app/Controllers/', '')
            priority = "🔴 High" if len(items) > 50 else "🟡 Medium"
            report.append(f"| {idx} | `{file_short}` | {len(items)} | {priority} |\n")
        report.append("\n")
    
    # Key Inconsistencies
    if data['key_comparison']:
        report.append("### 5. Fix Key Inconsistencies\n\n")
        if 'only_in_english' in data['key_comparison']:
            report.append(f"**Keys only in English ({len(data['key_comparison']['only_in_english'])}):**\n")
            for key in data['key_comparison']['only_in_english']:
                report.append(f"- `{key}` - Add to Indonesian file\n")
            report.append("\n")
    
    # Recommendations
    report.append("## 💡 RECOMMENDATIONS\n\n")
    report.append("### Phase 1: Quick Wins (1-2 days)\n")
    report.append("1. ✅ Add all missing translation keys to both language files\n")
    report.append("2. ✅ Fix key inconsistencies between EN and ID\n")
    report.append("3. 🔧 Update top 5 most critical view files\n\n")
    
    report.append("### Phase 2: High-Traffic Pages (3-5 days)\n")
    report.append("1. Update purchasing management pages\n")
    report.append("2. Update warehouse inventory pages\n")
    report.append("3. Update SILO/permit pages\n")
    report.append("4. Update work order pages\n\n")
    
    report.append("### Phase 3: Controllers (5-7 days)\n")
    report.append("1. Replace hardcoded messages with lang() in top 10 controllers\n")
    report.append("2. Standardize error/success message format\n\n")
    
    report.append("### Phase 4: Remaining Files (ongoing)\n")
    report.append("1. Gradually update remaining 100+ view files\n")
    report.append("2. Create translation helper functions for common patterns\n\n")
    
    # Statistics
    report.append("## 📊 DETAILED STATISTICS\n\n")
    report.append(f"- Total Translation Keys (EN): {summary['total_en_keys']}\n")
    report.append(f"- Total Translation Keys (ID): {summary['total_id_keys']}\n")
    report.append(f"- Keys Actually Used: {summary['total_used_keys']}\n")
    report.append(f"- Unused Keys: {summary['unused_keys']} (169 keys not found in views)\n")
    report.append(f"- Files with Hardcoded Text: {summary['files_with_hardcoded_views']}\n")
    report.append(f"- Controllers with Hardcoded Messages: {summary['files_with_hardcoded_controllers']}\n\n")
    
    # Next Steps
    report.append("## 🚀 IMMEDIATE NEXT STEPS\n\n")
    report.append("1. **Add missing keys** to both language files (15 minutes)\n")
    report.append("2. **Test language switching** after adding keys\n")
    report.append("3. **Create translation helper script** to assist with bulk replacement\n")
    report.append("4. **Start with purchasing.php** (190 hardcoded instances)\n")
    report.append("5. **Set up systematic approach** to tackle remaining files\n\n")
    
    return '\n'.join(report)

if __name__ == '__main__':
    report = generate_markdown_report()
    
    with open('TRANSLATION_AUDIT_REPORT.md', 'w', encoding='utf-8') as f:
        f.write(report)
    
    print("Report generated: TRANSLATION_AUDIT_REPORT.md")
    print("\nShowing preview...\n")
    print(report[:2000] + "\n\n[... report continues ...]")
