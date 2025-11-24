#!/usr/bin/env python3
"""
Script untuk mengurutkan ulang SQL dump file berdasarkan dependensi foreign key.
Urutan: Tables (tanpa FK) -> Views -> Procedures/Functions -> FK Constraints
"""

import re
import sys
from collections import defaultdict, deque

def extract_table_name(create_table_line):
    """Extract table name from CREATE TABLE statement"""
    match = re.search(r'CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?', create_table_line, re.IGNORECASE)
    return match.group(1) if match else None

def extract_fk_dependencies(alter_table_section):
    """Extract foreign key dependencies from ALTER TABLE statements"""
    dependencies = defaultdict(set)
    
    # Pattern untuk ALTER TABLE ... ADD CONSTRAINT ... FOREIGN KEY ... REFERENCES
    # Handle multi-line constraints
    pattern = r'ALTER TABLE `?(\w+)`?[^;]*?ADD CONSTRAINT[^;]*?FOREIGN KEY[^;]*?REFERENCES `?(\w+)`?[^;]*?;'
    
    for match in re.finditer(pattern, alter_table_section, re.IGNORECASE | re.DOTALL):
        table = match.group(1)
        referenced_table = match.group(2)
        dependencies[table].add(referenced_table)
    
    # Also extract individual FK constraints within a single ALTER TABLE block
    # Pattern: ADD CONSTRAINT ... FOREIGN KEY ... REFERENCES
    fk_pattern = r'ADD CONSTRAINT[^,;]*?FOREIGN KEY[^,;]*?REFERENCES `?(\w+)`?'
    
    # Find all ALTER TABLE blocks
    alter_blocks = re.finditer(r'ALTER TABLE `?(\w+)`?(.*?);', alter_table_section, re.IGNORECASE | re.DOTALL)
    
    for block_match in alter_blocks:
        table = block_match.group(1)
        block_content = block_match.group(2)
        
        # Find all FK references in this block
        for fk_match in re.finditer(fk_pattern, block_content, re.IGNORECASE):
            referenced_table = fk_match.group(1)
            dependencies[table].add(referenced_table)
    
    return dependencies

def topological_sort(tables, dependencies):
    """Topological sort untuk mengurutkan tabel berdasarkan dependensi"""
    # Build graph
    graph = defaultdict(set)
    in_degree = defaultdict(int)
    
    # Initialize all tables
    for table in tables:
        in_degree[table] = 0
    
    # Build graph from dependencies
    for table, deps in dependencies.items():
        if table in tables:
            for dep in deps:
                if dep in tables:
                    graph[dep].add(table)
                    in_degree[table] += 1
    
    # Kahn's algorithm
    queue = deque([table for table in tables if in_degree[table] == 0])
    result = []
    
    while queue:
        node = queue.popleft()
        result.append(node)
        
        for neighbor in graph[node]:
            in_degree[neighbor] -= 1
            if in_degree[neighbor] == 0:
                queue.append(neighbor)
    
    # Add any remaining tables (shouldn't happen if no cycles)
    remaining = [t for t in tables if t not in result]
    result.extend(remaining)
    
    return result

def parse_sql_file(filename):
    """Parse SQL file dan ekstrak semua bagian"""
    # Read file as binary first to preserve all characters
    with open(filename, 'rb') as f:
        content_bytes = f.read()
    
    # Decode to string, preserving all escape sequences
    content = content_bytes.decode('utf-8', errors='ignore')
    
    # Split content into sections
    sections = {
        'header': '',
        'procedures': '',
        'tables': {},
        'table_data': {},
        'views': '',
        'fk_constraints': '',
        'footer': ''
    }
    
    # Extract header (until first procedure or table)
    header_end = min(
        content.find('DELIMITER $$') if 'DELIMITER $$' in content else len(content),
        content.find('CREATE TABLE') if 'CREATE TABLE' in content else len(content),
        content.find('-- Table structure') if '-- Table structure' in content else len(content)
    )
    if header_end > 0 and header_end < len(content):
        sections['header'] = content[:header_end]
    
    # Extract procedures (between DELIMITER $$ and DELIMITER ;)
    proc_pattern = r'(DELIMITER \$\$.*?DELIMITER ;)'
    procedures = re.findall(proc_pattern, content, re.DOTALL)
    sections['procedures'] = '\n\n'.join(procedures) if procedures else ''
    
    # Extract all CREATE TABLE statements with their data
    # Use more precise pattern to capture everything including INSERT
    # Pattern: DROP TABLE ... CREATE TABLE ... ENGINE=...;
    table_pattern = r'DROP TABLE IF EXISTS `?(\w+)`?.*?CREATE TABLE.*?ENGINE=[^;]+;'
    table_matches = list(re.finditer(table_pattern, content, re.DOTALL | re.IGNORECASE))
    
    # Get all table positions
    table_positions = []
    for match in table_matches:
        table_name = match.group(1)
        start_pos = match.start()
        end_pos = match.end()
        table_positions.append((table_name, start_pos, end_pos, match.group(0)))
    
    # For each table, find its INSERT statements
    for i, (table_name, start_pos, end_pos, table_def) in enumerate(table_positions):
        # Find INSERT statements after this table definition
        next_table_start = table_positions[i+1][1] if i+1 < len(table_positions) else len(content)
        search_area = content[end_pos:next_table_start]
        
        # Find INSERT statement start
        insert_start_pattern = rf'INSERT INTO `?{re.escape(table_name)}`?'
        insert_start_match = re.search(insert_start_pattern, search_area, re.IGNORECASE)
        
        if insert_start_match:
            # Find the end of INSERT statement by looking for semicolon at end of line
            # or before next SQL statement
            insert_start = insert_start_match.start()
            insert_text = search_area[insert_start:]
            
            # Find the actual end - look for semicolon followed by newline or end of string
            # But be careful with semicolons inside JSON strings
            # Simple approach: find semicolon at end of line or before comment/new statement
            semicolon_pos = insert_text.find(';\n')
            if semicolon_pos == -1:
                semicolon_pos = insert_text.find(';\r\n')
            if semicolon_pos == -1:
                semicolon_pos = insert_text.find(';')
            
            if semicolon_pos != -1:
                # Include the semicolon and newline
                insert_text = insert_text[:semicolon_pos + 1]
                # Also include any trailing newlines
                while semicolon_pos + 1 < len(insert_text) and insert_text[semicolon_pos + 1] in '\n\r':
                    semicolon_pos += 1
                insert_text = insert_text[:semicolon_pos + 1]
                
                table_def += '\n\n' + insert_text
        
        sections['tables'][table_name] = table_def
    
    # Extract views
    view_pattern = r'(CREATE OR REPLACE VIEW `?(\w+)`?.*?;)'
    views = re.findall(view_pattern, content, re.DOTALL | re.IGNORECASE)
    sections['views'] = '\n\n'.join([v[0] for v in views]) if views else ''
    
    # Extract FK constraints (ALTER TABLE statements)
    fk_start = content.find('-- Constraints for table')
    if fk_start > 0:
        sections['fk_constraints'] = content[fk_start:]
    
    return sections

