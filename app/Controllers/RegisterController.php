<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class RegisterController extends BaseController
{
    public function index()
    {
        return view('register'); // atau view('register') jika kamu tidak pakai folder "auth"
    }

    public function store()
    {
        helper(['form']);

        $rules = [
            'username' => 'required|min_length[4]|is_unique[user.username]',
            'email'    => 'required|valid_email|is_unique[user.email]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'user',
        ]);

        return redirect()->to('login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
