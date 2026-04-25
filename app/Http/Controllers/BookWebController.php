<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookWebController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->only(['create','store','edit','update','destroy']);
    }
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = Book::with('category');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $books = $query->latest()->paginate(12);
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|digits:4|integer',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'cover' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $data['cover_path'] = $path;
        }

        Book::create($data);
        return redirect()->route('books.index')->with('success', 'Buku ditambahkan');
    }

    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|digits:4|integer',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'cover' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $data['cover_path'] = $path;
        }

        $book->update($data);
        return redirect()->route('books.index')->with('success', 'Buku diperbarui');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Buku dihapus');
    }

    public function show(Book $book)
    {
        return redirect()->route('books.index');
    }
}
