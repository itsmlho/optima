import re

path = r'c:\laragon\www\optima\app\Views\service\work_orders.php'
content = open(path, encoding='utf-8').read()

# Remove PHP blocks to avoid false positives
content_no_php = re.sub(r'<\?.*?\?>', '', content, flags=re.DOTALL)

opens = len(re.findall(r'<div[\s>]', content_no_php))
closes = content_no_php.count('</div>')
print(f'<div opens : {opens}')
print(f'</div closes: {closes}')
print(f'Difference  : {opens - closes}  (positive = unclosed divs)')

# Also check section opening/ending markers
print()
for marker in ['section(\'content\')', 'section(\'css\')', 'section(\'javascript\')']:
    n = content.count(marker)
    print(f"  $this->{marker}: {n}")

ends = content.count('endSection()')
print(f"  endSection(): {ends}")

# Check for style blocks that might affect layout
style_blocks = re.findall(r'<style[^>]*>.*?</style>', content_no_php, re.DOTALL | re.IGNORECASE)
print(f'\nStyle blocks found: {len(style_blocks)}')
for i, sb in enumerate(style_blocks):
    # Check for suspicious layout rules
    if any(kw in sb for kw in ['sidebar', 'main-content', 'width', 'margin', 'overflow']):
        print(f'  Block {i+1} contains layout CSS:')
        for line in sb.split('\n')[:20]:
            if line.strip():
                print(f'    {line.rstrip()}')
