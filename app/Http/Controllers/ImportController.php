<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\OrgList;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    foreach ($rows as $row) {
        // Prepare birth date format
        $birthDate = \Carbon\Carbon::createFromFormat('m/d/Y', $row[9])->format('Y-m-d');

        // Check if student already exists by id_number
        $student = Student::where('id_number', $row[1] ?? null)->first();

        if ($student) {
            // Only update if any of the fields (except status) have changed
            $changes = [
                'no' => $row[0] ?? null,

    'name' => $row[2] ?? null,
    'gender' => $row[3] ?? null,
    'course' => $row[4] ?? null,
    'year' => $row[5] ?? null,
    'units' => $row[6] ?? null,
    'section' => $row[7] ?? null,
    'contact_no' => $row[8] ?? null,
    'birth_date' => $birthDate,
    'address' => $row[10] ?? null,
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
                'no' => $row[0] ?? null,
                'id_number' => $row[1] ?? null,
                'name' => $row[2] ?? null,
                'gender' => $row[3] ?? null,
                'course' => $row[4] ?? null,
                'year' => $row[5] ?? null,
                'units' => $row[6] ?? null,
                'section' => $row[7] ?? null,
                'contact_no' => $row[8] ?? null,
                'birth_date' => $birthDate,
                'address' => $row[10] ?? null,
                'status' => 'Unregistered'
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

    $students = $query->paginate(10); // enable pagination
    $org_list = OrgList::all();
    $sections = Student::select('section')->distinct()->pluck('section')->filter()->sort()->values();
    $years = Student::select('year')->distinct()->pluck('year')->filter()->sort()->values();

    return view('student', compact('students', 'org_list', 'sections', 'years'));
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
        $idNumber = trim($row[1] ?? '');
        $existingStudent = Student::where('id_number', $idNumber)->first();

        $birthDateFormatted = isset($row[9]) ? Carbon::createFromFormat('m/d/Y', trim($row[9]))->format('Y-m-d') : '';

        $rowData = [
            'no' => trim($row[0] ?? ''),
            'id_number' => $idNumber,
            'name' => trim($row[2] ?? ''),
            'gender' => trim($row[3] ?? ''),
            'course' => trim($row[4] ?? ''),
            'year' => trim($row[5] ?? ''),
            'units' => trim($row[6] ?? ''),
            'section' => trim($row[7] ?? ''),
            'contact_no' => trim($row[8] ?? ''),
            'birth_date' => $birthDateFormatted,
            'address' => trim($row[10] ?? ''),
        ];

        if (!$existingStudent) {
            $rowData['status'] = 'New';
        } else {
            $differences = [];

            foreach ($rowData as $key => $value) {
                if ($key === 'no' || $key === 'id_number') continue; // Skip these in comparison

                // Normalize comparison for numbers
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
        $student = Student::where('id_number', $row['id_number'])->first();

        if (!$student) {
            Student::create([
                'no' => $row['no'],
                'id_number' => $row['id_number'],
                'name' => $row['name'],
                'gender' => $row['gender'],
                'course' => $row['course'],
                'year' => $row['year'],
                'units' => $row['units'],
                'section' => $row['section'],
                'contact_no' => $row['contact_no'],
                'birth_date' => $row['birth_date'], // Already formatted
                'address' => $row['address'],
                'status' => 'Unregistered',
            ]);
            $imported++;
        } elseif ($row['status'] === 'Updated') {
            $student->update([
                'name' => $row['name'],
                'gender' => $row['gender'],
                'course' => $row['course'],
                'year' => $row['year'],
                'units' => $row['units'],
                'section' => $row['section'],
                'contact_no' => $row['contact_no'],
                'birth_date' => $row['birth_date'], // Already formatted
                'address' => $row['address'],
            ]);
            $updated++;
        } else {
            $skipped++;
        }
    }

    session()->forget('previewData');

    return redirect()->back()->with('success', "Students imported: $imported, updated: $updated, skipped: $skipped");
}


}
