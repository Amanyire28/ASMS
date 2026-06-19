<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .receipt-container {
            width: 210mm;
            height: auto;
            margin: 0 auto;
            padding: 40px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #555;
            margin-top: 15px;
        }
        .receipt-number {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .label {
            font-weight: 500;
            color: #555;
        }
        .value {
            color: #333;
            text-align: right;
        }
        .amount-row {
            border-top: 1px solid #ddd;
            border-bottom: 2px solid #333;
            padding: 10px 0;
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        .amount {
            display: flex;
            justify-content: space-between;
        }
        .amount-label {
            color: #333;
        }
        .amount-value {
            color: #2ecc71;
            font-size: 16px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #999;
        }
        .signature-line {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }
        .signature {
            text-align: center;
            width: 150px;
        }
        .line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
            height: 30px;
        }
        .label-text {
            font-size: 11px;
        }
        .print-button {
            text-align: center;
            margin-bottom: 20px;
            display: no-print;
        }
        .print-button button {
            padding: 10px 30px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-button button:hover {
            background-color: #2980b9;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .receipt-container {
                box-shadow: none;
                width: 100%;
                padding: 20px;
            }
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()"><i class="fas fa-print"></i> Print Receipt</button>
    </div>

    <div class="receipt-container">
        <div class="header">
            <div class="school-name">{{ config('school.school_name', 'ASMS School') }}</div>
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <div class="receipt-number">Receipt #: {{ $payment->receipt_number }}</div>
        </div>

        <div class="section">
            <div class="section-title">Student Information</div>
            <div class="row">
                <span class="label">Name:</span>
                <span class="value">{{ $payment->studentFee->student->first_name }} {{ $payment->studentFee->student->last_name }}</span>
            </div>
            <div class="row">
                <span class="label">Admission #:</span>
                <span class="value">{{ $payment->studentFee->student->admission_number }}</span>
            </div>
            <div class="row">
                <span class="label">Class:</span>
                <span class="value">{{ $payment->studentFee->student->class?->name }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Payment Details</div>
            <div class="row">
                <span class="label">Payment Date:</span>
                <span class="value">{{ $payment->payment_date->format('d M Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ $payment->paymentMethod->name }}</span>
            </div>
            @if ($payment->transaction_reference)
                <div class="row">
                    <span class="label">Transaction Ref:</span>
                    <span class="value">{{ $payment->transaction_reference }}</span>
                </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Fee Details</div>
            <div class="row">
                <span class="label">Fee Type:</span>
                <span class="value">{{ $payment->studentFee->fee->name }}</span>
            </div>
            <div class="row">
                <span class="label">Term:</span>
                <span class="value">Term {{ $payment->studentFee->term }}</span>
            </div>
            <div class="row">
                <span class="label">Original Amount:</span>
                <span class="value">{{ number_format($payment->studentFee->amount, 2) }}</span>
            </div>
            <div class="row">
                <span class="label">Amount Paid:</span>
                <span class="value">{{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <div class="section">
            <div class="amount-row">
                <div class="amount">
                    <span class="amount-label">Total Paid:</span>
                    <span class="amount-value">{{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if ($payment->notes)
            <div class="section">
                <div class="section-title">Notes</div>
                <div class="row">
                    <span>{{ $payment->notes }}</span>
                </div>
            </div>
        @endif

        <div class="signature-line">
            <div class="signature">
                <div class="line"></div>
                <div class="label-text">Cashier / Treasurer</div>
            </div>
            <div class="signature">
                <div class="line"></div>
                <div class="label-text">Headteacher</div>
            </div>
        </div>

        <div class="footer">
            <p>This is an official receipt from {{ config('school.school_name', 'ASMS School') }}</p>
            <p>Issued on {{ $payment->created_at->format('d M Y H:i') }}</p>
            <p style="margin-top: 10px;">Thank you for your payment</p>
        </div>
    </div>
</body>
</html>
