<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;

class AuthController extends BaseController
{
        protected $user;
        
        function __construct()
        {
            helper('form');
            $this->user= new UserModel();
        }

    public function login()
    {
        // Jika sudah login admin, destroy session agar bisa login customer di window lain
        if (session()->get('isLoggedIn') && session()->get('role') === 'admin') {
            session()->destroy();
        } elseif (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/'));
        }

        if ($this->request->getPost()) {
            $rules = [
                'username' => 'required|min_length[3]',
                'password' => 'required|min_length[6]', // perbaikan: tidak harus numeric, cukup minimal 6 karakter
            ];

            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');

                $dataUser = $this->user->where(['username' => $username])->first(); //pasw 1234567

                if ($dataUser) {
                    if (password_verify($password, $dataUser['password'])) {
                        session()->set([
                            'username' => $dataUser['username'],
                            'role' => $dataUser['role'],
                            'isLoggedIn' => TRUE
                        ]);

                        return redirect()->to(base_url('/'));
                    } else {
                        session()->setFlashdata('failed', 'Kombinasi Username & Password Salah');
                        return redirect()->back();
                    }
                } else {
                    session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back();
            }
        }

        return view('v_login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}
