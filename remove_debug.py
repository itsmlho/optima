#!/usr/bin/env python3
import re

# Read the file
with open('/opt/lampp/htdocs/optima1/app/Views/marketing/kontrak.php', 'r') as f:
    content = f.read()

# Remove all console.log statements (including multiline ones)
content = re.sub(r'console\.log\([^)]*\);?\s*', '', content, flags=re.MULTILINE | re.DOTALL)

# Remove other debug patterns
content = re.sub(r'console\.error\([^)]*\);?\s*', '', content, flags=re.MULTILINE | re.DOTALL)
content = re.sub(r'console\.warn\([^)]*\);?\s*', '', content, flags=re.MULTILINE | re.DOTALL)

# Remove empty lines that might be left
content = re.sub(r'\n\s*\n\s*\n', '\n\n', content)

# Write back
with open('/opt/lampp/htdocs/optima1/app/Views/marketing/kontrak.php', 'w') as f:
    f.write(content)

print("Debug statements removed successfully!")
