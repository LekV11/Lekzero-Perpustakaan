import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import '../providers/book_provider.dart';
import '../utils/constants.dart';

class BookFormScreen extends StatefulWidget {
  final Map<String, dynamic>? book;

  const BookFormScreen({super.key, this.book});

  @override
  State<BookFormScreen> createState() => _BookFormScreenState();
}

class _BookFormScreenState extends State<BookFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _titleController;
  late TextEditingController _authorController;
  late TextEditingController _publisherController;
  late TextEditingController _yearController;
  late TextEditingController _descriptionController;
  late TextEditingController _stockController;
  String? _selectedCategoryId;
  File? _imageFile;
  final _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _titleController = TextEditingController(text: widget.book?['title']);
    _authorController = TextEditingController(text: widget.book?['author']);
    _publisherController = TextEditingController(text: widget.book?['publisher']);
    _yearController = TextEditingController(text: widget.book?['year']?.toString());
    _descriptionController = TextEditingController(text: widget.book?['description']);
    _stockController = TextEditingController(text: widget.book?['stock']?.toString());
    _selectedCategoryId = widget.book?['category_id']?.toString();
    
    Future.microtask(() => context.read<BookProvider>().fetchCategories());
  }

  Future<void> _pickImage() async {
    final pickedFile = await _picker.pickImage(source: ImageSource.gallery);
    if (pickedFile != null) {
      setState(() {
        _imageFile = File(pickedFile.path);
      });
    }
  }

  void _submit() async {
    if (_formKey.currentState!.validate()) {
      final body = {
        'title': _titleController.text,
        'author': _authorController.text,
        'publisher': _publisherController.text,
        'year': _yearController.text,
        'description': _descriptionController.text,
        'stock': _stockController.text,
        'category_id': _selectedCategoryId!,
      };

      final provider = context.read<BookProvider>();
      bool success;
      if (widget.book == null) {
        success = await provider.addBook(body, _imageFile);
      } else {
        success = await provider.updateBook(widget.book!['id'], body, _imageFile);
      }

      if (success && mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Berhasil menyimpan data buku")),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.book == null ? "Tambah Buku" : "Edit Buku"),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              Center(
                child: GestureDetector(
                  onTap: _pickImage,
                  child: Container(
                    width: 150,
                    height: 200,
                    decoration: BoxDecoration(
                      color: Colors.grey[200],
                      border: Border.all(color: Colors.grey),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: _imageFile != null
                        ? ClipRRect(
                            borderRadius: BorderRadius.circular(8),
                            child: Image.file(_imageFile!, fit: BoxFit.cover),
                          )
                        : (widget.book != null && widget.book!['cover_path'] != null
                            ? ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: Image.network(
                                  widget.book!['cover_path'].toString().startsWith('http')
                                      ? widget.book!['cover_path']
                                      : "${Constants.storageUrl}/storage/${widget.book!['cover_path']}",
                                  fit: BoxFit.cover,
                                ),
                              )
                            : const Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(Icons.add_a_photo, size: 50),
                                  Text("Pilih Cover"),
                                ],
                              )),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _titleController,
                decoration: const InputDecoration(labelText: "Judul Buku"),
                validator: (v) => v!.isEmpty ? "Wajib diisi" : null,
              ),
              TextFormField(
                controller: _authorController,
                decoration: const InputDecoration(labelText: "Penulis"),
                validator: (v) => v!.isEmpty ? "Wajib diisi" : null,
              ),
              Consumer<BookProvider>(
                builder: (context, provider, child) {
                  return DropdownButtonFormField<String>(
                    value: _selectedCategoryId,
                    decoration: const InputDecoration(labelText: "Kategori"),
                    items: provider.categories.map((c) {
                      return DropdownMenuItem(
                        value: c['id'].toString(),
                        child: Text(c['name']),
                      );
                    }).toList(),
                    onChanged: (v) => setState(() => _selectedCategoryId = v),
                    validator: (v) => v == null ? "Wajib pilih" : null,
                  );
                },
              ),
              TextFormField(
                controller: _stockController,
                decoration: const InputDecoration(labelText: "Stok"),
                keyboardType: TextInputType.number,
                validator: (v) => v!.isEmpty ? "Wajib diisi" : null,
              ),
              TextFormField(
                controller: _publisherController,
                decoration: const InputDecoration(labelText: "Penerbit"),
              ),
              TextFormField(
                controller: _yearController,
                decoration: const InputDecoration(labelText: "Tahun Terbit"),
                keyboardType: TextInputType.number,
              ),
              TextFormField(
                controller: _descriptionController,
                decoration: const InputDecoration(labelText: "Deskripsi"),
                maxLines: 3,
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: _submit,
                style: ElevatedButton.styleFrom(minimumSize: const Size(double.infinity, 50)),
                child: const Text("Simpan"),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
