import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../utils/constants.dart';
import '../services/auth_service.dart';

enum LoanState { loading, error, success }

class LoanProvider extends ChangeNotifier {
  List<dynamic> _loans = [];
  LoanState _state = LoanState.loading;
  String _errorMessage = '';

  List<dynamic> get loans => _loans;
  LoanState get state => _state;
  String get errorMessage => _errorMessage;

  final AuthService _authService = AuthService();

  Future<void> fetchLoans() async {
    _state = LoanState.loading;
    _errorMessage = '';
    notifyListeners();

    try {
      final token = await _authService.getToken();
      final response = await http.get(
        Uri.parse('${Constants.baseUrl}/loans'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['status'] == 'success') {
        _loans = data['data'];
        _state = LoanState.success;
      } else {
        _state = LoanState.error;
        _errorMessage = data['message'] ?? 'Gagal mengambil data peminjaman.';
      }
    } catch (e) {
      _state = LoanState.error;
      _errorMessage = 'Gagal terhubung ke server.';
    } finally {
      notifyListeners();
    }
  }

  Future<bool> addLoan(Map<String, dynamic> body) async {
    try {
      final token = await _authService.getToken();
      final response = await http.post(
        Uri.parse('${Constants.baseUrl}/loans'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(body),
      );

      if (response.statusCode == 201) {
        await fetchLoans();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updateLoan(int id, Map<String, dynamic> body) async {
    try {
      final token = await _authService.getToken();
      final response = await http.put(
        Uri.parse('${Constants.baseUrl}/loans/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(body),
      );

      if (response.statusCode == 200) {
        await fetchLoans();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<bool> deleteLoan(int id) async {
    try {
      final token = await _authService.getToken();
      final response = await http.delete(
        Uri.parse('${Constants.baseUrl}/loans/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 204 || response.statusCode == 200) {
        await fetchLoans();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
}
