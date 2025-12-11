<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class GuestCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Eğer login olduysa login sayfasına girmesin
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kullanılmayacak
    }
}