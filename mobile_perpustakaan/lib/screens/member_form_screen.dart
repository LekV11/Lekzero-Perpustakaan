import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/member_provider.dart';

class MemberFormScreen extends StatefulWidget {
  final Map<String, dynamic>? member;

  const MemberFormScreen({super.key, this.member});

  @override
  State<MemberFormScreen> createState() => _MemberFormScreenState();
}

class _MemberFormScreenState extends State<MemberFormScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _memberIdController;
  late TextEditingController _addressController;
  late TextEditingController _phoneController;
  late TextEditingController _emailController;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.member?['name']);
    _memberIdController = TextEditingController(text: widget.member?['member_id']);
    _addressController = TextEditingController(text: widget.member?['address']);
    _phoneController = TextEditingController(text: widget.member?['phone']);
    _emailController = TextEditingController(text: widget.member?['email']);
  }

  void _submit() async {
    if (_formKey.currentState!.validate()) {
      final body = {
        'name': _nameController.text,
        'member_id': _memberIdController.text,
        'address': _addressController.text,
        'phone': _phoneController.text,
        'email': _emailController.text,
      };

      final provider = context.read<MemberProvider>();
      bool success;
      if (widget.member == null) {
        success = await provider.addMember(body);
      } else {
        success = await provider.updateMember(widget.member!['id'], body);
      }

      if (success && mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Berhasil menyimpan data anggota")),
        );
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Gagal menyimpan data anggota. Pastikan ID Anggota unik.")),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.member == null ? "Tambah Anggota" : "Edit Anggota"),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: ListView(
            children: [
              TextFormField(
                controller: _nameController,
                decoration: const InputDecoration(labelText: "Nama Lengkap"),
                validator: (v) => v!.isEmpty ? "Wajib diisi" : null,
              ),
              TextFormField(
                controller: _memberIdController,
                decoration: const InputDecoration(labelText: "ID Anggota (NIM/NIK)"),
                validator: (v) => v!.isEmpty ? "Wajib diisi" : null,
              ),
              TextFormField(
                controller: _emailController,
                decoration: const InputDecoration(labelText: "Email"),
                keyboardType: TextInputType.emailAddress,
              ),
              TextFormField(
                controller: _phoneController,
                decoration: const InputDecoration(labelText: "Nomor Telepon"),
                keyboardType: TextInputType.phone,
              ),
              TextFormField(
                controller: _addressController,
                decoration: const InputDecoration(labelText: "Alamat"),
                maxLines: 2,
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
