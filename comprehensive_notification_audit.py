"""
COMPREHENSIVE NOTIFICATION VARIABLE AUDIT
Check if data sent from controllers actually exists in database and matches expected variables
"""

import json
import re
import os
from pathlib import Path
from collections import defaultdict

# Load previous audit
with open('notification_variables_audit.json', 'r', encoding='utf-8') as f:
    variables_data = json.load(f)

# Database table schemas (add more as needed)
DATABASE_SCHEMAS = {
    'inventory_attachment': [
        'id_inventory_attachment', 'tipe_item', 'po_id', 'id_inventory_unit',
        'attachment_id', 'sn_attachment', 'baterai_id', 'sn_baterai',
        'charger_id', 'sn_charger', 'kondisi_fisik', 'kelengkapan',
        'catatan_fisik', 'lokasi_penyimpanan', 'status_unit',
        'attachment_status', 'tanggal_masuk', 'catatan_inventory',
        'created_at', 'updated_at', 'status_attachment_id'
    ],
    'inventory_unit': [
        'id', 'no_unit', 'unit_type', 'model', 'serial_number',
        'year', 'kondisi', 'lokasi', 'status'
    ],
    'attachment': [
        'id', 'merk', 'model', 'type', 'description'
    ],
    'baterai': [
        'id', 'merk_baterai', 'jenis_baterai', 'type_baterai', 'voltage'
    ],
    'charger': [
        'id', 'merk_charger', 'jenis_charger', 'type_charger', 'voltage'
    ],
    'users': [
        'id', 'username', 'email', 'first_name', 'last_name', 'role', 'division'
    ]
}

# Variable naming standards
VARIABLE_STANDARDS = {
    'unit_number': ['no_unit'],  # Standard name
    'unit_number_aliases': ['unit_code', 'unit_no', 'unit_number', 'old_unit', 'new_unit'],  # Wrong names
    
    'user_name': ['username'],  # Standard
    'user_name_aliases': ['user_name', 'nama_user'],  # Wrong
    
    'performed_action': ['performed_by', 'performed_at'],  # Standard for actions
    'performed_action_aliases': ['created_by', 'updated_by', 'deleted_by', 'swapped_by', 'assigned_by'],  # Context-specific
    
    'quantity': ['quantity'],  # Standard
    'quantity_aliases': ['qty', 'jumlah'],  # Wrong
    
    'ids': ['attachment_id', 'spk_id', 'po_id', 'wo_id', 'user_id', 'unit_id'],  # Specific IDs
    'ids_wrong': ['id'],  # Too generic!
}

def analyze_controller_call(trigger_event):
    """Analyze what data controller actually sends"""
    function_name = f"notify_{trigger_event}"
    
    # Search in controllers
    for root, dirs, files in os.walk('app/Controllers'):
        for file in files:
            if file.endswith('.php'):
                filepath = os.path.join(root, file)
                try:
                    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                        content = f.read()
                        if function_name in content:
                            # Extract the data array
                            pattern = rf"{function_name}\s*\(\s*\[(.*?)\]\s*\)"
                            matches = re.finditer(pattern, content, re.DOTALL)
                            
                            for match in matches:
                                array_content = match.group(1)
                                
                                # Extract each key => value pair
                                data_sent = {}
                                pair_pattern = r"'([^']+)'\s*=>\s*([^,\]]+)"
                                for pair_match in re.finditer(pair_pattern, array_content):
                                    key = pair_match.group(1)
                                    value_expr = pair_match.group(2).strip()
                                    data_sent[key] = value_expr
                                
                                return {
                                    'file': filepath.replace('\\', '/'),
                                    'data_sent': data_sent
                                }
                except Exception as e:
                    pass
    
    return None

def check_data_source_validity(key, value_expr):
    """Check if data source actually exists in database"""
    issues = []
    
    # Check for direct array access like $data['merk']
    array_access_pattern = r"\$\w+\['([^']+)'\]"
    fields_accessed = re.findall(array_access_pattern, value_expr)
    
    for field in fields_accessed:
        # Check if field exists in any known table
        found = False
        for table, columns in DATABASE_SCHEMAS.items():
            if field in columns:
                found = True
                break
        
        if not found and field not in ['merk', 'model']:  # Known missing fields
            issues.append(f"Field '{field}' accessed but not found in database schemas")
    
    # Check for concatenation that might produce empty strings
    if '??' in value_expr and "''" in value_expr:
        issues.append(f"Might produce empty string due to null coalescing to ''")
    
    return issues

