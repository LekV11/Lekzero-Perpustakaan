import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../utils/constants.dart';

class AuthService {
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('${Constants.baseUrl}/login'),
        headers: {
          'Accept': 'application/json',
        },
        body: {'email': email, 'password': password},
      ).timeout(const Duration(seconds: 10));

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 200 && data['status'] == 'success') {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('jwt_token', data['data']['token']);
        await prefs.setString('user_name', data['data']['user']['name']);
        await prefs.setString('user_role', data['data']['user']['role'] ?? 'user');
        return data;
      } else {
        throw Exception(data['message'] ?? 'Email atau password salah');
      }
    } catch (e) {
      throw Exception('Error: $e');
    }
  }

  Future<Map<String, dynamic>> register(String name, String email, String password, String passwordConfirmation) async {
    try {
      final response = await http.post(
        Uri.parse('${Constants.baseUrl}/register'),
        headers: {
          'Accept': 'application/json',
        },
        body: {
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
        },
      ).timeout(const Duration(seconds: 10));

      final data = jsonDecode(response.body);
      
      if (response.statusCode == 201 && data['status'] == 'success') {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('jwt_token', data['data']['token']);
        await prefs.setString('user_name', data['data']['user']['name']);
        await prefs.setString('user_role', data['data']['user']['role'] ?? 'user');
        return data;
      } else {
        String msg = data['message'] ?? 'Gagal mendaftar';
        if (data['errors'] != null) {
          msg = (data['errors'] as Map).values.first[0].toString();
        }
        throw Exception(msg);
      }
    } catch (e) {
      throw Exception(e.toString().replaceAll('Exception: ', ''));
    }
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }

  Future<String?> getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('jwt_token');
  }

  Future<String?> getRole() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('user_role');
  }
}
