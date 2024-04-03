<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private function redirect_url()
    {
        $stock = Stock::whereName('MAKE STOCK')->first();

        return route('admin.orders.index', ['stock' => $stock->id]);
    }
    public function index(Request $request)
    {
        if (Auth::check()) {
            return redirect($this->redirect_url());
        } else {
            return view('backend.login.index');
        }
    }

    public function login(Request $request)
    {
        $login = [
            'email' => $request->username,
            'password' => $request->pass,
        ];
        if (Auth::attempt($login)) {
            return redirect($this->redirect_url());
        } else {
            return redirect()->back()->withFlashDanger('Wrong username or password!');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('admin.login.index');
    }
}
