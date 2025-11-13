Exam Form Payment Portal Backend — Documentation

Project: Exam Form & Payment Portal (Laravel 12)
Date: 2025-11-13

Overview
--------
This document describes the backend implementation for the Exam Form & Payment Portal built with Laravel 12.
It covers project setup, architecture (Repository + Service pattern), authentication, API endpoints (methods, URLs),
request and response examples, payment flow (Razorpay and Stripe), PDF receipt generation, Postman usage, and troubleshooting.

1. Project Setup (Quick)
-------------------------
Commands (run in project root):
    composer install
    cp .env.example .env
    # Edit .env: set DB_*, JWT_SECRET, RAZORPAY_KEY/SECRET, STRIPE_SECRET,
    php artisan key:generate
    php artisan jwt:secret
    php artisan migrate
    php artisan db:seed --class=AdminSeeder
    php artisan storage:link
    php artisan serve


2. Project Structure (important files)
--------------------------------------
app/
  Http/
    Controllers/Api/     # API controllers (AuthController, FormController, SubmissionController, PaymentController)
    Middleware/          # RoleMiddleware,JWTAuthenticate
  Models/                # Eloquent models: User, Form, Submission, Payment
  Repositories/
    Interfaces/          # Repository interfaces
    Eloquent/            # Eloquent implementations
  Services/              # Business logic services (FormService, SubmissionService, PaymentService)
  Providers/
    RepositoryServiceProvider.php  # Binds interfaces to implementations

resources/views/receipts/payment.blade.php  # Receipt template for DOMPDF
routes/api.php                              # API routes
database/migrations/                         # Migrations for users, forms, submissions, payments
database/seeders/AdminSeeder.php             # Creates default admin


3. Authentication
-----------------
JWT-based authentication using tymon/jwt-auth. Configure config/auth.php:
    'guards' => [
      'api' => [
          'driver' => 'jwt',
          'provider' => 'users',
      ],
    ],


4. API Endpoints (Full list with examples)
-----------------------------------------
Base URL: http://127.0.0.1:8000/api

AUTHENTICATION
1) POST /auth/register
   - Body (JSON): { "name", "email", "password" }
   - Response (201): { success: true, message, user, token }

2) POST /auth/login
   - Body (JSON): { "email", "password" }
   - Response (200): { success: true, message, token, user }

3) GET /auth/me
   - Header: Authorization: Bearer <token>
   - Response (200): user object

4) POST /auth/logout
   - Header: Authorization: Bearer <token>
   - Response: { message: 'Logged out' }

FORMS
5) GET /forms
   - Header: Authorization: Bearer <token>
   - Response: [ { id, title, description, exam_date, fee, structure } ]

6) GET /forms/{id}
   - Response: single form object

7) POST /forms  (ADMIN only)
   - Body: { title, description, exam_date, fee, structure }
   - Header: Authorization: Bearer <admin_token>

8) PUT /forms/{id}  (ADMIN only)
   - Body: fields to update

9) DELETE /forms/{id}  (ADMIN only)

SUBMISSIONS
10) POST /submissions
    - Body: { form_id, answers (JSON) }
    - Response: created submission { id, user_id, form_id, status }

11) GET /submissions/my
    - Returns user's submissions

12) GET /admin/submissions (ADMIN)
13) GET /admin/submissions/{id} (ADMIN)

PAYMENTS
14) POST /payments/initiate
    - Body: { submission_id, gateway: 'razorpay'|'stripe' }
    - Response (Razorpay): { payment, order }
    - Response (Stripe): { payment, client_secret }

15) POST /payments/confirm
    - Body: { payment_id, transaction_id }
    - Marks payment success, generates PDF receipt

16) GET /payments/{id}/receipt
    - Downloads PDF receipt (Authorization header required)

17) POST /webhook/payment
    - Public webhook for gateways (Stripe, Razorpay) to notify payment events

5. Payment Flow
-----------------------------
1. User submits a form → `/submissions` (status = pending)
2. User initiates payment → `/payments/initiate` (gateway returns order or client_secret)
3. Payment completes client-side (Razorpay/Stripe). Gateway calls webhook `/webhook/payment` or client calls `/payments/confirm`.
4. On success, PaymentService.markSuccess() sets status='success', submission.status='paid', and generateReceipt() is called.
5. Receipt saved to storage/app/public/receipts/receipt_{id}.pdf and public URL returned or downloadable from `/payments/{id}/receipt`.
6. Troubleshooting & Tips
-------------------------
- If you get HTML Welcome page, ensure you're hitting /api/... and server is started from project root (php artisan serve).
- If login returns boolean true, ensure config/auth.php 'api' guard driver is 'jwt' and use Auth::guard('api')->attempt(...)
- If receipts not found:
    - Ensure storage/app/public/receipts exists and php artisan storage:link has been run
    - Ensure generateReceipt() saves to Storage::disk('public')

End of document.
