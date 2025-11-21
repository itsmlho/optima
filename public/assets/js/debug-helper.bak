// Simple debug functions (production-safe)
window.checkSPA = function() {
    const status = {
        spa: !!window.optimaSPA,
        dataRefresh: !!window.OptimaDataRefresh,
        tables: document.querySelectorAll('table').length,
        activeLink: document.querySelector('.nav-link.active')?.textContent?.trim()
    };
    console.log('SPA Status:', status);
    return status;
};

window.reloadData = function() {
    if (window.optimaSPA) {
        window.optimaSPA.triggerPageDataLoading();
    }
};

window.debugSPA = function() {
    console.log('Path:', window.location.pathname);
    console.log('SPA Current Path:', window.optimaSPA?.currentPath);
    console.log('Tables found:', document.querySelectorAll('table').length);
    console.log('DataTables:', document.querySelectorAll('.dataTable').length);
};

window.checkTables = function() {
    const tables = document.querySelectorAll('table');
    console.log('📋 Comprehensive Table Analysis:');
    
    if (tables.length === 0) {
        console.log('❌ No tables found in document');
        return;
    }
    
    tables.forEach((table, index) => {
        const isDataTable = window.$ && $.fn.DataTable && $.fn.DataTable.isDataTable(table);
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        const headerRow = thead ? thead.querySelector('tr') : table.querySelector('tr');
        const bodyRows = tbody ? tbody.querySelectorAll('tr') : table.querySelectorAll('tr:not(:first-child)');
        
        const headerCount = headerRow ? headerRow.children.length : 0;
        const firstBodyRow = bodyRows.length > 0 ? bodyRows[0] : null;
        const rowCount = firstBodyRow ? firstBodyRow.children.length : 0;
        const mismatch = headerCount !== rowCount && rowCount > 0;
        
        const analysis = {
            index: index + 1,
            id: table.id || 'unnamed',
            tagName: table.tagName,
            isDataTable,
            hasId: !!table.id,
            hasClass: !!table.className,
            classes: table.className,
            hasHead: !!thead,
            hasBody: !!tbody,
            headerCols: headerCount,
            bodyRows: bodyRows.length,
            firstRowCols: rowCount,
            mismatch: mismatch,
            isEmpty: bodyRows.length === 0,
            hasData: bodyRows.length > 0 && rowCount > 0
        };
        
        const status = mismatch ? '❌ MISMATCH' : 
                     analysis.isEmpty ? '⚠️ EMPTY' : 
                     analysis.hasData ? '✅ VALID' : '❓ UNKNOWN';
        
        console.log(`${status} Table ${analysis.index}: ${analysis.id}`, analysis);
    });
    
    // Summary
    const validTables = Array.from(tables).filter(t => {
        const thead = t.querySelector('thead');
        const tbody = t.querySelector('tbody');
        const headerRow = thead ? thead.querySelector('tr') : t.querySelector('tr');
        const bodyRows = tbody ? tbody.querySelectorAll('tr') : t.querySelectorAll('tr:not(:first-child)');
        const headerCount = headerRow ? headerRow.children.length : 0;
        const firstBodyRow = bodyRows.length > 0 ? bodyRows[0] : null;
        const rowCount = firstBodyRow ? firstBodyRow.children.length : 0;
        return headerCount === rowCount || bodyRows.length === 0;
    });
    
    console.log(`\n📊 Summary: ${validTables.length}/${tables.length} tables are valid`);
};

window.fixTables = function() {
    console.log('🔧 Fixing table structures...');
    if (window.optimaSPA && window.optimaSPA.fixTableStructure) {
        const tables = document.querySelectorAll('table[id]');
        let fixedCount = 0;
        
        tables.forEach(table => {
            const fixed = window.optimaSPA.fixTableStructure(table);
            if (fixed) {
                console.log(`✅ Fixed: ${table.id}`);
                fixedCount++;
                // Reinitialize if possible
                if (window.optimaSPA.reinitializeTable) {
                    window.optimaSPA.reinitializeTable(table.id);
                }
            }
        });
        
        console.log(`Fixed ${fixedCount} tables`);
        return fixedCount;
    }
};
