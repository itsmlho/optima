#!/usr/bin/env python3
"""
Deep Variable Analysis - Compare notification templates with actual implementation
Identifies variables called in templates but not sent from controller
"""

import re
import json
from collections import defaultdict

# Read notification helper functions
with open('app/Helpers/notification_helper.php', 'r', encoding='utf-8') as f:
    helper_content = f.read()

# Read database templates
with open('notification_templates_db.txt', 'r', encoding='utf-8') as f:
    db_templates = f.read()

def extract_template_variables(template):
    """Extract all {{variable}} from template"""
    return set(re.findall(r'\{\{(\w+)\}\}', template))

def parse_notification_function(trigger_event):
    """Parse notification helper function to get sent variables"""
    # Try multiple patterns
    patterns = [
        rf'function notify_{trigger_event}\s*\([^)]+\)\s*\{{(.*?)(?:\n\}}|\nif\s*\(!function_exists)',
        rf'function notify_{trigger_event}\s*\([^)]+\)\s*\{{(.*?)(?=\nfunction|\Z)',
    ]
    
    function_body = None
    for pattern in patterns:
        match = re.search(pattern, helper_content, re.DOTALL)
        if match:
            function_body = match.group(1)
            # Limit to reasonable size (first 2000 chars usually enough)
            function_body = function_body[:2000]
            break
    
    if not function_body:
        return None
    
    sent_vars = set()
    
    # Extract variables from various patterns
    # Pattern 1: $data['key']
    sent_vars.update(re.findall(r'\$\w+\[\'(\w+)\'\]', function_body))
    
    # Pattern 2: 'key' => in arrays
    sent_vars.update(re.findall(r"'(\w+)'\s*=>", function_body))
    
    # Pattern 3: "key" => in arrays
    sent_vars.update(re.findall(r'"(\w+)"\s*=>', function_body))
    
    return sent_vars if sent_vars else None

def analyze_templates():
    """Main analysis function"""
    
    lines = db_templates.strip().split('\n')[1:]  # Skip header
    
    results = {
        'missing_variables': [],
        'extra_variables': [],
        'variable_usage': defaultdict(list),
        'standardization_issues': defaultdict(list)
    }
    
    # Known standard variable groups
    unit_vars = ['no_unit', 'unit_code', 'unit_number', 'unit_no', 'unit_id']
    id_vars = ['id', 'attachment_id', 'spk_id', 'wo_id', 'po_id', 'di_id', 'quotation_id']
    qty_vars = ['qty', 'quantity', 'jumlah']
    
    for line in lines:
        if not line.strip():
            continue
            
        parts = line.split('\t')
        if len(parts) < 4:
            continue
        
        rule_id = parts[0]
        trigger_event = parts[1]
        title_template = parts[2]
        message_template = parts[3]
        
        # Extract all variables used in templates
        title_vars = extract_template_variables(title_template)
        message_vars = extract_template_variables(message_template)
        all_template_vars = title_vars | message_vars
        
        # Get variables sent from helper function
        sent_vars = parse_notification_function(trigger_event)
        
        if sent_vars is None:
            results['missing_variables'].append({
                'event': trigger_event,
                'status': 'FUNCTION NOT IMPLEMENTED',
                'template_vars': list(all_template_vars),
                'missing': list(all_template_vars)
            })
            continue
        
        # Find missing variables (in template but not sent)
        missing = all_template_vars - sent_vars
        # Find extra variables (sent but not used)
        extra = sent_vars - all_template_vars
        
        if missing:
            results['missing_variables'].append({
                'event': trigger_event,
                'rule_id': rule_id,
                'template_vars': sorted(all_template_vars),
                'sent_vars': sorted(sent_vars),
                'missing': sorted(missing),
                'title': title_template,
                'message': message_template
            })
        
        if extra:
            results['extra_variables'].append({
                'event': trigger_event,
                'rule_id': rule_id,
                'extra': sorted(extra)
            })
        
        # Track variable usage for standardization
        for var in all_template_vars:
            results['variable_usage'][var].append(trigger_event)
            
            # Check for standardization issues
            if var in unit_vars and var != 'no_unit':
                results['standardization_issues']['unit_naming'].append({
                    'event': trigger_event,
                    'wrong_name': var,
                    'should_be': 'no_unit'
                })
            
            if var in id_vars and var == 'id':
                results['standardization_issues']['generic_id'].append({
                    'event': trigger_event,
                    'should_be_specific': 'Use specific ID (attachment_id, spk_id, etc)'
                })
            
            if var in qty_vars and var == 'qty':
                results['standardization_issues']['quantity_naming'].append({
                    'event': trigger_event,
                    'wrong_name': 'qty',
                    'should_be': 'quantity'
                })
    
    return results

