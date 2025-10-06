<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Financial Report</title>
  <!-- Bootstrap CSS (online) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @page {
      margin: 20mm;
    }

    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      background: url('{{ $logo }}') no-repeat center;
      background-size: 400px;
      opacity: 0.95;
    }

    .overlay {
      background-color: rgba(255, 255, 255, 0.85);
      padding: 20px;
      border-radius: 8px;
    }

    .header-table {
      width: 100%;
    }

    .header-table td {
      vertical-align: top;
    }

    .logo {
      width: 80px;
      height: auto;
    }

    .org-details {
      font-size: 14px;
      line-height: 1.4;
      margin-left: 10px;
    }

    .org-details .school-name {
      font-family: 'Arial Black', Gadget, sans-serif;
      font-size: 12px;
    }

    .org-details .college-name {
      font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
      color: #00796b; /* Deeper Cyan */
      font-size: 14px;
    }

    .org-address {
      font-size: 8px;
    }

    .contact-info {
      text-align: right;
      font-size: 10px;
      margin-top: 5px;
    }

    .motto {
      text-align: center;
      font-weight: bold;
      font-size: 11px;
      margin: 15px 0;
    }

    .vision-mission-table {
      width: 100%;
      margin-top: 10px;
      font-style: italic;
      font-size: 11px;
    }

    .vision-mission-table td {
      vertical-align: top;
      padding: 10px;
      border-radius: 6px;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }

    .content {
      margin-top: 30px;
      font-size: 12px;
    }

    .content p {
      margin: 6px 0;
    }

    ul {
      margin-top: 5px;
      margin-bottom: 10px;
    }

    hr {
      border: none;
      border-top: 1px solid #000;
      margin: 20px 0;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <!-- Header with logo and org info -->
    <table class="header-table">
      <tr>
        <td style="width: 100px;">
          <img src="{{ $logo }}" alt="Org Logo" class="logo">
        </td>
        <td>
          <div class="org-details">
            <div class="school-name">CAMARINES NORTE STATE COLLEGE</div>
            <div class="college-name">COLLEGE OF COMPUTING AND MULTIMEDIA STUDIES</div>
        
            <span class="org-address">F. PIMENTEL AVENUE, BARANGAY II, DAET, CAMARINES NORTE - 4600, PHILIPPINES</span>
          </div>
        </td>
        <td class="contact-info">
          www.facebook.com/ics.csc<br>
          ics_csc@cnsc.edu.ph<br>
          (+63) 093-705-6129
        </td>
      </tr>
    </table>

    <!-- Motto -->
    <div class="motto">LEAD, SERVE, EXCEL</div>

    <!-- Vision & Mission -->
    <table class="vision-mission-table">
      <tr>
        <td>
          <strong>Vision</strong><br>
          To empower generation of transformative leaders who will shape a more just, equitable, and sustainable world.
        </td>
        <td>
          <strong>Mission</strong><br>
          To foster a democratic and self-governing student body that empowers students to unite, protect their rights and interests, fulfill their duties and responsibilities, and actively engage in strengthening good governance to advance the institution’s pursuit of excellence.
        </td>
      </tr>
    </table>

    <!-- Memo content -->
    <div class="text-center mt-3 mb-4">
  <h4 class="fw-bold">FINANCIAL REPORT</h4>
</div>

<div class="text-center mb-4" style="line-height: 2;">
  @if($org)
    <div style="font-size: 14px; font-weight: bold;">{{ $org }}</div>
    <div style="text-decoration: underline;">Name of Organization</div>
  @else
    <div style="font-size: 14px; font-weight: bold;">ALL ORGANIZATIONS</div>
    <div style="text-decoration: underline;">Name of Organization</div>
  @endif
</div>

<div class="text-center mb-3">
  <strong>Fees and Payment Report</strong><br>
  <span>({{ $month ?? 'All Months' }}, {{ $year ?? 'All Years' }})</span>
</div>

<div class="content">
  <div class="row">
    <!-- Left: Breakdown by Event -->
    <div class="col-md-12">
      <h5><strong>Breakdown by Event</strong></h5>
      <table class="table table-sm" style="width: 100%; border-collapse: collapse;">
        <thead>
          <tr style="background-color: #eaeaea;">
            <th style="border: 1px solid #000; padding: 6px;">Event</th>
            <th style="border: 1px solid #000; padding: 6px;">Fees/Fine Issued</th>
            <th style="border: 1px solid #000; padding: 6px;">Payments Made</th>
            <th style="border: 1px solid #000; padding: 6px;">Balance</th>
          </tr>
        </thead>
        <tbody>
          @foreach($grouped as $event => $data)
          <tr>
            <td style="border: 1px solid #000; padding: 6px;">{{ $event }}</td>
            <td style="border: 1px solid #000; padding: 6px;">₱{{ number_format($data['fines'], 2) }}</td>
            <td style="border: 1px solid #000; padding: 6px;">₱{{ number_format($data['payments'], 2) }}</td>
            <td style="border: 1px solid #000; padding: 6px;">₱{{ number_format($data['balance'], 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Summary aligned to the right below the table -->
  <div class="d-flex justify-content-end mt-3">
    <div style="max-width: 300px;">
      <h5><strong>Summary</strong></h5>
      <ul class="list-unstyled">
        <li><strong>Total Fines Issued:</strong> ₱{{ number_format($summary['total_fines'], 2) }}</li>
        <li><strong>Total Payments Made:</strong> ₱{{ number_format($summary['total_payments'], 2) }}</li>
        <li><strong>Outstanding Balance:</strong> ₱{{ number_format($summary['balance'], 2) }}</li>
      </ul>
    </div>
  </div>
</div>
  </div>
</body>
</html>
