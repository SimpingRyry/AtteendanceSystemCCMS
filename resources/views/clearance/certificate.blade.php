<!DOCTYPE html>
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12pt; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    .section { margin-top: 20px; }
    .signatories { margin-top: 50px; display: flex; justify-content: space-between; }
    .underline { border-bottom: 1px solid #000; display: inline-block; width: 300px; }

    .motto {
        text-align: center;
        font-weight: bold;
        font-style: italic;
        font-size: 11pt;
        margin: 10px 0;
    }

    .org-address {
        font-size: 8pt;
        white-space: nowrap;
    }
     .school-name {
        font-size: 11pt;
        font-weight: bold;
       
    }
     .college-name {
        font-size: 11pt;
       
    }
      .contact-info {
        text-align: right;
        font-size: 10pt;
    }
    .content-section {
    padding: 0 60px; /* Adjust to compress left and right margins */
}
</style>
</head>
<body>
      <table class="header-table">
      <tr>
        <td style="width: 100px;">
          <img src="{{ public_path('images/org_list/' . $orgLogo) }}" alt="Org Logo" class="logo" width="80">

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

    <div class="center">
        
        <h3 class="bold" style="border-bottom: 1px solid #000; display: inline-block; width: 300px;">CERTIFICATE OF CLEARANCE</h3>
    </div>
  

<div class="content-section">

   <p style="text-align: justify;">
    This certifies that 
    <span class="underline" style="font-weight: bold; text-align:center">
        {{ $student->name }} - {{ $student->studentList->section }}
    </span> 
    has no outstanding obligations or debts to 
    <strong>{{ Auth::user()->org }}</strong> 
    as of 
    <u>{{ \Carbon\Carbon::now()->format('F d, Y') }}</u>.
</p>

    <div class="section">
        <p>This clearance covers the following:</p>
        <ul>
            <li>Financial Obligations (<em>Fines, Debts, and etc.</em>)</li>
            
        </ul>
    </div>
<table style="width: 100%; margin-top: 30px;">
    <tr>
        <!-- Verified by label above VP -->
        <td style="font-size: 10pt; width: 80%;">
            <div style="text-align: left;">
                <p style="margin: 0; font-size: 11pt;">Verified by:</p>
                <p style="margin: 0; font-weight: bold; margin-top: 15px; font-size: 12pt;">
                    {{ $vicePresident?->name ?? '' }}
                </p>
                <p style="margin: 0;">
                    {{ $vicePresident?->role ?? 'CCMS-SG Vice President for Financial Affairs' }}
                </p>
            </div>
        </td>

        <!-- Certified by label above President -->
        <td style="font-size: 10pt; width: 50%;">
            <div style="text-align: left;">
                <p style="margin: 0; font-size: 11pt;">Certified by:</p>
                <p style="margin: 0; font-weight: bold; margin-top: 15px; font-size: 12pt;">
                    {{ $president?->name ?? '' }}
                </p>
                <p style="margin: 0;">
                    {{ $president?->role ?? 'CCMS-SG President' }}
                </p>
            </div>
        </td>
    </tr>
</table>

</div>
<hr style="margin-top: 30px; border: none; border-top: 2px solid #000; width: 100%;">
</body>
</html>
