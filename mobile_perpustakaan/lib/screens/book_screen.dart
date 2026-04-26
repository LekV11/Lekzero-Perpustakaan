import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/book_provider.dart';
import '../utils/constants.dart';
import 'book_form_screen.dart';

class BookScreen extends StatefulWidget {
  final bool isAdmin;
  const BookScreen({super.key, this.isAdmin = false});

  @override
  State<BookScreen> createState() => _BookScreenState();
}

class _BookScreenState extends State<BookScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => context.read<BookProvider>().fetchBooks());
  }

  void _deleteBook(int id) async {
    final success = await context.read<BookProvider>().deleteBook(id);
    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Buku berhasil dihapus")),
      );
    }
  }

  void _showDeleteDialog(int id) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Hapus Buku"),
        content: const Text("Apakah Anda yakin ingin menghapus buku ini?"),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              _deleteBook(id);
            },
            child: const Text("Hapus", style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<BookProvider>(
      builder: (context, provider, child) {
        if (provider.state == BookState.loading) {
          return const Center(child: CircularProgressIndicator());
        }

        return Scaffold(
          body: RefreshIndicator(
            onRefresh: () => provider.fetchBooks(),
            child: ListView.builder(
              itemCount: provider.books.length,
              itemBuilder: (context, index) {
                final book = provider.books[index];
                return Card(
                  margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: ListTile(
                    leading: Container(
                      width: 50,
                      height: 70,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(4),
                        color: Colors.grey[200],
                      ),
                      child: book['cover_path'] != null
                          ? ClipRRect(
                              borderRadius: BorderRadius.circular(4),
                              child: Image.network(
                                book['cover_path'].toString().startsWith('http') 
                                    ? book['cover_path'] 
                                    : "${Constants.storageUrl}/storage/${book['cover_path']}",
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) => const Icon(Icons.book),
                              ),
                            )
                          : const Icon(Icons.book),
                    ),
                    title: Text(
                      book['title'] ?? '-',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          "${book['author']} | Stok: ${book['stock']}",
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        if (book['description'] != null && book['description'].toString().isNotEmpty)
                          Text(
                            book['description'],
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontSize: 12, color: Colors.grey),
                          ),
                      ],
                    ),
                    trailing: widget.isAdmin ? Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.edit, color: Colors.blue),
                          onPressed: () => Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => BookFormScreen(book: book)),
                          ),
                        ),
                        IconButton(
                          icon: const Icon(Icons.delete, color: Colors.red),
                          onPressed: () => _showDeleteDialog(book['id']),
                        ),
                      ],
                    ) : SizedBox(
                      width: 60,
                      child: Text(
                        book['category']?['name'] ?? '-',
                        textAlign: TextAlign.end,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontSize: 12),
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
          floatingActionButton: widget.isAdmin ? FloatingActionButton(
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => const BookFormScreen()),
            ),
            heroTag: 'book_fab',
            child: const Icon(Icons.add),
          ) : null,
        );
      },
    );
  }
}
