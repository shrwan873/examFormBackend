<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Exam Form - Payment Receipt</h2>
        <p>Date: {{ $date }}</p>
    </div>
    <table>
        <tr>
            <th>Receipt ID</th>
            <td>RECP-{{ $payment['id'] }}</td>
        </tr>
        <tr>
            <th>Transaction ID</th>
            <td>{{ $payment['transaction_id'] ?? ($payment['meta']['order']['id'] ?? '') }}</td>
        </tr>
        <tr>
            <th>Payer</th>
            <td>{{ $user['name'] }} ({{ $user['email'] }})</td>
        </tr>
        <tr>
            <th>Form</th>
            <td>{{ $form['title'] }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>₹ {{ number_format($payment['amount'],2) }}</td>
        </tr>
        <tr>
            <th>Payment Gateway</th>
            <td>{{ ucfirst($payment['gateway']) }}</td>
        </tr>
    </table>
    <p style="margin-top:20px">Thank you for your payment.</p>
</body>

</html>