<?php

namespace App\Http\Controllers;
use ConsoleTVs\Charts\Commands\ChartsCommand;
use ConsoleTVs\Charts\Facades\Charts;

use Fx3costa\Laravelchartjs\Chartjs;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */



    public function index()
    {
        // ExampleController.php
        // $chartjs = ChartsCommand::create('line', 'chartjs')
        // ->title('My Line Chart')
        // ->labels(['January', 'February', 'March', 'April'])
        // ->values([10, 25, 13, 40])
        // ->dimensions(500, 300)
        // ->responsive(true);

    return view('home');


//  return view('home', compact('chartjs'));

    }

}
