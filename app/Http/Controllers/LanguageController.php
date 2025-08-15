<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function switch(Request $request)
    {
        $language = $request->input('language');
        
        if (in_array($language, ['en', 'ne'])) {
            Session::put('locale', $language);
            App::setLocale($language);
        }
        
        return redirect()->back();
    }
}