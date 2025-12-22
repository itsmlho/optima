#!/usr/bin/env python3
"""
Audit and Fix Notification URLs
Checks all notification helper URLs against actual routes
"""

import re
from collections import defaultdict

# Read notification helper
with open('app/Helpers/notification_helper.php', 'r', encoding='utf-8') as f:
    helper_content = f.read()

# Extract all function URLs
functions = re.findall(r'function\s+(notify_\w+)\s*\([^)]*\)\s*{(.*?)(?=function\s+notify_|\Z)', helper_content, re.DOTALL)

url_issues = []

for func_name, func_body in functions:
    event_name = func_name.replace('notify_', '')
    
    # Find URL in function
    url_match = re.search(r"'url'\s*=>\s*([^\n,]+)", func_body)
    if url_match:
        url_line = url_match.group(1).strip().rstrip(',')
        url_issues.append({
            'event': event_name,
            'function': func_name,
            'url': url_line
        })

# Group by URL pattern
print("="*80)
print("NOTIFICATION URL AUDIT REPORT")
print("="*80)
print()

# Common issues
print("🔍 CHECKING FOR COMMON ISSUES:")
print("-"*80)

issues_found = []

for item in url_issues:
    url = item['url']
    event = item['event']
    
    # Check for common problems
    if '/attachment/view/' in url:
        issues_found.append({
            'event': event,
            'issue': '❌ Wrong attachment URL',
            'current': url,
            'should_be': "base_url('/warehouse/inventory/get-attachment-detail/' . \$id)"
        })
    
    if '/warehouse/unit/' in url and 'get-unit-detail' not in url:
        issues_found.append({
            'event': event,
            'issue': '⚠️  Generic warehouse/unit URL',
            'current': url,
            'should_be': "base_url('/warehouse/inventory/get-unit-detail/' . \$unit_id)"
        })
    
    if url == "base_url('/operational/delivery')":
        issues_found.append({
            'event': event,
            'issue': '⚠️  Generic delivery URL (no detail)',
            'current': url,
            'should_be': "base_url('/operational/delivery/detail/' . \$delivery_id)"
        })
    
    if url == "base_url('/operational/spk')":
        issues_found.append({
            'event': event,
            'issue': '⚠️  Generic SPK URL (no detail)',
            'current': url,
            'should_be': "base_url('/operational/spk/detail/' . \$spk_id)"
        })
    
    if url == "base_url('/operational/workorder')":
        issues_found.append({
            'event': event,
            'issue': '⚠️  Generic Work Order URL (no detail)',
            'current': url,
            'should_be': "base_url('/operational/workorder/detail/' . \$wo_id)"
        })
    
    if url == "base_url('/purchasing')":
        issues_found.append({
            'event': event,
            'issue': '⚠️  Generic purchasing URL (no detail)',
            'current': url,
            'should_be': "base_url('/purchasing/detail/' . \$po_id)"
        })

print(f"\n📊 Total Functions Checked: {len(url_issues)}")
print(f"❌ Issues Found: {len(issues_found)}")
print()

if issues_found:
    print("🔧 ISSUES TO FIX:")
    print("-"*80)
    for idx, issue in enumerate(issues_found, 1):
        print(f"\n{idx}. Event: {issue['event']}")
        print(f"   {issue['issue']}")
        print(f"   Current:    {issue['current']}")
        print(f"   Should be:  {issue['should_be']}")
    
    # Generate fix recommendations
    print("\n" + "="*80)
    print("RECOMMENDED FIXES BY MODULE")
    print("="*80)
    
    by_module = defaultdict(list)
    for issue in issues_found:
        if 'attachment' in issue['event'] or 'warehouse' in issue['current']:
            by_module['Warehouse'].append(issue)
        elif 'delivery' in issue['event']:
            by_module['Delivery'].append(issue)
        elif 'spk' in issue['event']:
            by_module['SPK'].append(issue)
        elif 'workorder' in issue['event'] or 'work_order' in issue['event']:
            by_module['Work Order'].append(issue)
        elif 'po_' in issue['event'] or 'purchasing' in issue['current']:
            by_module['Purchase Order'].append(issue)
        else:
            by_module['Other'].append(issue)
    
    for module, issues in sorted(by_module.items()):
        print(f"\n### {module} ({len(issues)} issues)")
        print("-"*80)
        for issue in issues:
            print(f"  • {issue['event']}: {issue['issue']}")

else:
    print("✅ No common URL issues found!")

print("\n" + "="*80)
print("📝 COMPLETE URL LIST BY EVENT CATEGORY")
print("="*80)

# Group by category
categories = defaultdict(list)
for item in url_issues:
    event = item['event']
    if event.startswith('attachment_'):
        categories['Attachment'].append(item)
    elif event.startswith('delivery_'):
        categories['Delivery'].append(item)
    elif event.startswith('spk_'):
        categories['SPK'].append(item)
    elif event.startswith('work_order_') or event.startswith('workorder_'):
        categories['Work Order'].append(item)
    elif event.startswith('po_'):
        categories['Purchase Order'].append(item)
    elif event.startswith('di_'):
        categories['DI'].append(item)
    elif event.startswith('invoice_'):
        categories['Invoice'].append(item)
    elif event.startswith('pmps_'):
        categories['PMPS'].append(item)
    elif event.startswith('customer_'):
        categories['Customer'].append(item)
    elif event.startswith('sparepart_'):
        categories['Sparepart'].append(item)
    else:
        categories['Other'].append(item)

for cat, items in sorted(categories.items()):
    print(f"\n{cat} ({len(items)} events)")
    print("-"*80)
    for item in items:
        print(f"  {item['event']:<30} → {item['url']}")

print("\n" + "="*80)
print("✅ Audit Complete! Check issues above.")
print("="*80)
