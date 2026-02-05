<?php
echo "<h1>📋 DOKUMENTASI WORKFLOW QUOTATION SYSTEM</h1>";
echo "<style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background: #f5f7fa; }
    .container { max-width: 1400px; margin: 0 auto; }
    .workflow-card { background: white; border-radius: 12px; padding: 25px; margin: 20px 0; box-shadow: 0 4px 16px rgba(0,0,0,0.1); }
    .stage-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; margin: -25px -25px 20px -25px; }
    .stage-flow { display: flex; align-items: center; gap: 15px; margin: 20px 0; }
    .stage-box { background: white; border: 2px solid #e1e8ed; border-radius: 8px; padding: 15px; text-align: center; min-width: 150px; position: relative; }
    .stage-box.active { border-color: #1da1f2; background: #e8f5fe; }
    .stage-box.success { border-color: #17bf63; background: #e8f7ed; }
    .stage-box.warning { border-color: #ffad1f; background: #fff9e8; }
    .stage-box.danger { border-color: #e0245e; background: #fdeaef; }
    .arrow { font-size: 24px; color: #657786; }
    .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
    .feature-card { background: #f8f9fa; border-radius: 8px; padding: 20px; border-left: 4px solid #1da1f2; }
    .code-example { background: #2d3748; color: #e2e8f0; padding: 15px; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 13px; overflow-x: auto; }
    .version-badge { background: #17bf63; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
    .action-buttons { margin: 15px 0; }
    .btn { padding: 8px 16px; border: none; border-radius: 6px; color: white; margin: 3px; font-size: 12px; cursor: pointer; }
    .btn-success { background: #17bf63; }
    .btn-warning { background: #ffad1f; color: #000; }
    .btn-primary { background: #1da1f2; }
    .btn-danger { background: #e0245e; }
    .btn-info { background: #17a2b8; }
    .btn-secondary { background: #6c757d; }
</style>";

echo "<div class='container'>";

// OVERVIEW
echo "<div class='workflow-card'>";
echo "<div class='stage-header'>";
echo "<h2>🎯 WORKFLOW QUOTATION OPTIMA - DOKUMENTASI LENGKAP</h2>";
echo "<p>Sistem quotation dengan 5 tahap utama + versioning otomatis + audit trail</p>";
echo "</div>";

echo "<h3>📊 RINGKASAN SISTEM SAAT INI:</h3>";
echo "<div class='features-grid'>";

echo "<div class='feature-card'>";
echo "<h4>🔄 Workflow Stages</h4>";
echo "<p><strong>5 tahap utama:</strong> PROSPECT → QUOTATION → SENT → DEAL/NOT_DEAL</p>";
echo "<p><strong>Stage tracking:</strong> DRAFT, SENT, FOLLOW_UP, NEGOTIATION, ACCEPTED, REJECTED, EXPIRED</p>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>📈 Versioning System</h4>";
echo "<p><strong>Auto-increment:</strong> Version naik otomatis saat edit di stage SENT/DEAL</p>";
echo "<p><strong>Audit trail:</strong> Semua perubahan tercatat di quotation_history</p>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>🎯 Sequential Actions</h4>";
echo "<p><strong>Conditional buttons:</strong> Action muncul sesuai stage</p>";
echo "<p><strong>Validation:</strong> Specs required sebelum send</p>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>📝 Complete Tracking</h4>";
echo "<p><strong>History logging:</strong> CREATED, UPDATED, REVISED</p>";
echo "<p><strong>Change summary:</strong> Detail perubahan amount, description, dll</p>";
echo "</div>";

echo "</div>";
echo "</div>";

// DETAILED WORKFLOW
echo "<div class='workflow-card'>";
echo "<h2>🔄 DETAIL WORKFLOW STAGES</h2>";

// Stage 1: PROSPECT
echo "<h3>1️⃣ STAGE: PROSPECT</h3>";
echo "<div class='stage-box warning'>";
echo "<h4>PROSPECT</h4>";
echo "<p>Initial lead/inquiry</p>";
echo "<div class='action-buttons'>";
echo "<button class='btn btn-success'>Create Quotation</button>";
echo "</div>";
echo "</div>";
echo "<p><strong>Fungsi:</strong> Entry point untuk lead baru</p>";
echo "<p><strong>Actions:</strong> Convert to Quotation</p>";

// Stage 2: QUOTATION
echo "<h3>2️⃣ STAGE: QUOTATION</h3>";
echo "<div class='stage-box warning'>";
echo "<h4>QUOTATION</h4>";
echo "<p>Building quotation details</p>";
echo "<div class='action-buttons'>";
echo "<button class='btn btn-warning'>Add Specs</button>";
echo "<button class='btn btn-secondary'>Print</button>";
echo "<button class='btn btn-info'>Send</button>";
echo "</div>";
echo "</div>";
echo "<p><strong>Fungsi:</strong> Membangun detail quotation dan spesifikasi</p>";
echo "<p><strong>Validasi:</strong> Specs harus ada sebelum bisa Send</p>";
echo "<p><strong>Actions:</strong> Add/Edit Specs → Print → Send</p>";

// Stage 3: SENT
echo "<h3>3️⃣ STAGE: SENT</h3>";
echo "<div class='stage-box active'>";
echo "<h4>SENT</h4>";
echo "<p>Awaiting customer response</p>";
echo "<div class='action-buttons'>";
echo "<button class='btn btn-secondary'>Print</button>";
echo "<button class='btn btn-success'>Deal</button>";
echo "<button class='btn btn-danger'>No Deal</button>";
echo "</div>";
echo "</div>";
echo "<p><strong>Fungsi:</strong> Quotation sudah dikirim ke customer, menunggu keputusan</p>";
echo "<p><strong>Versioning:</strong> ⚠️ Edit di stage ini akan increment version (REVISED)</p>";
echo "<p><strong>Actions:</strong> Mark as Deal atau No Deal</p>";

// Stage 4A: DEAL
echo "<h3>4️⃣A STAGE: DEAL</h3>";
echo "<div class='stage-box success'>";
echo "<h4>DEAL</h4>";
echo "<p>Customer agreed - process to contract</p>";
echo "<div class='action-buttons'>";
echo "<button class='btn btn-warning'>Complete Customer Profile</button>";
echo "<button class='btn btn-info'>Complete Contract</button>";
echo "<button class='btn btn-success'>Create SPK</button>";
echo "</div>";
echo "</div>";
echo "<p><strong>Fungsi:</strong> Sequential workflow untuk proses kontrak</p>";
echo "<p><strong>Sequential Steps:</strong></p>";
echo "<ol>";
echo "<li><strong>Complete Customer Profile</strong> - Update customer data & location</li>";
echo "<li><strong>Complete Contract</strong> - Create formal contract</li>";
echo "<li><strong>Create SPK</strong> - Generate work order</li>";
echo "</ol>";
echo "<p><strong>Versioning:</strong> ⚠️ Edit di stage ini akan increment version (REVISED)</p>";

// Stage 4B: NOT_DEAL
echo "<h3>4️⃣B STAGE: NOT_DEAL</h3>";
echo "<div class='stage-box danger'>";
echo "<h4>NOT_DEAL</h4>";
echo "<p>Customer declined</p>";
echo "<p><em>No actions available</em></p>";
echo "</div>";
echo "<p><strong>Fungsi:</strong> Terminal stage untuk quotation yang ditolak</p>";
echo "<p><strong>Actions:</strong> Read-only, no actions available</p>";

echo "</div>";

// VERSIONING SYSTEM
echo "<div class='workflow-card'>";
echo "<h2>📈 VERSIONING & AUDIT SYSTEM</h2>";

echo "<h3>🔢 Version Increment Logic</h3>";
echo "<div class='code-example'>";
echo "// Revision triggers - stages where editing increments version\n";
echo "\$revisionStages = ['SENT', 'FOLLOW_UP', 'NEGOTIATION', 'ACCEPTED'];\n";
echo "\$revisionWorkflowStages = ['SENT', 'DEAL'];\n\n";
echo "// Check if quotation is in revision-triggering stage\n";
echo "if (in_array(\$quotation['stage'], \$revisionStages) || \n";
echo "    in_array(\$quotation['workflow_stage'], \$revisionWorkflowStages)) {\n";
echo "    \$data['version'] = \$currentVersion + 1;\n";
echo "    \$data['revision_status'] = 'REVISED';\n";
echo "    \$isRevision = true;\n";
echo "}";
echo "</div>";

echo "<h3>📝 History Logging</h3>";
echo "<div class='features-grid'>";

echo "<div class='feature-card'>";
echo "<h4>📊 Action Types</h4>";
echo "<ul>";
echo "<li><span class='version-badge'>CREATED</span> - Quotation dibuat</li>";
echo "<li><span class='version-badge'>UPDATED</span> - Edit biasa (stage PROSPECT/QUOTATION)</li>";
echo "<li><span class='version-badge'>REVISED</span> - Edit dengan version increment (stage SENT/DEAL)</li>";
echo "</ul>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>🔍 Change Tracking</h4>";
echo "<ul>";
echo "<li><strong>changes_summary:</strong> Ringkasan perubahan yang user-friendly</li>";
echo "<li><strong>old_values:</strong> JSON nilai sebelum edit</li>";
echo "<li><strong>new_values:</strong> JSON nilai setelah edit</li>";
echo "<li><strong>ip_address & user_agent:</strong> Audit metadata</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<h3>💾 Database Tables</h3>";
echo "<div class='code-example'>";
echo "quotation_history:\n";
echo "├── id (PK)\n";
echo "├── quotation_id (FK)\n";
echo "├── version\n";
echo "├── action_type (CREATED/UPDATED/REVISED)\n";
echo "├── changed_by (FK users)\n";
echo "├── changed_at\n";
echo "├── changes_summary\n";
echo "├── old_values (JSON)\n";
echo "├── new_values (JSON)\n";
echo "├── ip_address\n";
echo "└── user_agent\n\n";
echo "vw_quotation_history_detail:\n";
echo "└── View with JOIN to users & quotations for display";
echo "</div>";

echo "</div>";

// BUSINESS LOGIC
echo "<div class='workflow-card'>";
echo "<h2>💼 BUSINESS LOGIC & RULES</h2>";

echo "<h3>🚫 Edit Restrictions</h3>";
echo "<div class='features-grid'>";

echo "<div class='feature-card'>";
echo "<h4>❌ Cannot Edit When:</h4>";
echo "<ul>";
echo "<li>Already linked to contract (<code>created_contract_id</code> exists)</li>";
echo "<li>Stage is EXPIRED</li>";
echo "<li>Workflow stage is NOT_DEAL</li>";
echo "</ul>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>⚠️ Versioning Triggers:</h4>";
echo "<ul>";
echo "<li>Stage: SENT, FOLLOW_UP, NEGOTIATION, ACCEPTED</li>";
echo "<li>Workflow: SENT, DEAL</li>";
echo "<li>Reason: Customer sudah terlibat, perubahan harus tracked</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "<h3>🎯 Sequential DEAL Workflow</h3>";
echo "<div class='stage-flow'>";

echo "<div class='stage-box warning'>";
echo "<strong>Step 1</strong><br>";
echo "Complete Customer Profile";
echo "<br><small>Location & basic info</small>";
echo "</div>";

echo "<span class='arrow'>➡️</span>";

echo "<div class='stage-box info'>";
echo "<strong>Step 2</strong><br>";
echo "Complete Contract";
echo "<br><small>Formal agreement</small>";
echo "</div>";

echo "<span class='arrow'>➡️</span>";

echo "<div class='stage-box success'>";
echo "<strong>Step 3</strong><br>";
echo "Create SPK";
echo "<br><small>Work order generation</small>";
echo "</div>";

echo "</div>";

echo "<p><strong>Database Flags:</strong></p>";
echo "<div class='code-example'>";
echo "quotations table flags:\n";
echo "├── customer_location_complete (boolean)\n";
echo "├── customer_contract_complete (boolean)\n";
echo "└── spk_created (boolean)\n\n";
echo "// Sequential validation\n";
echo "if (!customer_location_complete) {\n";
echo "    show 'Complete Customer Profile' button\n";
echo "} else if (!customer_contract_complete) {\n";
echo "    show 'Complete Contract' button  \n";
echo "} else if (!spk_created) {\n";
echo "    show 'Create SPK' button\n";
echo "} else {\n";
echo "    show 'SPK Created' badge\n";
echo "}";
echo "</div>";

echo "</div>";

// TECHNICAL IMPLEMENTATION
echo "<div class='workflow-card'>";
echo "<h2>⚙️ TECHNICAL IMPLEMENTATION</h2>";

echo "<h3>🔧 Key Functions</h3>";
echo "<div class='features-grid'>";

echo "<div class='feature-card'>";
echo "<h4>getQuotationActions()</h4>";
echo "<p>Generate dynamic action buttons berdasarkan workflow stage</p>";
echo "<ul>";
echo "<li>Validasi specifications</li>";
echo "<li>Check customer completion status</li>";
echo "<li>Sequential workflow logic</li>";
echo "</ul>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>updateQuotation()</h4>";
echo "<p>Handle quotation updates dengan versioning logic</p>";
echo "<ul>";
echo "<li>Edit restrictions validation</li>";
echo "<li>Version increment logic</li>";
echo "<li>Audit trail logging</li>";
echo "</ul>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>logQuotationChange()</h4>";
echo "<p>Comprehensive audit logging system</p>";
echo "<ul>";
echo "<li>Change summary generation</li>";
echo "<li>JSON old/new values storage</li>";
echo "<li>User & IP tracking</li>";
echo "</ul>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>getWorkflowStageBadge()</h4>";
echo "<p>Visual stage indicators dengan color coding</p>";
echo "<ul>";
echo "<li>Bootstrap badge styling</li>";
echo "<li>Consistent UI/UX</li>";
echo "<li>Stage status clarity</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "</div>";

// TESTING & VERIFICATION
echo "<div class='workflow-card'>";
echo "<h2>🧪 TESTING & VERIFICATION</h2>";

echo "<p><strong>Untuk memverifikasi sistem bekerja dengan baik:</strong></p>";

echo "<div class='features-grid'>";

echo "<div class='feature-card'>";
echo "<h4>1. Test Version Increment</h4>";
echo "<ol>";
echo "<li>Create quotation (v1)</li>";
echo "<li>Move to SENT stage</li>";
echo "<li>Edit quotation → should become v2</li>";
echo "<li>Check quotation_history for REVISED record</li>";
echo "</ol>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>2. Test Workflow Progression</h4>";
echo "<ol>";
echo "<li>PROSPECT → Create Quotation</li>";
echo "<li>QUOTATION → Add Specs → Send</li>";
echo "<li>SENT → Mark as Deal</li>";
echo "<li>DEAL → Complete sequential steps</li>";
echo "</ol>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>3. Test Audit Trail</h4>";
echo "<ol>";
echo "<li>Create quotation</li>";
echo "<li>Edit multiple times</li>";
echo "<li>Check vw_quotation_history_detail</li>";
echo "<li>Verify change summaries</li>";
echo "</ol>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>4. Test Restrictions</h4>";
echo "<ol>";
echo "<li>Try edit expired quotation → should fail</li>";
echo "<li>Try edit NOT_DEAL → should fail</li>";
echo "<li>Try send without specs → should be disabled</li>";
echo "</ol>";
echo "</div>";

echo "</div>";

echo "</div>";

echo "<div class='workflow-card'>";
echo "<div class='stage-header'>";
echo "<h2>✅ SISTEM STATUS</h2>";
echo "</div>";

echo "<div class='features-grid'>";

echo "<div class='feature-card'>";
echo "<h4>🎯 Workflow Implementation</h4>";
echo "<p><strong>Status:</strong> ✅ <span style='color: #17bf63;'>FULLY IMPLEMENTED</span></p>";
echo "<p>5-stage workflow dengan sequential DEAL processing</p>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>📈 Versioning System</h4>";
echo "<p><strong>Status:</strong> ✅ <span style='color: #17bf63;'>ACTIVE</span></p>";
echo "<p>Auto-increment version dengan comprehensive logging</p>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>🛡️ Business Rules</h4>";
echo "<p><strong>Status:</strong> ✅ <span style='color: #17bf63;'>ENFORCED</span></p>";
echo "<p>Edit restrictions, validations, dan sequential workflow</p>";
echo "</div>";

echo "<div class='feature-card'>";
echo "<h4>📊 Audit & Reporting</h4>";
echo "<p><strong>Status:</strong> ✅ <span style='color: #17bf63;'>COMPREHENSIVE</span></p>";
echo "<p>Complete change tracking dengan user-friendly summaries</p>";
echo "</div>";

echo "</div>";

echo "<p style='text-align: center; font-size: 18px; color: #17bf63; font-weight: bold; margin-top: 30px;'>";
echo "🎉 QUOTATION WORKFLOW SYSTEM SUDAH LENGKAP DAN SIAP DIGUNAKAN!";
echo "</p>";

echo "</div>";

echo "</div>";
?>