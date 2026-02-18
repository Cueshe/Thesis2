<?php

namespace App\Http\Controllers;


class PronunciationWordController extends Controller
{
    /**
     * Display the pronunciation tutor dashboard for students
     */
    public function index()
    {
        return view('pronunciation-tutor');
    }

    /**
     * Display the English pronunciation tutor dashboard
     */
    public function english()
    {
        return view('pronunciation-tutor-english');
    }

    /**
     * Display the Filipino pronunciation tutor dashboard
     */
    public function filipino()
    {
        return view('pronunciation-tutor-filipino');
    }

}
