import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/loan_provider.dart';
import 'loan_form_screen.dart';

class LoanScreen extends StatefulWidget {
  final bool isAdmin;
  const LoanScreen({super.key, this.isAdmin = false});

  @override
  State<LoanScreen> createState() => _LoanScreenState();
}

class _LoanScreenState extends State<LoanScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => context.read<LoanProvider>().fetchLoans());
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<LoanProvider>(
      builder: (context, provider, child) {
        if (provider.state == LoanState.loading) {
          return const Center(child: CircularProgressIndicator());
        }

        return Scaffold(
          body: RefreshIndicator(
            onRefresh: () => provider.fetchLoans(),
            child: ListView.builder(
              itemCount: provider.loans.length,
              itemBuilder: (context, index) {
                final loan = provider.loans[index];
                return Card(
                  margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: ListTile(
                    leading: const CircleAvatar(child: Icon(Icons.history_edu)),
                    title: Text(
                      loan['book']?['title'] ?? 'Buku Tidak Diketahui',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontWeight: FontWeight.bold),
                    ),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          "Peminjam: ${loan['member']?['name'] ?? '-'}",
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                        Text("Tgl Pinjam: ${loan['loan_date']}"),
                        const SizedBox(height: 4),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(
                            color: loan['status'] == 'returned' ? Colors.green : Colors.orange,
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            loan['status'] == 'returned' ? 'Dikembalikan' : 'Dipinjam',
                            style: const TextStyle(fontSize: 10, color: Colors.white, fontWeight: FontWeight.bold),
                          ),
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
                            MaterialPageRoute(builder: (context) => LoanFormScreen(loan: loan)),
                          ),
                        ),
                        IconButton(
                          icon: const Icon(Icons.delete, color: Colors.red),
                          onPressed: () => _showDeleteDialog(loan['id']),
                        ),
                      ],
                    ) : null,
                  ),
                );
              },
            ),
          ),
          floatingActionButton: widget.isAdmin ? FloatingActionButton(
            onPressed: () => Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => const LoanFormScreen()),
            ),
            heroTag: 'loan_fab',
            child: const Icon(Icons.add),
          ) : null,
        );
      },
    );
  }

  void _showDeleteDialog(int id) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Hapus Peminjaman"),
        content: const Text("Apakah Anda yakin ingin menghapus data ini?"),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              final success = await context.read<LoanProvider>().deleteLoan(id);
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text("Data peminjaman berhasil dihapus")),
                );
              }
            },
            child: const Text("Hapus", style: TextStyle(color: Colors.red)),
          ),
        ],
      ),
    );
  }
}
