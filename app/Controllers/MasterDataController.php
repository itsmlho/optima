<?php

namespace App\Controllers;

class MasterDataController extends BaseController
{
    protected $db;
    protected array $entityRegistry = [
        'departemen' => ['table' => 'departemen', 'pk' => 'id_departemen', 'title' => 'Departemen'],
        'tipe_unit' => [
            'table' => 'tipe_unit', 'pk' => 'id_tipe_unit', 'title' => 'Tipe Unit',
            'fk' => [
                'id_departemen' => ['entity' => 'departemen', 'table' => 'departemen', 'pk' => 'id_departemen', 'label' => 'nama_departemen', 'display_label' => 'Departemen'],
            ],
        ],
        // NOTE: In this codebase, "Jenis Unit" is effectively represented by `tipe_unit.jenis`
        // (see Purchasing quick-add flow which uses tipeUnitModel).
        // The standalone `jenis_unit` table may be empty/unused, so we map to `tipe_unit` to avoid confusion.
        'jenis_unit' => [
            'table' => 'tipe_unit', 'pk' => 'id_tipe_unit', 'title' => 'Jenis Unit (tipe_unit)',
            'fk' => [
                'id_departemen' => ['entity' => 'departemen', 'table' => 'departemen', 'pk' => 'id_departemen', 'label' => 'nama_departemen', 'display_label' => 'Departemen'],
            ],
        ],
        'model_unit' => [
            'table' => 'model_unit', 'pk' => 'id_model_unit', 'title' => 'Model Unit',
            'fk' => [
                'departemen_id' => ['entity' => 'departemen', 'table' => 'departemen', 'pk' => 'id_departemen', 'label' => 'nama_departemen', 'display_label' => 'Departemen'],
            ],
        ],
        'kapasitas' => ['table' => 'kapasitas', 'pk' => 'id_kapasitas', 'title' => 'Kapasitas'],
        'tipe_mast' => ['table' => 'tipe_mast', 'pk' => 'id_mast', 'title' => 'Tipe Mast'],
        'tipe_ban' => ['table' => 'tipe_ban', 'pk' => 'id_ban', 'title' => 'Tipe Ban'],
        'jenis_roda' => ['table' => 'jenis_roda', 'pk' => 'id_roda', 'title' => 'Jenis Roda'],
        'valve' => ['table' => 'valve', 'pk' => 'id_valve', 'title' => 'Valve'],
        'status_unit' => ['table' => 'status_unit', 'pk' => 'id_status', 'title' => 'Status Unit'],
        'attachment' => ['table' => 'attachment', 'pk' => 'id_attachment', 'title' => 'Attachment'],
        'baterai' => ['table' => 'baterai', 'pk' => 'id', 'title' => 'Baterai'],
        'charger' => ['table' => 'charger', 'pk' => 'id_charger', 'title' => 'Charger'],
        'mesin' => [
            'table' => 'mesin', 'pk' => 'id', 'title' => 'Mesin',
            'fk' => [
                'departemen_id' => ['entity' => 'departemen', 'table' => 'departemen', 'pk' => 'id_departemen', 'label' => 'nama_departemen', 'display_label' => 'Departemen'],
            ],
        ],
        'status_attachment' => ['table' => 'status_attachment', 'pk' => 'id', 'title' => 'Status Attachment'],
        'inventory_status' => ['table' => 'inventory_status', 'pk' => 'id', 'title' => 'Inventory Status'],
        'work_order_category' => ['table' => 'work_order_categories', 'pk' => 'id', 'title' => 'Work Order Category'],
        'work_order_priority' => ['table' => 'work_order_priorities', 'pk' => 'id', 'title' => 'Work Order Priority'],
        'work_order_status' => ['table' => 'work_order_statuses', 'pk' => 'id', 'title' => 'Work Order Status'],
        'jenis_perintah_kerja' => ['table' => 'jenis_perintah_kerja', 'pk' => 'id', 'title' => 'Jenis Perintah Kerja'],
        'tujuan_perintah_kerja' => ['table' => 'tujuan_perintah_kerja', 'pk' => 'id', 'title' => 'Tujuan Perintah Kerja'],
        'status_eksekusi_workflow' => ['table' => 'status_eksekusi_workflow', 'pk' => 'id', 'title' => 'Status Eksekusi Workflow'],
    ];

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        if (!hasPermission('view_master_data') && !hasPermission('master_data.index.view')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke Master Data.');
        }

