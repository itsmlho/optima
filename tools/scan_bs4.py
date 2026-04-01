import os, re

views = 'app/Views'
patterns = {
    'data-dismiss': re.compile(r'data-dismiss=["\']modal'),
    'form-group': re.compile(r'class="form-group"'),
    'close-btn': re.compile(r'class="close"'),
    'dt-cdn-old': re.compile(r'datatables\.net/1\.(11|10|9|8|7)'),
    'dt-bootstrap4': re.compile(r'dataTables\.bootstrap4|buttons\.bootstrap4'),
}

results = {k: [] for k in patterns}

for root, dirs, files in os.walk(views):
    dirs[:] = [d for d in dirs if d not in ['demo', 'emails', 'Examples']]
    for f in files:
        if not f.endswith('.php'):
            continue
        path = os.path.join(root, f)
        try:
            content = open(path, encoding='utf-8', errors='ignore').read()
        except Exception:
            continue
        for k, pat in patterns.items():
            if pat.search(content):
                results[k].append(path)

for k, files in results.items():
    print(f'=== {k} ({len(files)} files) ===')
    for f in sorted(files):
        print(f'  {f}')
    print()
