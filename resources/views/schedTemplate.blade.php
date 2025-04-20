<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Memo</title>
  <!-- Bootstrap CSS (online) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @page {
      margin: 20mm;
    }

    body {
      font-family: Arial, sans-serif;
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
            <strong>STUDENT GOVERNMENT</strong><br>
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
    <div class="text-center mt-4">
      <strong>OFFICE OF THE COLLEGE STUDENT GOVERNMENT PRESIDENT</strong><br>
      Memorandum No. 12<br>
      Series of 2025<br><br>
    </div>

    <div class="content">
      <p><strong>To/For:</strong> {{ $course . ' ' . $year . ' - ' . $block }}</p>
      <p><strong>From:</strong> REIZO BHIENN CATAROJA<br>President, CCMS – Student Government</p>

      <p><strong>Subject:</strong> Scheduled Registration and Biometric Data Collection</p>
      <p><strong>Venue:</strong> {{ $venue }}</p>
      <p><strong>Date:</strong> {{ $date }}</p>
      <p><strong>Time:</strong> {{ $start_time }} to {{ $end_time }}</p>

      <hr>

      <p>Greetings!</p>

      <p>
        In compliance with the upcoming academic term and as part of the modernization of student services, 
        all CCMS students are required to participate in the scheduled Registration and Biometric Data 
        Collection from {{ $date }} at {{ $venue }}. This initiative is part of the student profiling and security 
        enhancement protocols being implemented by the College in partnership with the CNSC Information 
        Systems Office.
      </p>

      <p>This activity involves the following:</p>
      <ul>
        <li>Verification and update of personal student information</li>
        <li>Capture of digital photograph</li>
        <li>Collection of fingerprint data for biometric verification</li>
        <li>Issuance of new digital student ID</li>
      </ul>

      <p>Students are advised to bring one (1) valid government ID, their student ID (if applicable), and 
      ensure they are properly groomed for photo capture. All data collected will be treated in accordance 
      with the Data Privacy Act of 2012 and the institution’s data management policy.</p>

      <p>Your full cooperation is expected to ensure the smooth facilitation of this registration. Should you 
      have further concerns, feel free to contact the CCMS-SG Office.</p>

      <p>Thank you for your prompt attention and participation.</p>

      <br><br>
      <p><strong>Noted:</strong></p>
      <p>SGD<br>MARC LESTER ACUNIN<br>Adviser, CCMS – Student Government</p>
    </div>

  </div>
</body>
</html>
