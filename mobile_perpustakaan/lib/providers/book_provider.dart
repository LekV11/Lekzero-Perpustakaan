import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:http_parser/http_parser.dart';
import 'dart:convert';
import 'dart:io';
import '../utils/constants.dart';
import '../services/auth_service.dart';

enum BookState { loading, error, success }

class BookProvider extends ChangeNotifier {
  List<dynamic> _books = [];
  List<dynamic> _categories = [];
  BookState _state = BookState.loading;
  String _errorMessage = '';

  List<dynamic> get books => _books;
  List<dynamic> get categories => _categories;
  BookState get state => _state;
  String get errorMessage => _errorMessage;

  final AuthService _authService = AuthService();

  Future<Map<String, String>> _getHeaders() async {
    final token = await _authService.getToken();
    return {
      'Authorization': 'Bearer $token',
      'Accept': 'application/json',
    };
  }

  Future<void> fetchBooks() async {
    _state = BookState.loading;
    notifyListeners();
    try {
      final response = await http.get(
        Uri.parse('${Constants.baseUrl}/books'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200) {
        _books = data['data'];
        _state = BookState.success;
      } else {
        _state = BookState.error;
        _errorMessage = data['message'] ?? 'Gagal mengambil data buku';
      }
    } catch (e) {
      _state = BookState.error;
      _errorMessage = e.toString();
    }
    notifyListeners();
  }

  Future<void> fetchCategories() async {
    try {
      final response = await http.get(
        Uri.parse('${Constants.baseUrl}/categories'),
        headers: await _getHeaders(),
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200) {
        _categories = data['data'];
      }
    } catch (e) {
      print("Error fetch categories: $e");
    }
    notifyListeners();
  }

  Future<bool> addBook(Map<String, String> body, File? imageFile) async {
    try {
      final token = await _authService.getToken();
      var request = http.MultipartRequest('POST', Uri.parse('${Constants.baseUrl}/books'));
      request.headers.addAll({
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });

      request.fields.addAll(body);

      if (imageFile != null) {
        request.files.add(await http.MultipartFile.fromPath(
          'cover',
          imageFile.path,
          contentType: MediaType('image', 'jpeg'),
        ));
      }

      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      print("Add Book Status: ${response.statusCode}");
      print("Add Book Body: ${response.body}");

      if (response.statusCode == 201 || response.statusCode == 200) {
        await fetchBooks();
        return true;
      }
    } catch (e) {
      print("Error add book: $e");
    }
    return false;
  }

  Future<bool> updateBook(int id, Map<String, String> body, File? imageFile) async {
    try {
      final token = await _authService.getToken();
      // Untuk update dengan file di Laravel, seringkali butuh POST dengan _method: PUT
      var request = http.MultipartRequest('POST', Uri.parse('${Constants.baseUrl}/books/$id'));
      request.headers.addAll({
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      });

      request.fields.addAll(body);
      request.fields['_method'] = 'PUT';

      if (imageFile != null) {
        request.files.add(await http.MultipartFile.fromPath(
          'cover',
          imageFile.path,
          contentType: MediaType('image', 'jpeg'),
        ));
      }

      var streamedResponse = await request.send();
      var response = await http.Response.fromStream(streamedResponse);

      print("Update Book Status: ${response.statusCode}");
      print("Update Book Body: ${response.body}");

      if (response.statusCode == 200) {
        await fetchBooks();
        return true;
      }
    } catch (e) {
      print("Error update book: $e");
    }
    return false;
  }

  Future<bool> deleteBook(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${Constants.baseUrl}/books/$id'),
        headers: await _getHeaders(),
      );
      if (response.statusCode == 200) {
        fetchBooks();
        return true;
      }
    } catch (e) {
      print("Error delete book: $e");
    }
    return false;
  }
}
