<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestEncrypt extends Controller
{
    public function index()
    {
        $e = service('encrypter');
        $plainText = 'Hello World';
        $c = $e->encrypt($plainText);
        $d = $e->decrypt($c);
        echo 'Decrypted value: ' . $d;
    }
}
