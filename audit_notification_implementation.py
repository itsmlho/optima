"""
Deep Audit: Notification Variables Implementation
Checks if all variables used in notification templates actually exist in the calling code
"""

import json
import re
import os
from pathlib import Path

# Load the variables audit
with open('notification_variables_audit.json', 'r', encoding='utf-8') as f:
    variables_data = json.load(f)

# Paths to search
search_paths = [
    'app/Controllers',
    'app/Models',
    'app/Helpers'
]

def find_notification_calls(trigger_event):
    """Find where notify_{trigger_event} is called"""
    function_name = f"notify_{trigger_event}"
    calls = []
    
    for path in search_paths:
        if not os.path.exists(path):
            continue
        for root, dirs, files in os.walk(path):
            for file in files:
                if file.endswith('.php'):
                    filepath = os.path.join(root, file)
                    try:
                        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                            content = f.read()
                            if function_name in content:
                                # Find the call with array data
                                pattern = rf"{function_name}\s*\(\s*\[(.*?)\]\s*\)"
                                matches = re.finditer(pattern, content, re.DOTALL)
                                for match in matches:
                                    array_content = match.group(1)
                                    # Extract keys from array
                                    keys = re.findall(r"'([^']+)'\s*=>", array_content)
                                    calls.append({
                                        'file': filepath,
                                        'keys_sent': keys
                                    })
                    except Exception as e:
                        print(f"Error reading {filepath}: {e}")
    
    return calls

def audit_notification_implementation():
    """Main audit function"""
    report = {
        'total_events': len(variables_data),
        'events': []
    }
    
    for trigger_event, event_info in sorted(variables_data.items()):
        expected_vars = event_info['variables']
        
        # Find where this notification is called
        calls = find_notification_calls(trigger_event)
        
        event_audit = {
            'trigger_event': trigger_event,
            'function': event_info['function'],
            'expected_variables': expected_vars,
            'calls_found': len(calls),
            'implementations': []
        }
        
        if not calls:
            event_audit['status'] = 'NOT_IMPLEMENTED'
            event_audit['issue'] = 'Function not called anywhere'
        else:
            all_keys_sent = set()
            for call in calls:
                all_keys_sent.update(call['keys_sent'])
                
                # Check which variables are missing
                missing_vars = set(expected_vars) - set(call['keys_sent'])
                extra_vars = set(call['keys_sent']) - set(expected_vars)
                
                impl = {
                    'file': call['file'].replace('\\', '/'),
                    'keys_sent': call['keys_sent'],
                    'missing_variables': list(missing_vars),
                    'extra_variables': list(extra_vars),
                    'status': 'COMPLETE' if not missing_vars else 'INCOMPLETE'
                }
                event_audit['implementations'].append(impl)
            
            # Overall status
            missing_in_all = set(expected_vars) - all_keys_sent
            if missing_in_all:
                event_audit['status'] = 'INCOMPLETE'
                event_audit['missing_globally'] = list(missing_in_all)
            else:
                event_audit['status'] = 'COMPLETE'
        
        report['events'].append(event_audit)
    
    return report

def generate_summary(report):
    """Generate summary statistics"""
    complete = 0
    incomplete = 0
    not_implemented = 0
    
    variable_inconsistencies = []
    
    for event in report['events']:
        if event['status'] == 'COMPLETE':
            complete += 1
        elif event['status'] == 'INCOMPLETE':
            incomplete += 1
        elif event['status'] == 'NOT_IMPLEMENTED':
            not_implemented += 1
            
        # Check for variable naming inconsistencies
        if event.get('implementations'):
            for impl in event['implementations']:
                if impl['missing_variables']:
                    variable_inconsistencies.append({
                        'event': event['trigger_event'],
                        'file': impl['file'],
                        'missing': impl['missing_variables']
                    })
    
    summary = {
        'total_events': report['total_events'],
        'complete': complete,
        'incomplete': incomplete,
        'not_implemented': not_implemented,
        'completion_rate': f"{(complete/report['total_events']*100):.1f}%",
        'variable_inconsistencies_count': len(variable_inconsistencies),
        'variable_inconsistencies': variable_inconsistencies
    }
    
    return summary

def check_variable_naming_patterns():
    """Check for common variables that should be standardized"""
    common_vars = {}
    
    for trigger_event, event_info in variables_data.items():
        for var in event_info['variables']:
            # Group similar variable names
            base_name = var.lower()
            if 'unit' in base_name or 'no_unit' in base_name:
                key = 'unit_number'
            elif 'user' in base_name and 'name' in base_name:
                key = 'user_name'
            elif 'date' in base_name:
                key = 'dates'
            elif 'url' in base_name:
                key = 'url'
            elif 'id' in base_name:
                key = 'ids'
            else:
                continue
            
            if key not in common_vars:
                common_vars[key] = {}
            if var not in common_vars[key]:
                common_vars[key][var] = []
            common_vars[key][var].append(trigger_event)
    
    return common_vars

if __name__ == "__main__":
    print("=" * 80)
    print("NOTIFICATION IMPLEMENTATION AUDIT")
    print("=" * 80)
    
    # Run audit
    report = audit_notification_implementation()
    summary = generate_summary(report)
    
    # Print summary
    print(f"\n📊 SUMMARY:")
    print(f"  Total Events: {summary['total_events']}")
    print(f"  ✅ Complete: {summary['complete']} ({summary['completion_rate']})")
    print(f"  ⚠️  Incomplete: {summary['incomplete']}")
    print(f"  ❌ Not Implemented: {summary['not_implemented']}")
    print(f"  🔧 Variable Inconsistencies: {summary['variable_inconsistencies_count']}")
    
    # Save detailed report
    with open('notification_implementation_audit.json', 'w', encoding='utf-8') as f:
        json.dump(report, f, indent=2, ensure_ascii=False)
    
    print(f"\n✅ Detailed report saved to: notification_implementation_audit.json")
    
    # Check variable naming patterns
    print(f"\n🔍 Checking variable naming patterns...")
    patterns = check_variable_naming_patterns()
    
    print(f"\n📋 VARIABLE STANDARDIZATION OPPORTUNITIES:")
    for category, vars_dict in patterns.items():
        if len(vars_dict) > 1:
            print(f"\n  {category.upper()}:")
            for var_name, events in vars_dict.items():
                print(f"    - {var_name}: used in {len(events)} events")
    
    # Print specific issues
    if summary['variable_inconsistencies']:
        print(f"\n❗ CRITICAL ISSUES (First 10):")
        for i, issue in enumerate(summary['variable_inconsistencies'][:10], 1):
            print(f"\n  {i}. Event: {issue['event']}")
            print(f"     File: {issue['file']}")
            print(f"     Missing Variables: {', '.join(issue['missing'])}")
    
    print(f"\n" + "=" * 80)
    print("AUDIT COMPLETE")
    print("=" * 80)
