<?php

namespace App\Http\Controllers;

use App\Invoices;
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
        /*
            to get Percentage : count of Element inside invoices  / Count of Invoices * 100 
            Invoices::where('value_status', 1)->count() / $count_all * 100
        */
        
        $count_all =Invoices::count();
        $count_invoices1 = Invoices::where('value_status', 1)->count();
        $count_invoices2 = Invoices::where('value_status', 2)->count();
        $count_invoices3 = Invoices::where('value_status', 3)->count();

        if($count_invoices2 == 0){ $nspainvoices2=0; } else{ $nspainvoices2 = $count_invoices2/ $count_all*100; }

        if($count_invoices1 == 0){ $nspainvoices1=0; } else{ $nspainvoices1 = $count_invoices1/ $count_all*100; }

        if($count_invoices3 == 0){ $nspainvoices3=0; } else{ $nspainvoices3 = $count_invoices3/ $count_all*100; }
        // Pie Chart
        $chartjs = app()->chartjs
        ->name('pieChartTest')
        ->type('pie')
        ->size(['width' => 340, 'height' => 200])
        ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة'])
        ->datasets([
            [
                'backgroundColor' => ['#ec5858', '#81b214','#ff9642'],
                'data' => [$nspainvoices2, $nspainvoices1]
            ]
        ])
        ->options([]);
        //Bar Chart
        $chartbarjs  = app()->chartjs
        ->name('barChartTest')
        ->type('bar')
        ->size(['width' => 350, 'height' => 200])
        ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة'])
        ->datasets([
            [
                "label" => "الفواتير الغير المدفوعة",
                'backgroundColor' => ['#ec5858'],
                'data' => [100,50]
            ],
            [
                "label" => "الفواتير المدفوعة",
                'backgroundColor' => ['#81b214'],
                'data' => [50]
            ]


        ])
        ->options([]);
        return view('home',compact('chartjs','chartbarjs'));
    }
}
