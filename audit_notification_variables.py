#!/usr/bin/env python3
"""
Audit Notification Variables
Extracts all trigger events and their available variables from notification_helper.php
"""

import re
import json

def extract_notification_functions(file_path):
    """Extract all notify_* functions and their variables"""
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Pattern to match notify functions
    pattern = r"function (notify_[a-z_]+)\([^\)]*\)\s*\{[^}]*?send_notification\('([^']+)',\s*\[(.*?)\]\s*\);"
    
    matches = re.findall(pattern, content, re.DOTALL)
    
    results = {}
    
    for func_name, trigger_event, params in matches:
        # Extract variables from the array
        var_pattern = r"'([^']+)'\s*=>"
        variables = re.findall(var_pattern, params)
        
        if trigger_event not in results:
            results[trigger_event] = {
                'function': func_name,
                'variables': []
            }
        
        # Merge variables (remove duplicates)
        for var in variables:
            if var not in results[trigger_event]['variables']:
                results[trigger_event]['variables'].append(var)
    
    return results

def main():
    file_path = r'c:\laragon\www\optima\app\Helpers\notification_helper.php'
    
    print("🔍 Auditing Notification Variables...")
    print("=" * 80)
    
    results = extract_notification_functions(file_path)
    
    # Sort by trigger_event
    sorted_results = dict(sorted(results.items()))
    
    print(f"\n✅ Found {len(sorted_results)} trigger events\n")
    
    # Print results
    for trigger_event, data in sorted_results.items():
        print(f"📌 {trigger_event}")
        print(f"   Function: {data['function']}()")
        print(f"   Variables ({len(data['variables'])}):")
        for var in data['variables']:
            print(f"      • {{{{${var}}}}}")
        print()
    
    # Save to JSON
    output_file = r'c:\laragon\www\optima\notification_variables_audit.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(sorted_results, f, indent=2)
    
    print(f"💾 Saved to: {output_file}")
    
    # Generate SQL for updating notification_rules with description
    print("\n" + "=" * 80)
    print("📝 SQL to add variable info to notification_rules:")
    print("=" * 80 + "\n")
    
    for trigger_event, data in sorted_results.items():
        vars_list = ', '.join([f'{{{{{var}}}}}' for var in data['variables']])
        description = f"Available variables: {vars_list}"
        
        sql = f"UPDATE notification_rules SET rule_description = '{description}' WHERE trigger_event = '{trigger_event}';"
        print(sql)

if __name__ == '__main__':
    main()
