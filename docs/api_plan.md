# Laravel API Plan

## Table: Admin
- **Ma_admin**: 10 characters, unique, primary key
- **Ho_va_ten**: 5-40 characters, alphabetic only
- **Mat_khau**: Encrypted with bcrypt (using Sanctum)
- **Email**: Unique, valid email format
- **Ngay_sinh**: Date format (dd/mm/yyyy)
- **Gioi_tinh**: 1 for male, 0 for female
- **id_chuc_vu**: Foreign key for roles
- **is_block**: 1 for active, 0 for blocked
- **hash_reset**: Used for password reset
- **so_dien_thoai**: Unique, 10 digits, starts with 0

## API Endpoints

### Public
1. **POST /api/login**: Login for all users

### Admin
1. **POST /api/admin/login**: Admin login
2. **DELETE /api/admin/{id}**: Delete admin
3. **GET /api/admin/{id}**: View admin details
4. **GET /api/admins**: View all admins
5. **PUT /api/admins**: Update all admins
6. **GET /api/admins/search**: Search admins by phone, email, or name
7. **PUT /api/admin/password**: Change own password
8. **POST /api/admin**: Add new admin
9. **PUT /api/admin/{id}**: Update admin details
10. **PATCH /api/admin/{id}/status**: Change admin status

## Sample Data
1. Admin 1: Example data
2. Admin 2: Example data
3. Admin 3: Example data
4. Admin 4: Example data
5. Admin 5: Example data

## Postman Test Cases
- Detailed test cases for each endpoint

## API Documentation
- Webview at `/api/docs` with detailed descriptions for each endpoint
