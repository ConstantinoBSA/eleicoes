<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->view('index');
    }

    public function teste()
    {
        $testando = 'Testando passagem';

        $this->view('teste', [
            'testando' => $testando
        ]);
    }
}
