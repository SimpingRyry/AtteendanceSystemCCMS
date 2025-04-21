<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrgList;

class HomeController extends Controller
{
    public function showlogin()
    {
        return view(view: 'login');
    }
    public function index()
    {

        $orgs = OrgList::all();

        $carouselSlides = $orgs->map(function ($org) {
            return [
                'image' => $org->bg_image,
                'logos' => [$org->org_logo], // assuming 1 logo per org
                'title' => $org->org_name,
                'subtitle' => $org->description
            ];
        });

        return view('home', compact('carouselSlides'));
    }
}
