<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrgList;
use App\Models\Event;
use Carbon\Carbon;
class HomeController extends Controller
{
    public function showlogin()
    {
        return view(view: 'login');
    }
    public function index()
    {

        $organizations = OrgList::all(); // fetch all orgs
           $events = Event::where('event_date', '>=', Carbon::today())
        ->orderBy('event_date', 'asc')
        ->take(3)
        ->get();

    return view('Home-Page', compact('organizations', 'events'));
    }

       public function show($id)
    {
        $organization = OrgList::findOrFail($id);
          $organizations = OrgList::all(); // fetch all orgs
        return view('organization', compact('organization', 'organizations'));
    }
}
