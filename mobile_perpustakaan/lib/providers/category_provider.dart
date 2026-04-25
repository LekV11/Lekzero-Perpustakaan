import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import '../utils/constants.dart';
import '../services/auth_service.dart';

enum CategoryState { loading, error, success }

class CategoryProvider extends ChangeNotifier {
  List<dynamic> _categories = [];
  CategoryState _state = CategoryState.loading;
  String _errorMessage = '';

  List<dynamic> get categories => _categories;
  CategoryState get state => _state;
  String get errorMessage => _errorMessage;

  final AuthService _authService = AuthService();

  Future<Map<String, String>> _getHeaders() async {
    final token = await _authService.getToken();
    return {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    };
  }

  Future<void> fetchCategories() async {
    _state = CategoryState.loading;
    notifyListeners();
    try {
      final response = await http.get(
        Uri.parse('${Constants.baseUrl}/categories'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200) {
        _categories = data['data'];
        _state = CategoryState.success;
      } else {
        _state = CategoryState.error;
        _errorMessage = data['message'] ?? 'Gagal mengambil data kategori';
      }
    } catch (e) {
      _state = CategoryState.error;
      _errorMessage = e.toString();
    }
    notifyListeners();
  }

  Future<bool> addCategory(String name) async {
    try {
      final response = await http.post(
        Uri.parse('${Constants.baseUrl}/categories'),
        headers: await _getHeaders(),
        body: {'name': name},
      );
      if (response.statusCode == 201 || response.statusCode == 200) {
        fetchCategories();
        return true;
      }
    } catch (e) {
      print("Error add category: $e");
    }
    return false;
  }

  Future<bool> updateCategory(int id, String name) async {
    try {
      final response = await http.put(
        Uri.parse('${Constants.baseUrl}/categories/$id'),
        headers: await _getHeaders(),
        body: {'name': name},
      );
      if (response.statusCode == 200) {
        fetchCategories();
        return true;
      }
    } catch (e) {
      print("Error update category: $e");
    }
    return false;
  }

  Future<bool> deleteCategory(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${Constants.baseUrl}/categories/$id'),
        headers: await _getHeaders(),
      );
      if (response.statusCode == 200) {
        fetchCategories();
        return true;
      }
    } catch (e) {
      print("Error delete category: $e");
    }
    return false;
  }
}