def generate_comprehensive_report():
    """Generate full audit report with data validity checks"""
    
    report = {
        'summary': {
            'total_events': len(variables_data),
            'fully_working': 0,
            'missing_data': 0,
            'wrong_variable_names': 0,
            'not_implemented': 0
        },
        'events': [],
        'standardization_issues': defaultdict(list),
        'critical_data_issues': []
    }
    
    for trigger_event, event_info in sorted(variables_data.items()):
        expected_vars = event_info['variables']
        
        # Analyze controller implementation
        impl = analyze_controller_call(trigger_event)
        
        event_report = {
            'trigger_event': trigger_event,
            'function': event_info['function'],
            'expected_variables': expected_vars,
            'implementation': impl
        }
        
        if not impl:
            event_report['status'] = 'NOT_IMPLEMENTED'
            event_report['severity'] = 'HIGH'
            report['summary']['not_implemented'] += 1
        else:
            # Check what's actually sent
            data_sent = impl['data_sent']
            sent_keys = list(data_sent.keys())
            
            missing = set(expected_vars) - set(sent_keys)
            extra = set(sent_keys) - set(expected_vars)
            
            # Check data validity
            data_issues = []
            for key, value_expr in data_sent.items():
                issues = check_data_source_validity(key, value_expr)
                if issues:
                    data_issues.extend([f"{key}: {issue}" for issue in issues])
            
            event_report['keys_sent'] = sent_keys
            event_report['missing_variables'] = list(missing)
            event_report['extra_variables'] = list(extra)
            event_report['data_validity_issues'] = data_issues
            
            # Determine status
            if missing or data_issues:
                if data_issues:
                    event_report['status'] = 'DATA_ISSUES'
                    event_report['severity'] = 'CRITICAL'
                    report['summary']['missing_data'] += 1
                    
                    # Add to critical issues
                    report['critical_data_issues'].append({
                        'event': trigger_event,
                        'issues': data_issues
                    })
                else:
                    event_report['status'] = 'INCOMPLETE'
                    event_report['severity'] = 'MEDIUM'
                    report['summary']['wrong_variable_names'] += 1
            else:
                event_report['status'] = 'WORKING'
                event_report['severity'] = 'LOW'
                report['summary']['fully_working'] += 1
            
            # Check for naming standard violations
            for key in sent_keys:
                # Check if using wrong unit number naming
                if key in VARIABLE_STANDARDS['unit_number_aliases']:
                    report['standardization_issues']['unit_number'].append({
                        'event': trigger_event,
                        'wrong_name': key,
                        'should_be': 'no_unit'
                    })
                
                # Check if using 'id' instead of specific ID
                if key == 'id':
                    report['standardization_issues']['generic_id'].append({
                        'event': trigger_event,
                        'should_be_specific': f"{trigger_event}_id or attachment_id, etc"
                    })
                
                # Check quantity naming
                if key in VARIABLE_STANDARDS['quantity_aliases']:
                    report['standardization_issues']['quantity'].append({
                        'event': trigger_event,
                        'wrong_name': key,
                        'should_be': 'quantity'
                    })
        
        report['events'].append(event_report)
    
    return report

def print_executive_summary(report):
    """Print executive summary"""
    print("=" * 80)
    print("COMPREHENSIVE NOTIFICATION AUDIT - EXECUTIVE SUMMARY")
    print("=" * 80)
    
    summary = report['summary']
    total = summary['total_events']
    
    print(f"\n📊 OVERALL HEALTH:")
    print(f"  Total Events: {total}")
    print(f"  ✅ Fully Working: {summary['fully_working']} ({summary['fully_working']/total*100:.1f}%)")
    print(f"  🔴 Data Issues (CRITICAL): {summary['missing_data']} ({summary['missing_data']/total*100:.1f}%)")
    print(f"  ⚠️  Wrong Names: {summary['wrong_variable_names']} ({summary['wrong_variable_names']/total*100:.1f}%)")
    print(f"  ❌ Not Implemented: {summary['not_implemented']} ({summary['not_implemented']/total*100:.1f}%)")
    
    print(f"\n🚨 CRITICAL DATA ISSUES ({len(report['critical_data_issues'])}):")
    for issue in report['critical_data_issues'][:10]:
        print(f"\n  Event: {issue['event']}")
        for i in issue['issues']:
            print(f"    - {i}")
    
    if len(report['critical_data_issues']) > 10:
        print(f"\n  ... and {len(report['critical_data_issues']) - 10} more issues")
    
    print(f"\n📋 STANDARDIZATION ISSUES:")
    for category, issues in report['standardization_issues'].items():
        if issues:
            print(f"\n  {category.upper()}: {len(issues)} cases")
            for issue in issues[:3]:
                print(f"    - {issue['event']}: {issue.get('wrong_name', 'generic')} → should be {issue.get('should_be', 'specific')}")
            if len(issues) > 3:
                print(f"    ... and {len(issues) - 3} more")

if __name__ == "__main__":
    print("Starting comprehensive audit...")
    report = generate_comprehensive_report()
    
    # Save report
    with open('comprehensive_notification_report.json', 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2, ensure_ascii=False)
    
    print_executive_summary(report)
    
    print("\n" + "=" * 80)
    print("✅ Detailed report saved to: comprehensive_notification_report.json")
    print("=" * 80)
