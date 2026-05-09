# Payment QR Demo Guide

Tai lieu nay dung de demo nhanh luong `customer -> admin -> doi tac` cho QR payment ma khong can sua bo seed legacy cu.

## 1. Chuan bi

Chay migrate:

```bash
php artisan migrate
```

Chay seed demo QR:

```bash
php artisan db:seed --class=PaymentQrDemoSeeder
```

Neu muon tao QR fallback dung ngay tren local, can co toi thieu cac bien sau trong `.env`:

```env
BANK_BIN=970422
BANK_ACCOUNT_NO=0399620355
BANK_ACCOUNT_NAME=VU HA THAI SON
QR_TEMPLATE=PtCU367
```

Neu muon demo live sandbox voi VietQR + SePay, bo sung them:

```env
VIETQR_CLIENT_ID=
VIETQR_API_KEY=
SEPAY_WEBHOOK_API_KEY=
```

## 2. Tai khoan demo

Tat ca tai khoan demo dung chung mat khau: `demo123`

- Customer:
  - Email: `customer.qr.demo@example.com`
  - Body login: `Email`, `Mat_khau`
- Partner:
  - Email: `partner.qr.demo@example.com`
  - Body login: `email`, `mat_khau`
- Admin:
  - Email: `admin.qr.demo@example.com`
  - Body login: `email`, `Mat_khau`

## 3. Du lieu demo duoc seed

- Tour demo: `DMT001`
- Lich khoi hanh demo:
  - `DMS001`
  - `DMS002`
- Hoa don demo:
  - `HD99010001`: `pending`
  - `HD99010002`: `paid`
  - `HD99010003`: `expired`
  - `HD99010004`: `failed`

Muc dich tung hoa don:

- `HD99010001`: dung de xem don dang cho thanh toan
- `HD99010002`: dung de doi chieu paid giua customer, admin, doi tac
- `HD99010003`: dung de demo `retry-payment`
- `HD99010004`: dung de demo trang thai that bai

## 4. Kich ban demo

### Kich ban 1: Customer tao don moi va hien QR

1. Dang nhap customer.
2. Tao QR moi voi:
   - `POST /api/khach-hang/tour/DMT001/thanh-toan/qr`
   - `ma_thoi_gian_tour = DMS002`
3. Xac nhan response co:
   - `ma_hoa_don`
   - `so_tien`
   - `noi_dung_chuyen_khoan = ma_hoa_don`
   - `qr_url`
4. Mo man checkout customer:
   - QR phai hien dung so tien
   - Trang thai ban dau la `pending`

### Kich ban 2: Simulate webhook va doi chieu voi admin/doi tac

1. Dang nhap customer, tao don moi hoac dung don `HD99010001`.
2. Goi:
   - `POST /api/payments/sepay/webhook`
3. Payload mau:

```json
{
  "id": "DEMO-SEPAY-NEW-01",
  "gateway": "VietinBank",
  "transactionDate": "2026-04-13 14:02:37",
  "content": "HD99010001",
  "transferType": "in",
  "transferAmount": 1890000,
  "referenceCode": "DEMO-PAID-NEW-01"
}
```

4. Neu `SEPAY_WEBHOOK_API_KEY` da dat, gui them header:

```http
Authorization: Apikey <SEPAY_WEBHOOK_API_KEY>
```

5. Xac nhan:
   - Customer `GET /api/khach-hang/hoa-don/{ma_hoa_don}/payment-status` -> `paid`
   - Admin `GET /api/admin/hoa-don` -> cung `ma_hoa_don`, `payment_status = paid`
   - Doi tac `GET /api/doi-tac/don-hang` -> cung `ma_hoa_don`, `payment_status = paid`

### Kich ban 3: Retry tren cung ma hoa don

1. Dang nhap customer.
2. Dung hoa don seed `HD99010003`.
3. Goi:
   - `POST /api/khach-hang/hoa-don/HD99010003/retry-payment`
4. Xac nhan:
   - `ma_hoa_don` giu nguyen la `HD99010003`
   - `payment_status` quay ve `pending`
   - `qr_url` duoc tao lai
   - man customer checkout hien nut `Tao ma QR moi` truoc retry va QR moi sau retry

## 5. Ky vong tren tung man hinh

### Customer

- Lich su don hang hien du 4 trang thai: `pending`, `paid`, `expired`, `failed`
- Don `expired` va `failed` co the mo lai checkout va retry
- Don `paid` co the xem lai trong lich su

### Admin

- Danh sach hoa don hien:
  - `customer_summary`
  - `payment_status`
  - `payment_reference`
  - `latest_qr_payment_summary`
- Hoa don QR la `read-only`, khong sua tay trang thai

### Doi tac

- Man quan ly don hang hien:
  - ten tour
  - khach dat
  - email
  - so dien thoai
  - payment status
  - payment reference
- Doanh thu chi cong cac don `paid`

## 6. Postman

Collection demo QR nam tai:

```text
docs/payment_qr_demo.postman_collection.json
```

Collection nay da tach rieng khoi `docs/testPostman.json` cu.
