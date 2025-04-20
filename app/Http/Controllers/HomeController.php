<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function showlogin()
    {
        return view(view: 'login'); 
    }
    public function index()
    {
        $carouselSlides = [
            [
                'image' => 'carousel_image1.jpg',
                'logos' => ['ccms_logo.png'],
                'title' => 'College of Computing and Multimedia Studies',
                'subtitle' => 'Attendance System'
            ],
            [
                'image' => 'carousel_image2.jpg',
                'logos' => ['sg-logo.png'],
                'title' => 'Student Government',
                'subtitle' => 'an organization in CNSC CCMS'
            ],
            [
                'image' => 'carousel_image3.jpg',
                'logos' => ['ITS_LOGO.png'],
                'title' => 'Information Technology Society',
                'subtitle' => 'an organization in CNSC CCMS'
            ],
            [
                'image' => 'carousel1_img3.jpg',
                'logos' => ['praxis_logo.png'],
                'title' => 'Proficient Architects of Information Systems',
                'subtitle' => 'an organization in CNSC CCMS'
            ],
        ];
    
        return view('home', compact('carouselSlides'));
    }
}