def reorganize_sql(input_file, output_file):
    """Reorganize SQL file dengan urutan yang benar"""
    print("Parsing SQL file...")
    sections = parse_sql_file(input_file)
    
    print(f"Found {len(sections['tables'])} tables")
    
    # Extract FK dependencies from constraints section
    print("Extracting foreign key dependencies...")
    fk_dependencies = extract_fk_dependencies(sections['fk_constraints'])
    
    # Get all table names
    all_tables = list(sections['tables'].keys())
    
    # Topological sort
    print("Sorting tables by dependencies...")
    sorted_tables = topological_sort(all_tables, fk_dependencies)
    
    print(f"Sorted {len(sorted_tables)} tables")
    
    # Write reorganized file
    print(f"Writing reorganized file to {output_file}...")
    # Write as binary to preserve all escape sequences
    with open(output_file, 'wb') as f:
        # Write header
        f.write(sections['header'].encode('utf-8'))
        f.write(b'\n\n')
        
        # Disable foreign key checks to allow DROP TABLE without constraint errors
        f.write(b'-- Disable foreign key checks for table creation\n')
        f.write(b'SET FOREIGN_KEY_CHECKS = 0;\n\n')
        
        # Write tables in sorted order (without FK constraints)
        f.write(b'-- --------------------------------------------------------\n')
        f.write(b'-- TABLES (ordered by dependencies)\n')
        f.write(b'-- --------------------------------------------------------\n\n')
        
        for table_name in sorted_tables:
            if table_name in sections['tables']:
                f.write(f'--\n-- Table: {table_name}\n--\n'.encode('utf-8'))
                f.write(sections['tables'][table_name].encode('utf-8'))
                f.write(b'\n\n')
        
        # Write views
        if sections['views']:
            f.write(b'-- --------------------------------------------------------\n')
            f.write(b'-- VIEWS\n')
            f.write(b'-- --------------------------------------------------------\n\n')
            f.write(sections['views'].encode('utf-8'))
            f.write(b'\n\n')
        
        # Write procedures
        if sections['procedures']:
            f.write(b'-- --------------------------------------------------------\n')
            f.write(b'-- PROCEDURES AND FUNCTIONS\n')
            f.write(b'-- --------------------------------------------------------\n\n')
            f.write(sections['procedures'].encode('utf-8'))
            f.write(b'\n\n')
        
        # Write FK constraints
        if sections['fk_constraints']:
            f.write(b'-- --------------------------------------------------------\n')
            f.write(b'-- FOREIGN KEY CONSTRAINTS\n')
            f.write(b'-- --------------------------------------------------------\n\n')
            f.write(sections['fk_constraints'].encode('utf-8'))
        
        # Re-enable foreign key checks after all constraints are added
        f.write(b'\n\n-- Re-enable foreign key checks\n')
        f.write(b'SET FOREIGN_KEY_CHECKS = 1;\n')
    
    print("Done! File reorganized successfully.")
    print(f"\nTable creation order:")
    for i, table in enumerate(sorted_tables, 1):
        deps = fk_dependencies.get(table, set())
        deps_str = ', '.join(deps) if deps else 'none'
        print(f"  {i:3d}. {table:40s} (depends on: {deps_str})")

if __name__ == '__main__':
    input_file = '/opt/lampp/htdocs/optima1/databases/optima_db 24-11-25.sql'
    output_file = '/opt/lampp/htdocs/optima1/databases/optima_db_24-11-25_reorganized.sql'
    
    reorganize_sql(input_file, output_file)

