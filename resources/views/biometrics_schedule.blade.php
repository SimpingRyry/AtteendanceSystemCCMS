<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
 <style>
    body { font-family: sans-serif; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 5px; }
    .page-break { page-break-after: always; }

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
        padding: 0 60px;
    }

    /* Remove borders from header-table */
    .header-table td,
    .header-table th {
        border: none !important;
        padding: 0; /* optional: reduce spacing */
    }

    .header-table {
        width: 100%;
        margin-bottom: 5px;
    }

    .logo {
        display: block;
        margin: 0 auto;
    }

    .org-details {
        text-align: center;
    }
</style>
</head>
<body>

@foreach ($pagedChunks as $index => $chunk)
    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 100px;">
                <img src="{{ public_path('images/org_list/' . $orgLogo) }}" alt="Org Logo" class="logo" width="80">
            </td>
            <td>
                <div class="org-details" style="text-align: left;">
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

    <div class="motto">LEAD, SERVE, EXCEL</div>

    <!-- Content Section -->
    <div class="content-section">
        <h2>{{ $title }}</h2>
        <p>Date: <strong>{{ $chunk['date'] }}</strong></p>
        <p>Page {{ $index + 1 }}</p>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Section</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk['students'] as $i => $student)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->section }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!$loop->last)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>
