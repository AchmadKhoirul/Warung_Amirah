<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthAdminController extends BaseController
{
    protected $user;
    public function __construct()
    {
        helper('form');
        $this->user = new UserModel();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            // Jika sudah login sebagai customer, logout dulu agar bisa login admin
            if (session()->get('role') !== 'admin') {
                session()->destroy();
            } else {
                return redirect()->to(base_url('/admin'));
            }
        }

        if ($this->request->getPost()) {
            $rules = [
                'username' => 'required|min_length[3]',
                'password' => 'required|min_length[6]',
            ];
            if ($this->validate($rules)) {
                $username = $this->request->getVar('username');
                $password = $this->request->getVar('password');
                $dataUser = $this->user->where(['username' => $username, 'role' => 'admin'])->first();
                if ($dataUser) {
                    if (password_verify($password, $dataUser['password'])) {
                        session()->set([
                            'username' => $dataUser['username'],
                            'role' => $dataUser['role'],
                            'isLoggedIn' => TRUE,
                            'isAdmin' => TRUE
                        ]);
                        return redirect()->to(base_url('/admin'));
                    } else {
                        session()->setFlashdata('failed', 'Password salah');
                        return redirect()->back();
                    }
                } else {
                    session()->setFlashdata('failed', 'Username admin tidak ditemukan');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', $this->validator->listErrors());
                return redirect()->back();
            }
        }
        return view('admin/v_login_admin');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}
