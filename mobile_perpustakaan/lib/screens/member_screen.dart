import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/member_provider.dart';
import 'member_form_screen.dart';

class MemberScreen extends StatefulWidget {
  final bool isAdmin;
  const MemberScreen({super.key, this.isAdmin = false});

  @override
  State<MemberScreen> createState() => _MemberScreenState();
}

class _MemberScreenState extends State<MemberScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => context.read<MemberProvider>().fetchMembers());
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<MemberProvider>(
      builder: (context, provider, child) {
        if (provider.state == MemberState.loading) {
          return const Center(child: CircularProgressIndicator());
        }

        return Scaffold(
          body: RefreshIndicator(
            onRefresh: () => provider.fetchMembers(),
            child: ListView.builder(
              itemCount: provider.members.length,
              itemBuilder: (context, index) {
                final member = provider.members[index];
                return Card(
                  margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: ListTile(
                    leading: const CircleAvatar(child: Icon(Icons.person)),
                    title: Text(member['name'] ?? '-'),
                    subtitle: Text("ID: ${member['member_id']}"),
                    trailing: widget.isAdmin ? Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        IconButton(
                          icon: const Icon(Icons.edit, color: Colors.blue),
                          onPressed: () => Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => MemberFormScreen(member: member)),
                          ),
                        ),
                        IconButton(
                          icon: const Icon(Icons.delete, color: Colors.red),
                          onPressed: () => _showDeleteDialog(member['id']),
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
              MaterialPageRoute(builder: (context) => const MemberFormScreen()),
            ),
            heroTag: 'member_fab',
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
        title: const Text("Hapus Anggota"),
        content: const Text("Apakah Anda yakin ingin menghapus anggota ini?"),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          TextButton(
            onPressed: () async {
              Navigator.pop(context);
              final success = await context.read<MemberProvider>().deleteMember(id);
              if (success && mounted) {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(content: Text("Anggota berhasil dihapus")),
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

