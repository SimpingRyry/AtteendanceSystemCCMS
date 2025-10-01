<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Financial Report</title>
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
      color: #00796b;
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

    .text-center-heading {
      text-align: center;
      line-height: 1.6;
      margin-bottom: 10px;
    }

    table.table-bordered,
    .table-bordered td,
    .table-bordered th {
      border: 1px solid #000 !important;
    }

    .table {
      width: 100%;
      margin-top: 10px;
      border-collapse: collapse !important;
    }

    .table th, .table td {
      padding: 5px;
      text-align: center;
    }

    .summary {
      margin-top: 20px;
      font-size: 13px;
      text-align: right;
    }
  </style>
</head>
<body>
  <div class="overlay">
    <!-- Header -->
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

    <!-- Centered Report Title & Org Info -->
<div class="text-center-heading">
  <h4 class="fw-bold">FINANCIAL REPORT</h4>

  <div style="font-size: 14px; font-weight: bold; border-bottom: 1px solid black; display: inline-block; padding-bottom: 2px; margin-bottom: 2px;">
    {{ $org ?? 'ALL ORGANIZATIONS' }}
  </div>

  <div style="font-size: 13px; margin-bottom: 8px;">Name of Organization</div>

  @if($event)
    <div style="margin-top: 8px; font-size: 13px;">
      <strong>{{ $event }}</strong>
    </div>

    <!-- Cash on Hand Line with Dots -->
    <div style="font-size: 13px; margin-top: 5px;">
      <span style="display: inline-block; min-width: 90px;">Cash on Hand</span>
      <span style="border-bottom: 1px dotted black; display: inline-block; width: 400px;"></span>
      <span style="display: inline-block; margin-left: 4px;"><strong>₱{{ $cashOnHand }}</strong></span>
    </div>
  @endif
</div>

    <!-- Content: Table and Summary -->
    <div class="content">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>OR Number</th>
            <th>Item</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Cost</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tableData as $row)
          <tr>
            <td>{{ $row['or_number'] }}</td>
            <td>{{ $row['item'] }}</td>
            <td>{{ $row['unit'] }}</td>
            <td>{{ $row['quantity'] }}</td>
           <td>₱{{ number_format((float) $row['cost'], 2) }}</td>
<td>₱{{ number_format((float) $row['total'], 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Summary -->
<div class="summary" style="margin-top: 50px;">
  <table style="float: right; text-align: left; border-collapse: collapse;">
    <tr>
      <td><strong>Cash on Hand:</strong></td>
      <td style="padding-left: 10px;">₱{{ $cashOnHand }}</td>
    </tr>
    <tr style="border-bottom: 1px solid black;">
      <td><strong>Expenses:</strong></td>
      <td style="padding-left: 10px;">₱{{ $expenses }}</td>
    </tr>
    <tr>
      <td><strong>Balance:</strong></td>
      <td style="padding-left: 10px;">₱{{ $balance }}</td>
    </tr>
  </table>
</div>


<!-- Signatories Table -->
<table style="width: 100%; margin-top: 100px; font-size: 12px; margin-left: 20px;">
  <tr>
    <!-- Prepared by -->
    <td style="width: 100%; text-align: left; vertical-align: top;">
      <div style="margin-bottom: 5px;">Prepared by:</div>
      <strong style="font-size: 14px;">{{ $financialUser->name ?? '' }}</strong><br>
      <span style="font-size: 12px;">{{ $financialUser->role ?? '' }}</span>
    </td>

    <!-- Attested by -->
    <td style="width: 20%; text-align: left; vertical-align: top; margin-left: 20px;">
      <div style="margin-bottom: 5px;">Attested:</div>
      <strong style="font-size: 14px;">{{ $presidentUser->name ?? '' }}</strong><br>
      <span style="font-size: 12px;">{{ $presidentUser->role ?? '' }}</span>
    </td>
  </tr>

  <tr>
    <!-- Audited -->
    <td style="width: 50%; text-align: left; padding-top: 40px;">
      <div style="margin-bottom: 5px;">Audited:</div>
      <strong style="font-size: 14px;">{{ $auditorUser->name ?? '' }}</strong><br>
      <span style="font-size: 12px;">{{ $auditorUser->role ?? '' }}</span>
    </td>

    <!-- Noted -->
    <td style="width: 50%; text-align: left; padding-top: 40px;">
      <div style="margin-bottom: 5px; ">Noted:</div>
      <strong style="font-size: 14px;">{{ $adviserUser->name ?? '' }}</strong><br>
      <span style="font-size: 12px;">{{ $adviserUser->role ?? 'Adviser' }}</span>
    </td>
  </tr>
</table>

  </div>
</body>
</html>
