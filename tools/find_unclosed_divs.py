import re

path = r'c:\laragon\www\optima\app\Views\service\work_orders.php'
lines = open(path, encoding='utf-8').readlines()

# Remove PHP blocks line by line (simple approach)
stack = []  # stack of (line_num, indent)
depth = 0

for i, line in enumerate(lines, 1):
    # Skip PHP-only lines  
    clean = re.sub(r'<\?.*?\?>', '', line)
    
    # Count opens and closes on this line
    opens_here = len(re.findall(r'<div[\s>]', clean))
    closes_here = clean.count('</div>')
    
    for _ in range(opens_here):
        stack.append(i)
        depth += 1
    for _ in range(closes_here):
        if stack:
            stack.pop()
            depth -= 1

print(f'Total unclosed divs: {len(stack)}')
print(f'Unclosed at lines: {stack}')

# Show surrounding context for each unclosed div
for ln in stack:
    print(f'\n--- Line {ln} (unclosed <div>) ---')
    start = max(0, ln-3)
    end = min(len(lines), ln+3)
    for i in range(start, end):
        marker = '>>>' if i+1 == ln else '   '
        print(f'{marker} {i+1:4d}: {lines[i].rstrip()}')
