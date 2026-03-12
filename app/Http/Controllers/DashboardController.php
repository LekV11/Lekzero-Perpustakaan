<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            // regular user sees only their own loans (match by member email)
            $data = [
                'my_loans' => Loan::whereHas('member', function($q) {
                    $q->where('email', auth()->user()->email);
                })->count(),
            ];
        } else {
            $data = [
                'books_count' => Book::count(),
                'members_count' => Member::count(),
                'borrowed_count' => Loan::where('status', 'borrowed')->count(),
                'available_count' => Book::sum('stock') - Loan::where('status','borrowed')->count(),
            ];
        }

        return view('dashboard', $data);
    }
}
