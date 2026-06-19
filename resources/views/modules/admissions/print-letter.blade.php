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
        }
        .letter-content {
            font-size: 13px;
            line-height: 1.6;
        }
        /* Prevent page breaks inside elements */
        p { page-break-inside: avoid; margin: 6px 0; }
        table { page-break-inside: avoid; }
        div { page-break-inside: avoid; }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            html {
                margin: 0;
                padding: 0;
            }
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .page {
                width: 100%;
                margin: 0;
                padding: 15mm;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        @include('modules.admissions.letter-template', ['student' => $letter->student, 'remarks' => $letter->remarks, 'schoolSettings' => $schoolSettings])
    </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
