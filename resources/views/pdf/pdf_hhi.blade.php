<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Heart Health Initiative</title>
    <style>
        /* Reset body margin/padding */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            background: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        /* Center the letter-page */
        .letter-page {
            width: 8.5in;
            min-height: 11in;
            margin: 0 auto;
            background: white;
            padding: 0.8in 1in 0.6in 1in;
            font-size: 12pt;
            line-height: 1.5;
            position: relative;
            box-sizing: border-box;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .subheader {
            font-family: Helvetica, sans-serif;
            font-size: 12px;
            margin: 5px 0 20px 0;
        }

        hr {
            border: none;
            height: 2px;
            background: black;
            margin-bottom: 20px;
        }

        /* Two-column content */
        .content {
            column-count: 2;
            column-gap: 40px;
            white-space: pre-wrap; /* preserve newlines */
            word-break: break-word;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 0.6in;
            width: 100%;
            text-align: right;
            font-size: 13pt;
        }

        .footer img {
            height: 0.8in;
            width: auto;
        }

        .signature-block p {
            margin: 5px 0 0 0;
        }
    </style>
</head>
<body>
<div class="letter-page">
    <!-- HEADER -->
    <div class="header">
        <h2>
            <img src="{{ public_path('img/document_logo.png') }}" alt="Logo">
            Heart Health Initiative
        </h2>
        <p class="subheader">
            A Primary TeleMedicine Health Program<br>
            Ateneo de Zamboanga University
        </p>
        <hr>
    </div>

    <!-- MAIN CONTENT -->
    <div class="content">
        {!! $content !!}
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="signature-block">
            <img src="{{ public_path('img/tex_signature.jpg') }}" alt="Signature"><br>
            <p>Fr. Alberto B. Paurom MD SJ<br>Lic# 60679</p>
        </div>
    </div>
</div>
</body>
</html>
