#!/usr/bin/env python3
"""
Analyze most common hardcoded text patterns
"""

import json
from collections import Counter

def analyze_patterns():
    with open('translation_audit_comprehensive.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    # Collect all hardcoded text
    all_texts = []
    for file, items in data['hardcoded_views'].items():
        for item in items:
            all_texts.append(item['text'])
    
    # Count frequencies
    counter = Counter(all_texts)
    
    print("=" * 80)
    print("TOP 50 MOST COMMON HARDCODED TEXT")
    print("=" * 80)
    print(f"{'Frequency':<12} {'Text'}")
    print("-" * 80)
    
    for text, count in counter.most_common(50):
        print(f"{count:<12} {text}")
    
    print("\n" + "=" * 80)
    print(f"Total unique hardcoded texts: {len(counter)}")
    print(f"Total instances: {sum(counter.values())}")
    print("=" * 80)
    
    # Suggest translations
    print("\n" + "=" * 80)
    print("SUGGESTED TRANSLATION KEYS TO ADD")
    print("=" * 80)
    
    common_translations = {
        'Action': 'action',
        'Edit': 'edit',
        'Delete': 'delete',
        'Save': 'save',
        'Cancel': 'cancel',
        'Submit': 'submit',
        'Add': 'add',
        'Create': 'create',
        'Update': 'update',
        'View': 'view',
        'Details': 'details',
        'Search': 'search',
        'Filter': 'filter',
        'Export': 'export',
        'Import': 'import',
        'Status': 'status',
        'Date': 'date',
        'Name': 'name',
        'Description': 'description',
        'Total': 'total',
        'Price': 'price',
        'Quantity': 'quantity',
        'Type': 'type',
        'Category': 'category',
        'Close': 'close',
        'Back': 'back',
        'Next': 'next',
        'Previous': 'previous',
        'Yes': 'yes',
        'No': 'no',
        'Loading': 'loading',
        'Please wait': 'please_wait',
        'Success': 'success',
        'Error': 'error',
        'Warning': 'warning',
        'Info': 'info',
        'Confirm': 'confirm',
        'Are you sure': 'are_you_sure',
        'Actions': 'actions'
    }
    
    print("\nHigh-priority keys to add (English):\n```php")
    for text, count in counter.most_common(30):
        if text in common_translations:
            key = common_translations[text]
            print(f"'{key}' => '{text}',  // Used {count}x")
    print("```")

if __name__ == '__main__':
    analyze_patterns()
