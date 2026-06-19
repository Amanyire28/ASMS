<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admission Letter - {{ $letter->student->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 8.5in;
            margin: auto;
            padding: 0.5in;
            min-height: 11in;
        }
        h1, h2 {
            color: #1565C0;
        }
        .letter-content {
            line-height: 1.8;
            font-size: 14px;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        @include('modules.admissions.letter-template', ['student' => $letter->student, 'remarks' => $letter->remarks])
    </div>
    <script>
        window.print();
    </script>
</body>
</html>
