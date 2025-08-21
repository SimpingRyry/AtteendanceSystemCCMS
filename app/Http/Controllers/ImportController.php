<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OrgList;
use App\Models\Student;
use App\Models\OfficerRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Logs;

class ImportController extends Controller
{
    public function import(Request $request)
{
    // Validate the uploaded file
    $request->validate([
        'importFile' => 'required|mimes:csv,txt',
    ]);

    // Get the uploaded file
    $file = $request->file('importFile');
    $path = $file->getRealPath();

    // Read the CSV file, starting from the 8th line
    $rows = array_map('str_getcsv', file($path));
    $rows = array_slice($rows, 7); // Skip first 7 lines (index 0-based)

    $studentsUpdated = 0;
    $studentsAdded = 0;

   foreach ($rows as $index => $row) {
    // Skip if row is completely empty (null or blank values)
    if (empty(array_filter($row))) {
        continue;
    }

    // Prepare birth date format safely
    $birthDate = null;
    if (!empty($row[9])) {
        try {
            $birthDate = \Carbon\Carbon::createFromFormat('m/d/Y', $row[9])->format('Y-m-d');
        } catch (\Exception $e) {
            $birthDate = null; // fallback if date is invalid
        }
    }

    // Check if student already exists by id_number
    $student = Student::where('id_number', $row[1] ?? null)->first();

    if ($student) {
        // Only update if any of the fields (except status) have changed
        $changes = [
            'no'         => $row[0] ?? null,
            'name'       => $row[2] ?? null,
            'gender'     => $row[3] ?? null,
            'course'     => $row[4] ?? null,
            'year'       => $row[5] ?? null,
            'units'      => $row[6] ?? null,
            'section'    => $row[7] ?? null,
            'contact_no' => $row[8] ?? null,
            'birth_date' => $birthDate,
            'address'    => $row[10] ?? null,
        ];

        $hasChanges = false;
        foreach ($changes as $key => $value) {
            if ($student->$key != $value) {
                $student->$key = $value;
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            $student->save();
            $studentsUpdated++;
        }
    } else {
        // Create a new student
        Student::create([
            'no'         => $row[0] ?? null,
            'id_number'  => $row[1] ?? null,
            'name'       => $row[2] ?? null,
            'gender'     => $row[3] ?? null,
            'course'     => $row[4] ?? null,
            'year'       => $row[5] ?? null,
            'units'      => $row[6] ?? null,
            'section'    => $row[7] ?? null,
            'contact_no' => $row[8] ?? null,
            'birth_date' => $birthDate,
            'address'    => $row[10] ?? null,
            'status'     => 'Unregistered'
        ]);
        $studentsAdded++;
    }
}

    // Redirect back with success message
    return redirect()->back()->with('success', "Data imported successfully! Students added: {$studentsAdded}, Students updated: {$studentsUpdated}");
}
public function show(Request $request)
{
    $query = Student::query();

    if ($request->filled('section')) {
        $query->where('section', $request->section);
    }

    if ($request->filled('year')) {
        $query->where('year', $request->year);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $students = $query->paginate(10);
    $sections = Student::select('section')->distinct()->pluck('section')->filter()->sort()->values();
    $years = Student::select('year')->distinct()->pluck('year')->filter()->sort()->values();

    $user = auth()->user();
    $userOrg = OrgList::where('org_name', $user->org)->first();

    $org_list = collect();
    $officerRoles = collect(); // Empty unless not a parent
    $sgRoles = collect();

    if ($userOrg) {
        $childOrgs = $userOrg->children()->get();

        if ($childOrgs->isNotEmpty()) {
            // ✅ Parent org: load children for selection, wait for AJAX to get roles
            $org_list = $childOrgs;
            $sgRoles = OfficerRole::where('org', $userOrg->org_name)->get();
            // officerRoles will be loaded via AJAX — leave it empty
        } else {
            // ✅ Not a parent: load current org roles right away
            $officerRoles = OfficerRole::where('org', $user->org)->get();
        }
    }

    return view('student', compact(
        'students',
        'sections',
        'years',
        'org_list',
        'officerRoles',
        'sgRoles'
    ));
}

public function preview(Request $request)
{
    $request->validate([
        'importFile' => 'required|mimes:csv,txt',
    ]);

    $file = $request->file('importFile');
    $rows = array_map('str_getcsv', file($file->getRealPath()));
    $rows = array_slice($rows, 7); // Skip headers or first lines

    $previewData = [];

    foreach ($rows as $row) {
        // ✅ Skip if row is completely empty
        if (empty(array_filter($row))) {
            continue;
        }

        $idNumber = trim($row[1] ?? '');
        $existingStudent = Student::where('id_number', $idNumber)->first();

        // ✅ Handle birth date safely
        $birthDateFormatted = '';
        if (!empty($row[9])) {
            try {
                $birthDateFormatted = Carbon::createFromFormat('m/d/Y', trim($row[9]))->format('Y-m-d');
            } catch (\Exception $e) {
                $birthDateFormatted = ''; // leave blank if invalid
            }
        }

        $rowData = [
            'no'         => trim($row[0] ?? ''),
            'id_number'  => $idNumber,
            'name'       => trim($row[2] ?? ''),
            'gender'     => trim($row[3] ?? ''),
            'course'     => trim($row[4] ?? ''),
            'year'       => trim($row[5] ?? ''),
            'units'      => trim($row[6] ?? ''),
            'section'    => trim($row[7] ?? ''),
            'contact_no' => trim($row[8] ?? ''),
            'birth_date' => $birthDateFormatted,
            'address'    => trim($row[10] ?? ''),
        ];

        if (!$existingStudent) {
            $rowData['status'] = 'New';
        } else {
            $differences = [];

            foreach ($rowData as $key => $value) {
                if ($key === 'no' || $key === 'id_number') continue; // skip these in comparison

                // Normalize comparison
                $oldValue = trim((string) $existingStudent->$key);
                $newValue = trim((string) $value);

                if ($oldValue !== $newValue) {
                    $differences[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }

            if (!empty($differences)) {
                $rowData['status'] = 'Updated';

                // Log the differences
                Log::info("Student {$idNumber} has updates:", $differences);
            } else {
                $rowData['status'] = 'Duplicate';
            }
        }

        $previewData[] = $rowData;
    }

    session(['previewData' => $previewData]);

    return redirect()->back()->with('showPreview', true);
}



public function confirmImport()
{
    $previewData = session('previewData', []);
    $imported = 0;
    $skipped = 0;
    $updated = 0;

    foreach ($previewData as $row) {
        // ✅ Skip if row is completely empty or missing ID
        if (empty(array_filter($row)) || empty($row['id_number'])) {
            $skipped++;
            continue;
        }

        $student = Student::where('id_number', $row['id_number'])->first();

        // ✅ Ensure safe birth_date
        $birthDate = !empty($row['birth_date']) ? $row['birth_date'] : null;

        if (!$student) {
            Student::create([
                'no'         => $row['no'],
                'id_number'  => $row['id_number'],
                'name'       => $row['name'],
                'gender'     => $row['gender'],
                'course'     => $row['course'],
                'year'       => $row['year'],
                'units'      => $row['units'],
                'section'    => $row['section'],
                'contact_no' => $row['contact_no'],
                'birth_date' => $birthDate, // ✅ safe
                'address'    => $row['address'],
                'status'     => 'Unregistered',
            ]);
            $imported++;
        } elseif ($row['status'] === 'Updated') {
            $student->update([
                'name'       => $row['name'],
                'gender'     => $row['gender'],
                'course'     => $row['course'],
                'year'       => $row['year'],
                'units'      => $row['units'],
                'section'    => $row['section'],
                'contact_no' => $row['contact_no'],
                'birth_date' => $birthDate, // ✅ safe
                'address'    => $row['address'],
            ]);
            $updated++;
        } else {
            $skipped++;
        }
    }

    session()->forget('previewData');

    Logs::create([
        'action'      => 'Import',
        'description' => "Student import completed — Imported: $imported, Updated: $updated, Skipped: $skipped",
        'user'        => auth()->user()->name ?? 'System',
        'date_time'   => now(),
        'type'        => 'Others',
    ]);

    return redirect()->back()->with('success', "Students imported: $imported, updated: $updated, skipped: $skipped");
}


}
