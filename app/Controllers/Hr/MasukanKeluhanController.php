<?php

namespace App\Controllers\Hr;

use App\Controllers\BaseController;
use App\Models\CompanyFeedbackModel;

class MasukanKeluhanController extends BaseController
{
    protected CompanyFeedbackModel $feedbackModel;

    public function __construct()
    {
        $this->feedbackModel = new CompanyFeedbackModel();
    }

    public function index()
    {
        return view('hr/masukan_keluhan_index', [
            'title' => 'Masukan & Keluh Kesah',
        ]);
    }

    /**
     * DataTables server-side JSON.
     */
    public function getData()
    {
        $request = $this->request;
        $draw    = (int) ($request->getPost('draw') ?? 1);
        $start   = (int) ($request->getPost('start') ?? 0);
        $length  = (int) ($request->getPost('length') ?? 10);
        $length  = max(1, min(100, $length));

        $searchData = $request->getPost('search');
        $search     = isset($searchData['value']) ? trim((string) $searchData['value']) : '';

        $totalRecords = $this->feedbackModel->countAllResults();

        $filteredBuilder = $this->feedbackModel->builder();
        if ($search !== '') {
            $filteredBuilder->groupStart()
                ->like('message', $search)
                ->orLike('contact_email', $search)
                ->orLike('contact_phone', $search)
                ->groupEnd();
        }
        $recordsFiltered = $filteredBuilder->countAllResults();

        $order = $request->getPost('order');
        $dir   = 'desc';
        if (! empty($order[0]['dir']) && strtolower((string) $order[0]['dir']) === 'asc') {
            $dir = 'asc';
        }

        $dataBuilder = $this->feedbackModel->builder();
        if ($search !== '') {
            $dataBuilder->groupStart()
                ->like('message', $search)
                ->orLike('contact_email', $search)
                ->orLike('contact_phone', $search)
                ->groupEnd();
        }

        $rows = $dataBuilder
            ->orderBy('created_at', $dir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        $h = static function (string $s): string {
            return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };

        $data = [];
        foreach ($rows as $row) {
            $typeLabel = ($row['type'] ?? '') === 'keluh_kesah' ? 'Keluh kesah' : 'Masukan';
            $msg       = (string) ($row['message'] ?? '');
            $snippet   = mb_strlen($msg) > 160 ? mb_substr($msg, 0, 160) . '…' : $msg;

            $contactParts = [];
            if (! empty($row['contact_email'])) {
                $contactParts[] = $h($row['contact_email']);
            }
            if (! empty($row['contact_phone'])) {
                $contactParts[] = $h($row['contact_phone']);
            }
            $contactHtml = $contactParts !== [] ? implode('<br>', $contactParts) : '<span class="text-muted">—</span>';

            $data[] = [
                'type_label'    => $typeLabel,
                'message_snip'  => $snippet,
                'message_plain' => $msg,
                'contact'       => $contactHtml,
                'created_at'    => $row['created_at'] ?? '',
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
