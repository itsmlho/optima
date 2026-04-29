<?php

namespace App\Controllers;

use App\Models\CompanyFeedbackModel;

class MasukanKeluhanPublicController extends BaseController
{
    protected CompanyFeedbackModel $feedbackModel;

    public function __construct()
    {
        $this->feedbackModel = new CompanyFeedbackModel();
    }

    public function index()
    {
        return view('public/masukan_keluhan', [
            'title'       => 'Masukan & Keluh Kesah — PT Sarana Mitra Luas Tbk',
            'companyName' => 'PT Sarana Mitra Luas Tbk',
            'logoUrl'     => base_url('assets/images/company-logo.svg'),
            'formAction'  => base_url('masukan-keluhan/kirim'),
        ]);
    }

    /**
     * Halaman print QR/Barcode untuk ditempel (akses publik).
     */
    public function printPage()
    {
        $formUrl = base_url('masukan-keluhan');
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . rawurlencode($formUrl);

        return view('public/masukan_keluhan_print', [
            'title'      => 'Print QR Masukan & Keluh Kesah',
            'companyName' => 'PT Sarana Mitra Luas Tbk',
            'formUrl'     => $formUrl,
            'qrUrl'       => $qrUrl,
            'smlLogoUrl'  => base_url('assets/images/company-logo.svg'),
            'optimaLogoUrl' => base_url('assets/images/logo-optima.png'),
        ]);
    }

    /**
     * Simpan masukan anonim (opsional: kontak untuk tindak lanjut).
     */
    public function kirim()
    {
        // Honeypot: bot mengisi field tersembunyi
        $trap = trim((string) $this->request->getPost('company_website'));
        if ($trap !== '') {
            return redirect()->to('/masukan-keluhan')->with('success', 'Terima kasih, masukan Anda telah kami terima.');
        }

        $rules = [
            'type'    => 'required|in_list[masukan,keluh_kesah]',
            'message' => 'required|min_length[10]|max_length[10000]',
            'contact_email' => 'permit_empty|valid_email|max_length[255]',
            'contact_phone' => 'permit_empty|max_length[50]',
        ];

        $messages = [
            'type' => [
                'required' => 'Silakan pilih jenis: Masukan atau Keluh kesah.',
                'in_list'  => 'Jenis tidak valid.',
            ],
            'message' => [
                'required'   => 'Silakan tulis pesan Anda.',
                'min_length' => 'Pesan minimal 10 karakter agar lebih jelas.',
                'max_length' => 'Pesan terlalu panjang.',
            ],
            'contact_email' => [
                'valid_email' => 'Format email tidak valid.',
                'max_length'  => 'Email terlalu panjang.',
            ],
            'contact_phone' => [
                'max_length' => 'Nomor telepon terlalu panjang.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = trim((string) $this->request->getPost('contact_email'));
        $phone = trim((string) $this->request->getPost('contact_phone'));
        $email = $email === '' ? null : $email;
        $phone = $phone === '' ? null : $phone;

        $data = [
            'type'           => $this->request->getPost('type'),
            'message'        => trim((string) $this->request->getPost('message')),
            'contact_email'  => $email,
            'contact_phone'  => $phone,
            'is_anonymous'   => 1,
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        try {
            $this->feedbackModel->skipValidation(true)->insert($data);
        } catch (\Throwable $e) {
            log_message('error', 'company_feedback insert: ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', 'Maaf, sistem sedang sibuk. Silakan coba lagi nanti.');
        }

        return redirect()->to('/masukan-keluhan')->with('success', 'Terima kasih. Masukan Anda telah terkirim.');
    }
}
