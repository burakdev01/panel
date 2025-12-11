<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;


class Auth extends BaseController
{
    public function login()
    {
        return view('admin/login');
    }

    public function default()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        return redirect()->to('/admin/login');
    }

    public function loginPost()
    {
        
        // POST verileri
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        // Model
       
        $adminModel = new \App\Models\AdminModel();
 
        $admin = $adminModel->where('username', $username)->first();

        // Kullanıcı bulunamadı
        if (!$admin) {
            return redirect()->to('/admin/login')
                             ->with('error', 'Kullanıcı adı bulunamadı! maybe');
        }

        // Şifre yanlış
        if (!password_verify($password, $admin['password'])) {
            return redirect()->to('/admin/login')
                             ->with('error', 'Şifre yanlış!');
        }

        // Session başlat
        session()->set([
            'id'       => $admin['id'],
            'username' => $admin['username'],
            'isLoggedIn'     => true
        ]);

        // last_login güncelle
        $adminModel->update($admin['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        // Dashboard'a yönlendir
        return redirect()->to('/admin/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}