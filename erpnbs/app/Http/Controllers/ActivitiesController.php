<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerEmail;
use App\Models\CustomerMeeting;
use App\Models\CustomerTask;
use App\Models\CustomerVisit;
use App\Models\CustomerCall;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\DealEmail;
use App\Models\DealMeeting;
use App\Models\DealTask;
use App\Models\DealVisit;
use App\Models\DealCall;
use App\Models\Lead;
use App\Models\LeadEmail;
use App\Models\LeadMeeting;
use App\Models\LeadTasks;
use App\Models\LeadView;
use App\Models\LeadCall;
class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::orderBy('id', 'desc')->get();
        $leads = Lead::orderBy('id', 'desc')->get();
        $deals = Deal::orderBy('id', 'desc')->get();
        $customerTask = CustomerTask::orderBy('id', 'desc')->get();
        $leadTask = LeadTasks::orderBy('id', 'desc')->get();
        $dealTask = DealTask::orderBy('id', 'desc')->get();
        $customerEmail = CustomerEmail::orderBy('id', 'desc')->get();
        $leadEmail = LeadEmail::orderBy('id', 'desc')->get();
        $dealEmail = DealEmail::orderBy('id', 'desc')->get();
        $customerCall = CustomerCall::orderBy('id', 'desc')->get();
        $leadCall = LeadCall::orderBy('id', 'desc')->get();
        $dealCall = DealCall::orderBy('id', 'desc')->get();
        $customerVisit = CustomerVisit::orderBy('id', 'desc')->get();
        $leadVisit = LeadView::orderBy('id', 'desc')->get();
        $dealVisit = DealVisit::orderBy('id', 'desc')->get();
        $customerMeeting = CustomerMeeting::orderBy('id', 'desc')->get();
        $leadMeeting = LeadMeeting::orderBy('id', 'desc')->get();
        $dealMeeting = DealMeeting::orderBy('id', 'desc')->get();

        return view('activities.index',
        compact(
            'customerTask',
            'leadTask',
            'dealTask',
            'dealEmail',
            'leadEmail',
            'customerEmail',
            'dealCall',
            'leadCall',
            'customerCall',
            'dealVisit',
            'leadVisit',
            'customerVisit',
            'dealMeeting',
            'leadMeeting',
            'customerMeeting',
            'customers',
            'leads',
            'deals'
        ));
        // return view('activities.index', compact('customers','leads','deals'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function task()
    {
        // dd('hi');


    }
}
