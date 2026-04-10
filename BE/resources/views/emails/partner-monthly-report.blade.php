<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bao cao doanh thu thang</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937;">
    <h2>Xin chao {{ $doiTac->ten_nguoi_dai_dien ?? $doiTac->ten_doi_tac }},</h2>

    <p>
        Day la bao cao doanh thu thang <strong>{{ $reportData['month'] }}</strong>
        (tu {{ $reportData['period']['from'] }} den {{ $reportData['period']['to'] }}).
    </p>

    <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse;">
        <tr>
            <td>Tong don hang</td>
            <td><strong>{{ $reportData['metrics']['total_orders'] }}</strong></td>
        </tr>
        <tr>
            <td>Tong doanh thu giao dich</td>
            <td><strong>{{ number_format($reportData['metrics']['gross_revenue'], 0, ',', '.') }} VND</strong></td>
        </tr>
        <tr>
            <td>Phi hoa hong nen tang</td>
            <td><strong>{{ number_format($reportData['metrics']['platform_commission'], 0, ',', '.') }} VND</strong></td>
        </tr>
        <tr>
            <td>Tien doi tac thuc nhan</td>
            <td><strong>{{ number_format($reportData['metrics']['partner_revenue'], 0, ',', '.') }} VND</strong></td>
        </tr>
        <tr>
            <td>Da chuyen khoan</td>
            <td><strong>{{ number_format($reportData['metrics']['paid_to_partner'], 0, ',', '.') }} VND</strong></td>
        </tr>
        <tr>
            <td>Dang cho doi soat</td>
            <td><strong>{{ number_format($reportData['metrics']['pending_to_partner'], 0, ',', '.') }} VND</strong></td>
        </tr>
    </table>

    <p style="margin-top: 16px;">
        Cam on doi tac da dong hanh cung Smart Travel.
    </p>
</body>
</html>
