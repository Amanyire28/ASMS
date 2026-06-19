<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Letter - {{ $letter->student->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
            width: 100%;
            height: auto;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.5;
        }
        .page {
            width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
            box-shadow: none;
            display: block;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        .letter-content {
            font-size: 13px;
            line-height: 1.6;
            width: 100%;
            display: block;
        }
        
        /* Prevent page breaks inside critical elements */
        .letter-content > div {
            page-break-inside: avoid;
            display: block;
        }
        
        p { 
            page-break-inside: avoid; 
            margin: 6px 0;
            display: block;
        }
        table { 
            page-break-inside: avoid;
            display: table;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: auto !important;
                background: white !important;
            }
            .page {
                width: 100% !important;
                margin: 0 !important;
                padding: 15mm !important;
                box-shadow: none !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
                display: block !important;
            }
            .letter-content {
                display: block !important;
                width: 100% !important;
                background: white !important;
                color: #333 !important;
            }
            body::before,
            body::after {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        @include('modules.admissions.letter-template', ['student' => $letter->student, 'remarks' => $letter->remarks, 'schoolSettings' => $schoolSettings])
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure content is fully rendered before printing
            setTimeout(function() {
                window.print();
            }, 1000);
        });
    </script>
</body>
</html>
