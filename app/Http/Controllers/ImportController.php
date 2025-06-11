<?php

namespace App\Http\Controllers;

use App\Models\OrgList;
use App\Models\Student;
use Illuminate\Http\Request;

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
    public function show()
    {
        $students = Student::all();  // Get all students from the database
        $org_list = OrgList::where('org_name', '!=', 'CCMS Student Government')->get(); 
    
        return view('student',compact('students','org_list'));  // Pass the students to the view
    }

    public function showUnregistered()
{
    $students = Student::where('status', 'Unregistered')->get(); // Get only Unregistered students
    return view('registration', compact('students')); // Pass the filtered students to the view
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
        $idNumber = $row[1] ?? null;
        $existing = Student::where('id_number', $idNumber)->exists();

        $previewData[] = [
            'no' => $row[0] ?? '',
            'id_number' => $idNumber,
            'name' => $row[2] ?? '',
            'gender' => $row[3] ?? '',
            'course' => $row[4] ?? '',
            'year' => $row[5] ?? '',
            'units' => $row[6] ?? '',
            'section' => $row[7] ?? '',
            'contact_no' => $row[8] ?? '',
            'birth_date' => $row[9] ?? '',
            'address' => $row[10] ?? '',
            'status' => $existing ? 'Duplicate' : 'New',
        ];
    }

    // Store in session temporarily
    session(['previewData' => $previewData]);

    return redirect()->back()->with('showPreview', true);
}

public function confirmImport()
{
    $previewData = session('previewData', []);
    $imported = 0;
    $skipped = 0;

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
                'birth_date' => \Carbon\Carbon::createFromFormat('m/d/Y', $row['birth_date'])->format('Y-m-d'),
                'address' => $row['address'],
                'status' => 'Unregistered',
            ]);
            $imported++;
        } else {
            $skipped++;
        }
    }

    session()->forget('previewData');

    return redirect()->back()->with('success', "Students imported: $imported, Students skipped: $skipped");
}
}
