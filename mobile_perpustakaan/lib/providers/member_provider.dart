import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../utils/constants.dart';
import '../services/auth_service.dart';

enum MemberState { loading, error, success }

class MemberProvider extends ChangeNotifier {
  List<dynamic> _members = [];
  MemberState _state = MemberState.loading;
  String _errorMessage = '';

  List<dynamic> get members => _members;
  MemberState get state => _state;
  String get errorMessage => _errorMessage;

  final AuthService _authService = AuthService();

  Future<void> fetchMembers() async {
    _state = MemberState.loading;
    _errorMessage = '';
    notifyListeners();

    try {
      final token = await _authService.getToken();
      final response = await http.get(
        Uri.parse('${Constants.baseUrl}/members'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['status'] == 'success') {
        _members = data['data'];
        _state = MemberState.success;
      } else {
        _state = MemberState.error;
        _errorMessage = data['message'] ?? 'Gagal mengambil data anggota.';
      }
    } catch (e) {
      _state = MemberState.error;
      _errorMessage = 'Gagal terhubung ke server.';
    } finally {
      notifyListeners();
    }
  }

  Future<bool> addMember(Map<String, String> body) async {
    try {
      final token = await _authService.getToken();
      final response = await http.post(
        Uri.parse('${Constants.baseUrl}/members'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(body),
      );

      if (response.statusCode == 201) {
        await fetchMembers();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<bool> updateMember(int id, Map<String, String> body) async {
    try {
      final token = await _authService.getToken();
      final response = await http.put(
        Uri.parse('${Constants.baseUrl}/members/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(body),
      );

      if (response.statusCode == 200) {
        await fetchMembers();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }

  Future<bool> deleteMember(int id) async {
    try {
      final token = await _authService.getToken();
      final response = await http.delete(
        Uri.parse('${Constants.baseUrl}/members/$id'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 204 || response.statusCode == 200) {
        await fetchMembers();
        return true;
      }
      return false;
    } catch (e) {
      return false;
    }
  }
}
