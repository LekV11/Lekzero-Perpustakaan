import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/loan_provider.dart';
import '../providers/book_provider.dart';
import '../providers/member_provider.dart';
import 'package:intl/intl.dart';

class LoanFormScreen extends StatefulWidget {
  final Map<String, dynamic>? loan;

  const LoanFormScreen({super.key, this.loan});

  @override
  State<LoanFormScreen> createState() => _LoanFormScreenState();
}

class _LoanFormScreenState extends State<LoanFormScreen> {
  final _formKey = GlobalKey<FormState>();
  String? _selectedBookId;
  String? _selectedMemberId;
  DateTime _loanDate = DateTime.now();
  DateTime? _returnDate;
  String _status = 'borrowed';

  @override
  void initState() {
    super.initState();
    if (widget.loan != null) {
      _selectedBookId = widget.loan!['book_id']?.toString();
      _selectedMemberId = widget.loan!['member_id']?.toString();
      _loanDate = DateTime.parse(widget.loan!['loan_date']);
      if (widget.loan!['return_date'] != null) {
        _returnDate = DateTime.parse(widget.loan!['return_date']);
      }
      _status = widget.loan!['status'] ?? 'borrowed';
    }
    
    Future.microtask(() {
      context.read<BookProvider>().fetchBooks();
      context.read<MemberProvider>().fetchMembers();
    });
  }

  void _submit() async {
    if (_formKey.currentState!.validate()) {
      final body = {
        'book_id': _selectedBookId,
        'member_id': _selectedMemberId,
        'loan_date': DateFormat('yyyy-MM-dd').format(_loanDate),
        'return_date': _returnDate != null ? DateFormat('yyyy-MM-dd').format(_returnDate!) : null,
        'status': _status,
      };

      final provider = context.read<LoanProvider>();
      bool success;
      if (widget.loan == null) {
        success = await provider.addLoan(body);
      } else {
        success = await provider.updateLoan(widget.loan!['id'], body);
      }

      if (success && mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Berhasil menyimpan data peminjaman")),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.loan == null ? "Tambah Peminjaman" : "Edit Peminjaman"),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              Consumer<BookProvider>(
                builder: (context, provider, child) {
                  return DropdownButtonFormField<String>(
                    isExpanded: true,
                    value: _selectedBookId,
                    decoration: const InputDecoration(labelText: "Buku"),
                    items: provider.books.map((b) {
                      return DropdownMenuItem(
                        value: b['id'].toString(),
                        child: Text(
                          b['title'],
                          overflow: TextOverflow.ellipsis,
                        ),
                      );
                    }).toList(),
                    onChanged: (v) => setState(() => _selectedBookId = v),
                    validator: (v) => v == null ? "Wajib pilih" : null,
                  );
                },
              ),
              Consumer<MemberProvider>(
                builder: (context, provider, child) {
                  return DropdownButtonFormField<String>(
                    isExpanded: true,
                    value: _selectedMemberId,
                    decoration: const InputDecoration(labelText: "Anggota"),
                    items: provider.members.map((m) {
                      return DropdownMenuItem(
                        value: m['id'].toString(),
                        child: Text(
                          m['name'],
                          overflow: TextOverflow.ellipsis,
                        ),
                      );
                    }).toList(),
                    onChanged: (v) => setState(() => _selectedMemberId = v),
                    validator: (v) => v == null ? "Wajib pilih" : null,
                  );
                },
              ),
              const SizedBox(height: 16),
              Card(
                child: Column(
                  children: [
                    ListTile(
                      leading: const Icon(Icons.calendar_today, color: Colors.blue),
                      title: const Text("Tanggal Pinjam"),
                      subtitle: Text(DateFormat('dd MMMM yyyy').format(_loanDate)),
                      onTap: () async {
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: _loanDate,
                          firstDate: DateTime(2000),
                          lastDate: DateTime(2100),
                        );
                        if (picked != null) setState(() => _loanDate = picked);
                      },
                    ),
                    const Divider(height: 1),
                    ListTile(
                      leading: const Icon(Icons.event_available, color: Colors.green),
                      title: const Text("Tanggal Kembali"),
                      subtitle: Text(_returnDate != null ? DateFormat('dd MMMM yyyy').format(_returnDate!) : "Belum Kembali"),
                      onTap: () async {
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: _returnDate ?? DateTime.now(),
                          firstDate: DateTime(2000),
                          lastDate: DateTime(2100),
                        );
                        if (picked != null) setState(() => _returnDate = picked);
                      },
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<String>(
                value: _status,
                decoration: const InputDecoration(labelText: "Status"),
                items: const [
                  DropdownMenuItem(value: 'borrowed', child: Text("Dipinjam")),
                  DropdownMenuItem(value: 'returned', child: Text("Dikembalikan")),
                ],
                onChanged: (v) => setState(() => _status = v!),
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
