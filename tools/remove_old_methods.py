"""Remove old attachment methods from Warehouse.php."""
with open(r'c:\laragon\www\optima\app\Controllers\Warehouse.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

print('Total lines:', len(lines))

# Lines to KEEP (1-indexed, converted to 0-indexed for Python slices):
# Keep   1-312  (idx 0-311)   : everything before exportAttachmentInventory
# Skip 313-470  (idx 312-469) : exportAttachmentInventory/Battery/Charger
# Keep 471-1357 (idx 470-1356): confirmUnitToAsset through debugInventUnit
# Skip 1358-3315 (idx 1357-3314): all attachment methods (inventAttachment…getUnits)
# Keep 3316     (idx 3315)    : class closing brace '}'

keep_lines = lines[0:312] + lines[470:1357] + lines[3315:]
print('Lines after removal:', len(keep_lines))

with open(r'c:\laragon\www\optima\app\Controllers\Warehouse.php', 'w', encoding='utf-8') as f:
    f.writelines(keep_lines)
print('Done.')
