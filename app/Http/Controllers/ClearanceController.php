<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrgList;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClearanceController extends Controller
{

public function index()
{
    $authUser = Auth::user();
    $authOrgName = $authUser->org;
    $userRole = $authUser->role;

    // Get the org record including its children
    $org = OrgList::where('org_name', $authOrgName)->with('children')->first();

    // Get the names of all children orgs (for parent org view)
    $childOrgNames = $org?->children->pluck('org_name')->toArray() ?? [];

    // Determine if parent org (has children)
    $isParentOrg = !empty($childOrgNames);

    $studentsQuery = User::query();

    if ($userRole !== 'Super Admin') {
        if ($isParentOrg) {
            // If current org is a parent: fetch members from all child orgs
            $studentsQuery->whereIn('org', $childOrgNames)
                          ->where('role', 'Member');
        } else {
            // Otherwise, fetch members from the current (child) org
            $studentsQuery->where('org', $authOrgName)
                          ->where('role', 'Member');
        }
    } else {
        // Super Admin: show all with student list
        $studentsQuery->whereHas('studentList');
    }

    // Fetch the students along with transactions tied to the current org (not children)
    $students = $studentsQuery
        ->with([
            'studentList',
            'transactions' => function ($query) use ($authOrgName) {
                $query->where('org', $authOrgName);
            }
        ])
        ->get()
        ->unique('student_id');

    // For filters or dropdowns
    $organizations = OrgList::pluck('org_name');
    $courses = Student::distinct()->pluck('course');
    $sections = Student::distinct()->pluck('section');

    return view('clearance', compact('students', 'organizations', 'courses', 'sections'));
}






public function generate($id)
{
    // Get current academic term from settings
    $term = Setting::where('key', 'academic_term')->value('value');

    // Fetch the student with org and transactions
    $student = User::whereHas('studentList')
        ->with(['studentList', 'transactions'])
        ->where('student_id', $id)
        ->firstOrFail();

    // Access balance
    $balance = $student->studentList->balance ?? 0;

    // Only allow if eligible
    if ($balance > 0) {
        return back()->with('error', 'Student is not eligible for clearance.');
    }

    // Use the authenticated user's org instead of the student's org
    $authOrg = Auth::user()->org;

    // Query the Organization table (orglist) by name
    $organization = OrgList::where('org_name', $authOrg)->first();
    $orgLogo = $organization?->org_logo ?? null;

    // Get President for the same organization and term
    $president = User::where('role', 'like', '%President%')
        ->where('term', $term)
        ->where('org', $authOrg)
        ->select('name', 'role')
        ->first();

    $vicePresident = User::where('role', 'like', '%Vice President%')
        ->where('term', $term)
        ->where('org', $authOrg)
        ->select('name', 'role')
        ->first();

    // Capitalize names if found
    if ($president) {
        $president->name = Str::upper($president->name);
    }

    if ($vicePresident) {
        $vicePresident->name = Str::upper($vicePresident->name);
    }

    // Generate and return the PDF
    $pdf = Pdf::loadView('clearance.certificate', compact(
        'student',
        'orgLogo',
        'president',
        'vicePresident'
    ));

    return $pdf->stream('clearance_certificate.pdf');
}

    public function showFinanceClearance(Request $request)
{
    // Get filter values from request
    $selectedOrg = $request->input('org');
    $selectedProgram = $request->input('program');

    // Fetch unique filter options
    $orgs = DB::table('finance_data')->select('org')->distinct()->pluck('org');
    $programs = DB::table('finance_data')->select('program')->distinct()->pluck('program');

    // Base query
    $query = DB::table('finance_data')
        ->select('student_id', 'org', 'program', DB::raw('SUM(amount) as total_fines'))
        ->groupBy('student_id', 'org', 'program');

    // Apply filters if selected
    if ($selectedOrg) {
        $query->where('org', $selectedOrg);
    }

    if ($selectedProgram) {
        $query->where('program', $selectedProgram);
    }

    $financeSummary = $query->get();

    return view('clearance', compact('financeSummary', 'orgs', 'programs', 'selectedOrg', 'selectedProgram'));
}

public function downloadPDF(Request $request)
{
    $query = DB::table('finance_data')
        ->select('student_id', 'org', 'program', DB::raw('SUM(amount) as total_fines'))
        ->groupBy('student_id', 'org', 'program');

    if ($request->filled('org')) {
        $query->where('org', $request->org);
    }

    if ($request->filled('program')) {
        $query->where('program', $request->program);
    }

    $financeSummary = $query->get();

    $date = now()->format('F j, Y');

    $pdf = Pdf::loadView('report.clearance_report', [
        'financeSummary' => $financeSummary,
        'date' => $date
    ]);

    return $pdf->download('Finance_Clearance_Report_' . now()->format('Ymd_His') . '.pdf');
}
}
