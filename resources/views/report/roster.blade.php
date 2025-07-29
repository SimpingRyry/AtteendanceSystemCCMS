<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 50px 25px 70px 25px; /* top, right, bottom, left */
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding-top: 100px; /* Space for fixed header */
            padding-bottom: 70px; /* Space for fixed footer */
        }

        header {
            position: fixed;
            top: -12px;
            left: 0;
            right: 0;
            bottom: 5px;
            height: 70px;
            text-align: center;
        }

        header img {
            width: 100%;
            height: auto;
            bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 10px;
            color: #555;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            padding: 0;
            font-size: 10px;
        }

        .footer-left {
            text-align: left;
        }

        .footer-left div {
            line-height: 1.2;
        }

        .footer-right {
            text-align: right;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            color:rgb(0, 0, 0);
        }

        .container {
            width: 100%;
            text-align: center;
        }

        .card {
            display: inline-block;
            vertical-align: top;
            width: 16%;
            margin: 1%;
            box-sizing: border-box;
            text-align: center;
            height: 180px;
            page-break-inside: avoid;
        }

        .card img {
            width: 80px;
            height: 75px;
            object-fit: cover;
        }

        .info {
            margin-top: 3px;
            line-height: 1.2;
        }

        .position {
            font-size: 10px;
            color: #555;
            font-style: italic;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        thead tr {
            font-weight: bold;
        }

        td {
            padding: 2px 5px;
            text-align: center;
            border: none;
        }

        .underline-wrapper {
            display: flex;
            justify-content: center;
        }

        .underline {
            display: inline-block;
            min-width: 130px;
            padding: 2px 5px;
            border-bottom: 1px solid #000;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- Fixed Header -->
<header>
    <img src="{{ public_path('images/header.png') }}" alt="CNSC Header">
</header>

<!-- Fixed Footer -->
<footer>
    <table class="footer-table">
        <tr>
            <td class="footer-left">
                <div>CNSC-OP-SSD-01F12</div>
                <div>Revision: 2</div>
            </td>
            <td class="footer-right">
                <!-- Page number handled by DOMPDF script below -->
            </td>
        </tr>
    </table>
</footer>

<!-- Main Content Starts -->
<div class="title">ROSTER OF OFFICERS</div>

<div class="org-section" style="text-align: center; margin-bottom: 10px;">
    <div style="font-size: 14px; font-weight: bold;">{{ $org }}</div>
    <div style="width: 200px; margin: 4px auto; border-bottom: 1px solid #000;"></div>
    <div style="font-size: 12px;">Name of Organization</div>
</div>

<div class="container">
    @foreach ($officers as $officer)
        <div class="card">
            <img src="{{ $officer['photo'] }}" alt="Profile Photo">
            <div class="info"><strong>Name:</strong> {{ $officer['name'] }}</div>
            <div class="info"><strong>Birthdate:</strong> {{ $officer['birth_date'] }}</div>
            <div class="info"><strong>Address:</strong> {{ $officer['address'] }}</div>
        </div>
    @endforeach
</div>

<div style="page-break-before: always;"></div>


<table>
    <thead>
        <tr>
            <td>Name</td>
            <td>Position</td>
            <td>Course</td>
            <td>Year Level</td>
        </tr>
    </thead>
    <tbody>
        @php
            $yearMap = [
                '1' => '1st Year',
                '2' => '2nd Year',
                '3' => '3rd Year',
                '4' => '4th Year',
                '5' => '5th Year',
            ];
        @endphp
        @foreach ($officers as $officer)
            <tr>
                <td><div class="underline-wrapper"><span class="underline">{{ $officer['name'] }}</span></div></td>
                <td><div class="underline-wrapper"><span class="underline">{{ $officer['position'] }}</span></div></td>
                <td><div class="underline-wrapper"><span class="underline">{{ $officer['course'] }}</span></div></td>
                <td><div class="underline-wrapper"><span class="underline">{{ $yearMap[$officer['year']] ?? ($officer['year'] ?? '-') }}</span></div></td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Page Number Script for DOMPDF -->
<script type="text/php">
    if (isset($pdf)) {
        $x = 500;
        $y = 765;
        $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
        $font = $fontMetrics->getFont("DejaVu Sans", "sans-serif");
        $size = 8;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>

</body>
</html>