def generate_report(results):
    """Generate detailed report"""
    
    print("=" * 80)
    print("DEEP VARIABLE ANALYSIS REPORT")
    print("=" * 80)
    print()
    
    # Summary
    print("📊 EXECUTIVE SUMMARY")
    print("-" * 80)
    missing_count = len(results['missing_variables'])
    extra_count = len(results['extra_variables'])
    
    print(f"Total Events with Missing Variables: {missing_count}")
    print(f"Total Events with Unused Variables: {extra_count}")
    print()
    
    # Critical: Missing Variables (templates will show empty)
    print("🔴 CRITICAL: Variables in Template but NOT SENT from Controller")
    print("-" * 80)
    
    if results['missing_variables']:
        for item in results['missing_variables']:
            if item.get('status') == 'FUNCTION NOT IMPLEMENTED':
                print(f"\n❌ {item['event']}: FUNCTION NOT IMPLEMENTED")
                print(f"   Template needs: {', '.join(item['template_vars'])}")
            else:
                print(f"\n⚠️  {item['event']} (ID: {item['rule_id']})")
                print(f"   Title: {item['title']}")
                print(f"   Message: {item['message']}")
                print(f"   Missing: {', '.join(item['missing'])}")
                print(f"   Currently sent: {', '.join(item['sent_vars'])}")
    else:
        print("✅ All template variables are being sent!")
    
    print("\n" + "=" * 80)
    print("📋 STANDARDIZATION ISSUES")
    print("-" * 80)
    
    # Unit naming
    if results['standardization_issues']['unit_naming']:
        print(f"\n🟡 Wrong Unit Variable Names ({len(results['standardization_issues']['unit_naming'])} cases)")
        print("   Should use 'no_unit' consistently:\n")
        for issue in results['standardization_issues']['unit_naming']:
            print(f"   - {issue['event']}: using '{issue['wrong_name']}' → should be '{issue['should_be']}'")
    
    # Generic IDs
    if results['standardization_issues']['generic_id']:
        print(f"\n🟡 Generic 'id' Usage ({len(results['standardization_issues']['generic_id'])} cases)")
        print("   Should use specific IDs (attachment_id, spk_id, etc):\n")
        for issue in results['standardization_issues']['generic_id']:
            print(f"   - {issue['event']}: {issue['should_be_specific']}")
    
    # Quantity naming
    if results['standardization_issues']['quantity_naming']:
        print(f"\n🟡 Wrong Quantity Variable Name ({len(results['standardization_issues']['quantity_naming'])} cases)")
        print("   Should use 'quantity' not 'qty':\n")
        for issue in results['standardization_issues']['quantity_naming']:
            print(f"   - {issue['event']}: using '{issue['wrong_name']}' → should be '{issue['should_be']}'")
    
    print("\n" + "=" * 80)
    print("📈 VARIABLE USAGE STATISTICS")
    print("-" * 80)
    
    # Most used variables
    sorted_vars = sorted(results['variable_usage'].items(), key=lambda x: len(x[1]), reverse=True)
    print("\nTop 20 Most Used Variables:")
    for i, (var, events) in enumerate(sorted_vars[:20], 1):
        print(f"{i:2}. {var:30} - used in {len(events):3} events")
    
    print("\n" + "=" * 80)
    
    # Save detailed JSON
    with open('deep_variable_analysis_report.json', 'w', encoding='utf-8') as f:
        json.dump(results, f, indent=2, ensure_ascii=False)
    
    print("\n✅ Detailed report saved to: deep_variable_analysis_report.json")
    
    return results

if __name__ == "__main__":
    results = analyze_templates()
    generate_report(results)
