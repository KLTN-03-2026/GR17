<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>API Documentation</h1>
        <h2>Public Endpoints</h2>
        <h3>POST /api/login</h3>
        <p>Login for all users.</p>
        <pre>
{
    "email": "user@example.com",
    "password": "password"
}
        </pre>

        <h2>Admin Endpoints</h2>
        <h3>GET /api/admins</h3>
        <p>Retrieve a list of all admins.</p>

        <h3>POST /api/admin</h3>
        <p>Create a new admin.</p>
        <pre>
{
    "Ma_admin": "A123456789",
    "Ho_va_ten": "John Doe",
    "Mat_khau": "password",
    "Email": "john.doe@example.com",
    "Ngay_sinh": "01/01/1990",
    "Gioi_tinh": 1,
    "id_chuc_vu": 1,
    "so_dien_thoai": "0123456789"
}
        </pre>

        <h3>Other Endpoints</h3>
        <p>Refer to the Postman collection for detailed examples.</p>
    </div>
</body>
</html>
