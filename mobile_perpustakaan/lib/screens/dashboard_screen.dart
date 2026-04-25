import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../services/auth_service.dart';
import '../providers/category_provider.dart';
import 'login_screen.dart';
import 'member_screen.dart';
import 'loan_screen.dart';
import 'book_screen.dart';
import 'category_screen.dart';
import 'book_form_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;
  String? _role;

  final List<String> _titles = [
    "Katalog Buku",
    "Daftar Anggota",
    "Riwayat Pinjam",
    "Genre Buku",
  ];

  @override
  void initState() {
    super.initState();
    _checkRole();
  }

  void _checkRole() async {
    final role = await AuthService().getRole();
    setState(() {
      _role = role;
    });
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  void _handleLogout() async {
    await AuthService().logout();
    if (mounted) {
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> _pages = [
      BookScreen(isAdmin: _role == 'admin'),
      MemberScreen(isAdmin: _role == 'admin'),
      LoanScreen(isAdmin: _role == 'admin'),
      CategoryScreen(isAdmin: _role == 'admin'),
    ];

    return Scaffold(
      appBar: AppBar(
        title: Text(_titles[_selectedIndex]),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: _handleLogout,
          )
        ],
      ),
      body: _pages[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        type: BottomNavigationBarType.fixed,
        items: const <BottomNavigationBarItem>[
          BottomNavigationBarItem(icon: Icon(Icons.book), label: 'Buku'),
          BottomNavigationBarItem(icon: Icon(Icons.people), label: 'Anggota'),
          BottomNavigationBarItem(icon: Icon(Icons.history), label: 'Pinjam'),
          BottomNavigationBarItem(icon: Icon(Icons.category), label: 'Genre'),
        ],
        currentIndex: _selectedIndex,
        onTap: _onItemTapped,
      ),
    );
  }
}
