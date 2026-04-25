import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/category_provider.dart';

class CategoryScreen extends StatefulWidget {
  final bool isAdmin;
  const CategoryScreen({super.key, this.isAdmin = false});

  @override
  State<CategoryScreen> createState() => CategoryScreenState();
}

class CategoryScreenState extends State<CategoryScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => context.read<CategoryProvider>().fetchCategories());
  }

  void showForm([Map<String, dynamic>? category]) {
    final controller = TextEditingController(text: category?['name']);
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: Text(category == null ? "Tambah Genre" : "Edit Genre"),
        content: TextField(
          controller: controller,
          decoration: const InputDecoration(labelText: "Nama Genre"),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          ElevatedButton(
            onPressed: () async {
              if (controller.text.isEmpty) return;
              final provider = context.read<CategoryProvider>();
              bool success;
              if (category == null) {
                success = await provider.addCategory(controller.text);
              } else {
                success = await provider.updateCategory(category['id'], controller.text);
              }
              if (success && mounted) {
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text("Berhasil menyimpan genre")),
                );
              }
            },
            child: const Text("Simpan"),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Consumer<CategoryProvider>(
        builder: (context, provider, child) {
          if (provider.state == CategoryState.loading) {
            return const Center(child: CircularProgressIndicator());
          }

          return RefreshIndicator(
            onRefresh: () => provider.fetchCategories(),
            child: ListView.builder(
              itemCount: provider.categories.length,
              itemBuilder: (context, index) {
                final cat = provider.categories[index];
                return Card(
                  margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: ListTile(
                    title: Text(cat['name'] ?? '-'),
                    trailing: widget.isAdmin ? Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.edit, color: Colors.blue),
                          onPressed: () => showForm(cat),
                        ),
                        IconButton(
                          icon: const Icon(Icons.delete, color: Colors.red),
                          onPressed: () {
                            showDialog(
                              context: context,
                              builder: (context) => AlertDialog(
                                title: const Text("Hapus Genre"),
                                content: const Text("Hapus genre ini?"),
                                actions: [
                                  TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
                                  TextButton(
                                    onPressed: () async {
                                      Navigator.pop(context);
                                      await provider.deleteCategory(cat['id']);
                                    },
                                    child: const Text("Hapus", style: TextStyle(color: Colors.red)),
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
                      ],
                    ) : null,
                  ),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: widget.isAdmin ? FloatingActionButton(
        onPressed: () => showForm(),
        heroTag: 'category_fab',
        child: const Icon(Icons.add),
      ) : null,
    );
  }
}
