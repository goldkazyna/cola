<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['receipts', 'receipts as approved_receipts_count' => function ($q) {
            $q->where('status', 'approved');
        }])->orderBy('created_at', 'desc');

        // Поиск по телефону
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }
}