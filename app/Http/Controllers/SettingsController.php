<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Setting;
use App\Models\FineHistory;
use App\Models\OfficerRole;
use App\Models\DeliveryUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class SettingsController extends Controller
{
    // Display the settings page with current values
// Make sure to import this at the top

public function index()
{
    $authOrg = Auth::user()->org;

    // Get fine settings
    $fines = DB::table('fine_settings')
        ->where('org', $authOrg)
        ->first();

    // Get current academic term full record
    $academicTermRecord = Setting::where('key', 'academic_term')->first();
    $academicTerm = $academicTermRecord->value ?? null;

    // Get acad_code from the academic term record
    $acadCode = $academicTermRecord->acad_code ?? null;

    // Get fine history filtered by org and acad code
    $history = FineHistory::where('org', $authOrg)
        ->where('acad_code', $acadCode) // assuming your table has this column
        ->orderBy('changed_at', 'desc')
        ->get();

    $deliveryUnits = DeliveryUnit::with('courses')->get();
    $officerRoles = OfficerRole::where('org', $authOrg)->get();

    return view('config', compact(
        'fines',
        'history',
        'academicTerm',
        'acadCode',
        'deliveryUnits',
        'officerRoles'
    ));
}
public function searchFineHistory($acadCode)
{
    $authOrg = Auth::user()->org;

    $history = FineHistory::where('org', $authOrg)
        ->where('acad_code', $acadCode)
        ->orderBy('changed_at', 'desc')
        ->get()
        ->map(function ($record) {
            return [
                'type' => $record->type,
                'amount' => $record->amount,
                'updated_by_name' => $record->updated_by ? User::find($record->updated_by)->name : null,
                'changed_at' => Carbon::parse($record->changed_at)->format('M d, Y h:i A')
            ];
        });

    return response()->json($history);
}
    
public function destroy($id)
{
    $role = OfficerRole::findOrFail($id);

    // Optional: ensure the adviser can only delete roles from their own org
    if ($role->org_id !== Auth::user()->org) {
        return redirect()->back()->with('error', 'Unauthorized to delete this role.');
    }

    $role->delete();

    return redirect()->back()->with('success', 'Officer role deleted successfully.');
}
public function updateFines(Request $request)
{
    $request->validate([
        'absent_member' => 'required|numeric|min:0',
        'absent_officer' => 'required|numeric|min:0',
        'late_member' => 'required|numeric|min:0',
        'late_officer' => 'required|numeric|min:0',
        'grace_period_minutes' => 'required|integer|min:0',
        // Optional validation for time inputs (future-proof)
        'morning_in' => 'nullable|date_format:H:i',
        'morning_out' => 'nullable|date_format:H:i',
        'afternoon_in' => 'nullable|date_format:H:i',
        'afternoon_out' => 'nullable|date_format:H:i',
    ]);

    $orgName = auth()->user()->org;

    $data = [
        'absent_member' => $request->absent_member,
        'absent_officer' => $request->absent_officer,
        'late_member' => $request->late_member,
        'late_officer' => $request->late_officer,
        'grace_period_minutes' => $request->grace_period_minutes,
        'updated_at' => now(),
    ];

    // Only include time fields if columns exist (for now, just prepare)
    $timeFields = ['morning_in', 'morning_out', 'afternoon_in', 'afternoon_out'];
    foreach ($timeFields as $field) {
        if ($request->has($field)) {
            $data[$field] = $request->$field; // Won't error even if column not yet added
        }
    }

    $existing = DB::table('fine_settings')->where('org', $orgName)->first();

    if ($existing) {
        DB::table('fine_settings')->where('org', $orgName)->update($data);
    } else {
        $data['org'] = $orgName;
        $data['created_at'] = now();
        DB::table('fine_settings')->insert($data);
    }

    // Fine history (only for monetary values)
    $fineTypes = [
        'Absent Fine (Member)' => $request->absent_member,
        'Absent Fine (Officer)' => $request->absent_officer,
        'Late Fine (Member)' => $request->late_member,
        'Late Fine (Officer)' => $request->late_officer,
    ];

    foreach ($fineTypes as $type => $amount) {
        FineHistory::create([
            'type' => $type,
            'amount' => $amount,
            'updated_by' => auth()->id(),
            'org' => $orgName,
            'changed_at' => now(),
        ]);
    }

    return redirect()->back()->with('success', 'Fine settings updated successfully.');
}

public function updateAcademic(Request $request)
{
    $request->validate([
        'academic_term' => 'required|string|max:255',
    ]);

    $academicTerm = $request->academic_term;

    // Get code part before any space (e.g., "24-1" from "24-1 First Semester")
    $acadCode = explode(' ', $academicTerm)[0];

    Setting::updateOrCreate(
        ['key' => 'academic_term'], // look up by key instead of id
        [
            'value' => $academicTerm, // update the "value" column
            'acad_code' => $acadCode,
        ]
    );

    return redirect()->back()->with('success', 'Academic term updated successfully!');
}

 public function storeUnit(Request $request)
    {
        $request->validate([
            'unit_name' => 'required|string|max:255',
            'unit_description' => 'nullable|string',
        ]);

        DeliveryUnit::create([
            'name' => $request->unit_name,
            'description' => $request->unit_description,
        ]);

        return back()->with('success', 'Delivery unit added successfully.');
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:delivery_units,id',
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:20|unique:courses,code',
        ]);

        Course::create([
            'delivery_unit_id' => $request->unit_id,
            'name' => $request->course_name,
            'code' => $request->course_code,
        ]);

        return back()->with('success', 'Course added successfully.');
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    OfficerRole::create([
        'org' => Auth::user()->org,
        'title' => $request->title,
        'description' => $request->description,
    ]);

    return back()->with('success', 'Officer role added successfully.');
}
}
