<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->get();
        return $this->sendResponse($books, 'Books retrieved successfully.');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $input = $request->except('cover');

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('books', 'public');
            $input['cover_path'] = $path;
        }

        $book = Book::create($input);
        return $this->sendResponse($book->load('category'), 'Book created successfully.', 201);
    }

    public function show(Book $book)
    {
        return $this->sendResponse($book->load('category'), 'Book retrieved successfully.');
    }

    public function update(Request $request, Book $book)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'author' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|required|integer|min:0',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $input = $request->except('cover');

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $path = $request->file('cover')->store('books', 'public');
            $input['cover_path'] = $path;
        }

        $book->update($input);
        return $this->sendResponse($book->load('category'), 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }
        $book->delete();
        return $this->sendResponse([], 'Book deleted successfully.', 200);
    }
}
