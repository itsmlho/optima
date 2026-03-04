f = r'c:/laragon/www/optima/app/Views/warehouse/inventory/attachments/index.php'
with open(f, 'r', encoding='utf-8') as fh:
    c = fh.read()
reps = [
    ('warehouse/inventory/export_attachment_inventory', 'warehouse/inventory/attachments/export/attachment'),
    ('warehouse/inventory/export_battery_inventory',    'warehouse/inventory/attachments/export/battery'),
    ('warehouse/inventory/export_charger_inventory',    'warehouse/inventory/attachments/export/charger'),
    ('warehouse/inventory/update-attachment/',          'warehouse/inventory/attachments/update/'),
    ('warehouse/inventory/get-attachment-detail/',      'warehouse/inventory/attachments/detail/'),
    ('warehouse/inventory/get-attachment-history/',     'warehouse/inventory/attachments/history/'),
    ('warehouse/inventory/delete-attachment/',          'warehouse/inventory/attachments/delete/'),
    ('warehouse/inventory/get-available-units',         'warehouse/inventory/attachments/available-units'),
    ('warehouse/inventory/attach-to-unit',              'warehouse/inventory/attachments/attach'),
    ('warehouse/inventory/swap-unit',                   'warehouse/inventory/attachments/swap'),
    ('warehouse/inventory/detach-from-unit',            'warehouse/inventory/attachments/detach'),
    ('warehouse/inventory/add-inventory-item',          'warehouse/inventory/attachments/add'),
    ('warehouse/inventory/invent_attachment',           'warehouse/inventory/attachments'),
    ('warehouse/master-attachment',                     'warehouse/inventory/attachments/master/attachment'),
    ('warehouse/master-baterai',                        'warehouse/inventory/attachments/master/baterai'),
    ('warehouse/master-charger',                        'warehouse/inventory/attachments/master/charger'),
    ('warehouse/get-units',                             'warehouse/inventory/attachments/units'),
]
for old, new in reps:
    cnt = c.count(old)
    c = c.replace(old, new)
    print(f'  {cnt}x: {old}')
with open(f, 'w', encoding='utf-8') as fh:
    fh.write(c)
print('Done')