        return view('master_data/index', [
            'title' => 'Master Data Center',
            'entities' => $this->buildEntityList(),
        ]);
    }

    public function entities()
    {
        return $this->response->setJSON([
            'success' => true,
            'data' => $this->buildEntityList(),
        ]);
    }

    public function schema(string $entityKey)
    {
        $entity = $this->resolveEntity($entityKey);
        if ($entity === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Entitas tidak ditemukan'])->setStatusCode(404);
        }
        if (!$this->canEntityAction($entityKey, 'view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        if (!$this->db->tableExists($entity['table'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tabel tidak tersedia di environment ini.',
                'table' => $entity['table'],
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'entity' => $entityKey,
                'table' => $entity['table'],
                'pk' => $entity['pk'],
                'title' => $entity['title'],
                'fields' => $this->tableFields($entity['table']),
                'fk' => $entity['fk'] ?? [],
            ],
        ]);
    }

    public function list(string $entityKey)
    {
        $entity = $this->resolveEntity($entityKey);
        if ($entity === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Entitas tidak ditemukan'])->setStatusCode(404);
        }
        if (!$this->canEntityAction($entityKey, 'view')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }
        if (!$this->db->tableExists($entity['table'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tabel tidak tersedia'])->setStatusCode(404);
        }

        $fields = $this->tableFields($entity['table']);
        $effectivePk = $this->resolveEffectivePk($entity['pk'], $fields);
        $limit = max(1, min(500, (int) ($this->request->getGet('limit') ?? 200)));

        $fk = $entity['fk'] ?? [];
        if (!empty($fk)) {
            $tbl = $entity['table'];
            $builder = $this->db->table("{$tbl} t");
            $selectParts = ['t.*'];
            foreach ($fk as $col => $fkDef) {
                $joinAlias = 'fk_' . preg_replace('/[^a-z0-9]/i', '_', $col);
                $selectParts[] = "`{$joinAlias}`.`{$fkDef['label']}` AS `{$col}__label`";
                $builder->join("{$fkDef['table']} {$joinAlias}", "`{$joinAlias}`.`{$fkDef['pk']}` = t.`{$col}`", 'left');
            }
            $builder->select(implode(', ', $selectParts));
            $rows = $builder->orderBy("t.{$effectivePk}", 'DESC')->limit($limit)->get()->getResultArray();
        } else {
            $rows = $this->db->table($entity['table'])->orderBy($effectivePk, 'DESC')->limit($limit)->get()->getResultArray();
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $rows,
            'meta' => ['effective_pk' => $effectivePk],
        ]);
    }

    public function create(string $entityKey)
    {
        $entity = $this->resolveEntity($entityKey);
        if ($entity === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Entitas tidak ditemukan'])->setStatusCode(404);
        }
        if (!$this->canEntityAction($entityKey, 'create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }
        if (!$this->db->tableExists($entity['table'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tabel tidak tersedia'])->setStatusCode(404);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getPost();
        $data = $this->sanitizePayload($entity['table'], $entity['pk'], $payload);
        if (empty($data)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data kosong atau field tidak valid'])->setStatusCode(422);
        }

        try {
            $this->db->table($entity['table'])->insert($data);
            $id = $this->db->insertID();
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dibuat', 'id' => $id]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function update(string $entityKey, string $id)
    {
        $entity = $this->resolveEntity($entityKey);
        if ($entity === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Entitas tidak ditemukan'])->setStatusCode(404);
        }
        if (!$this->canEntityAction($entityKey, 'update')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }
        if (!$this->db->tableExists($entity['table'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tabel tidak tersedia'])->setStatusCode(404);
        }

        $payload = $this->request->getJSON(true) ?? $this->request->getRawInput();
        $data = $this->sanitizePayload($entity['table'], $entity['pk'], $payload);
        if (empty($data)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data kosong atau field tidak valid'])->setStatusCode(422);
        }

        try {
            $this->db->table($entity['table'])->where($entity['pk'], $id)->update($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil diupdate']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    public function delete(string $entityKey, string $id)
    {
        $entity = $this->resolveEntity($entityKey);
        if ($entity === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'Entitas tidak ditemukan'])->setStatusCode(404);
        }
        if (!$this->canEntityAction($entityKey, 'delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }
        if (!$this->db->tableExists($entity['table'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tabel tidak tersedia'])->setStatusCode(404);
        }

        try {
            $this->db->table($entity['table'])->where($entity['pk'], $id)->delete();
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    // Compatibility endpoint for other modules to consume centralized lookups.
    public function options(string $entityKey)
    {
        $entity = $this->resolveEntity($entityKey);
        if ($entity === null || !$this->db->tableExists($entity['table'])) {
            return $this->response->setJSON(['success' => false, 'data' => []])->setStatusCode(404);
        }

        $fields = $this->tableFields($entity['table']);
        $valueField = $this->resolveEffectivePk($entity['pk'], $fields);
        $labelField = $this->guessLabelField($fields, $entity['pk']);

        $rows = $this->db->table($entity['table'])
            ->select("{$valueField} as id, {$labelField} as name")
            ->orderBy($valueField, 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    protected function resolveEntity(string $entityKey): ?array
    {
        $key = strtolower(trim($entityKey));
        return $this->entityRegistry[$key] ?? null;
    }

    protected function buildEntityList(): array
    {
        $list = [];
        foreach ($this->entityRegistry as $key => $config) {
            $list[] = [
                'key' => $key,
                'title' => $config['title'],
                'table' => $config['table'],
                'pk' => $config['pk'],
                'available' => $this->db->tableExists($config['table']),
                'permissions' => [
                    'view' => $this->canEntityAction($key, 'view'),
                    'create' => $this->canEntityAction($key, 'create'),
                    'update' => $this->canEntityAction($key, 'update'),
                    'delete' => $this->canEntityAction($key, 'delete'),
                ],
            ];
        }
        return $list;
    }

    protected function canEntityAction(string $entityKey, string $action): bool
    {
        $entityKey = strtolower($entityKey);
        $keys = [
            "master_data.{$entityKey}.{$action}",
            "master_data.{$entityKey}." . ($action === 'update' ? 'edit' : $action),
            "master_data.{$action}",
            "{$action}_master_data",
            "{$action}_master",
        ];

        foreach ($keys as $perm) {
            if (hasPermission($perm)) {
                return true;
            }
        }

        return false;
    }

    protected function tableFields(string $table): array
    {
        $fieldData = $this->db->getFieldData($table);
        return array_map(static function ($f) {
            return [
                'name' => $f->name,
                'type' => strtolower((string) ($f->type ?? 'varchar')),
                'max_length' => isset($f->max_length) ? (int) $f->max_length : null,
                'nullable' => !empty($f->nullable),
                'default' => $f->default ?? null,
                'primary_key' => !empty($f->primary_key),
            ];
        }, $fieldData);
    }

    protected function sanitizePayload(string $table, string $pk, array $payload): array
    {
        $fields = $this->tableFields($table);
        $allowed = [];
        foreach ($fields as $field) {
            $name = $field['name'];
            if ($name === $pk || $field['primary_key']) {
                continue;
            }
            if (in_array($name, ['created_at', 'updated_at', 'deleted_at'], true)) {
                continue;
            }
            $allowed[$name] = $field;
        }

        $data = [];
        foreach ($allowed as $name => $meta) {
            if (!array_key_exists($name, $payload)) {
                continue;
            }
            $value = $payload[$name];
            if ($value === '' && $meta['nullable']) {
                $value = null;
            }
            $data[$name] = $value;
        }

        return $data;
    }

    protected function guessLabelField(array $fields, string $pk): string
    {
        $priority = [
            'name', 'nama', 'nama_status', 'status_name', 'status_unit',
            'category_name', 'priority_name', 'tipe', 'model_unit', 'merk_unit',
            'kapasitas_unit', 'tipe_mast', 'tipe_ban', 'tipe_roda', 'jumlah_valve',
        ];

        $fieldNames = array_column($fields, 'name');
        foreach ($priority as $candidate) {
            if (in_array($candidate, $fieldNames, true)) {
                return $candidate;
            }
        }

        foreach ($fieldNames as $name) {
            if ($name !== $pk && !str_contains($name, '_id')) {
                return $name;
            }
        }

        return $pk;
    }

    protected function resolveEffectivePk(string $configuredPk, array $fields): string
    {
        $fieldNames = array_column($fields, 'name');
        if (in_array($configuredPk, $fieldNames, true)) {
            return $configuredPk;
        }

        foreach ($fields as $field) {
            if (!empty($field['primary_key'])) {
                return $field['name'];
            }
        }

        return $fieldNames[0] ?? $configuredPk;
    }
}

