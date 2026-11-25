<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('notificaciones.index', [
            'notificaciones' => $request->user()->notifications()->paginate(15),
        ]);
    }

    public function leer(Request $request, string $id)
    {
        $n = $request->user()->notifications()->findOrFail($id);
        $n->markAsRead();

        $url = $n->data['url'] ?? null;

        return $url ? redirect()->to($url) : back();
    }

    public function leerTodas(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
