<?php

namespace App\Http\Controllers;

use App\Mail\SendDealEmail;
use App\Models\ActivityLog;
use App\Models\ClientDeal;
use App\Models\ClientPermission;
use App\Models\CustomField;
use App\Models\Deal;
use App\Models\DealCall;
use App\Models\DealDiscussion;
use App\Models\DealEmail;
use App\Models\DealFile;
use App\Models\DealTask;
use App\Models\Label;
use App\Models\Pipeline;
use App\Models\ProductService;
use App\Models\Source;
use App\Models\Stage;
use App\Models\User;
use App\Models\UserDeal;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Exports\DealExport;
use App\Imports\DealImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\LeadStage;
use App\Models\Lead;
use App\Models\AllFilter;
use App\Models\DealEmailTemplate;
use App\Models\Employee;
use App\Models\DealMeeting;
use App\Models\DealVisit;
use App\Models\Hierarchy;
use App\Models\Customer;
use App\Models\CustomerStage;

class DealController extends Controller
{
    /**
     * Display a listing of the redeal.
     *
     * @return \Illuminate\Http\Response
     */
    private static $dealData = NULL;

    public function index()
    {
        $usr      = \Auth::user();

        if($usr->can('manage deal'))
        {
            if($usr->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', '=', $usr->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
            }

            // $pipelines = Pipeline::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id');

            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()
            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            // dd($userPermissions);

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines->pluck('name', 'id');

            if($usr->type == 'client')
            {
                $id_deals = $usr->clientDeals->pluck('id');
            }
            else
            {
                $id_deals = $usr->deals->pluck('id');
            }

            $deals       = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->get();
            $curr_month  = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereMonth('created_at', '=', date('m'))->get();
            $curr_week   = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereBetween(
                'created_at', [
                                \Carbon\Carbon::now()->startOfWeek(),
                                \Carbon\Carbon::now()->endOfWeek(),
                            ]
            )->get();
            $last_30days = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereDate('created_at', '>', \Carbon\Carbon::now()->subDays(30))->get();
            // Deal Summary
            $cnt_deal                = [];
            $cnt_deal['total']       = Deal::getDealSummary($deals);
            $cnt_deal['this_month']  = Deal::getDealSummary($curr_month);
            $cnt_deal['this_week']   = Deal::getDealSummary($curr_week);
            $cnt_deal['last_30days'] = Deal::getDealSummary($last_30days);

            $allfilter = AllFilter::where('saved_by', \Auth::user()->id)
                      ->where('page_name', 'deal')
                      ->where('pipeline_id', $pipeline->id)
                      ->get();


            // Decode the 'criteria' field for each filter to ensure it's in the proper format
            $allfilter = $allfilter->map(function ($filter) {
                // Decode the JSON string in the 'criteria' field
                $filter->criteria = json_decode($filter->criteria, true);
                return $filter;
            });
            $stages = $pipeline->Stages->pluck('name', 'id');
            $sources = Source::all()->pluck('name', 'id');
            $users = User::all()->pluck('name', 'id');
            $clients      = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', 'client')->get()->pluck('name', 'id');

            return view('deals.index', compact('pipelines', 'pipeline', 'cnt_deal','allfilter','stages','sources','users', 'clients'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function deal_list()
    {
        $usr = \Auth::user();
        if($usr->can('manage deal'))
        {
            if($usr->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', '=', $usr->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
            }

            $pipelines = Pipeline::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id');

            if($usr->type == 'client')
            {
                $id_deals = $usr->clientDeals->pluck('id');
            }
            else
            {
                $id_deals = $usr->deals->pluck('id');
            }

            $deals       = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->get();
            $curr_month  = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereMonth('created_at', '=', date('m'))->get();
            $curr_week   = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereBetween(
                'created_at', [
                                \Carbon\Carbon::now()->startOfWeek(),
                                \Carbon\Carbon::now()->endOfWeek(),
                            ]
            )->get();
            $last_30days = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereDate('created_at', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Deal Summary
            $cnt_deal                = [];
            $cnt_deal['total']       = Deal::getDealSummary($deals);
            $cnt_deal['this_month']  = Deal::getDealSummary($curr_month);
            $cnt_deal['this_week']   = Deal::getDealSummary($curr_week);
            $cnt_deal['last_30days'] = Deal::getDealSummary($last_30days);

            // Deals
            if($usr->type == 'client')
            {
                $deals = Deal::select('deals.*')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', $usr->id)->where('deals.pipeline_id', '=', $pipeline->id)->orderBy('deals.order')->get();
            }
            else
            {
                $deals = Deal::select('deals.*')->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', $usr->id)->where('deals.pipeline_id', '=', $pipeline->id)->orderBy('deals.order')->get();
            }

            return view('deals.list', compact('pipelines', 'pipeline', 'deals', 'cnt_deal'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new redeal.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('create deal'))
        {
            $clients      = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', 'client')->get()->pluck('name', 'id');
            $clients->prepend(__('Select Client'), '');

            $customFields = CustomField::where('module', '=', 'deal')->get();

            $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
            $users->prepend(__('Select Users'), '');

            $user = \Auth::user();
            // Generate the deal name: user initials + month-year + deal number
            $initials = strtoupper(substr($user->name, 0, 2));
            $monthYear = date('my'); // Current month and year in format MMYY
            $dealCount = Deal::count() + 1; // Assuming Deal is your model for deals, get the next deal number

            $generatedDealName = $initials . '-' . $monthYear . '-' . str_pad($dealCount, 3, '0', STR_PAD_LEFT); // Auto-generated deal name

            $lead_stages = LeadStage::select('lead_stages.*', 'pipelines.name as pipeline')->join('pipelines', 'pipelines.id', '=', 'lead_stages.pipeline_id')->where('pipelines.created_by', '=', \Auth::user()->ownerId())->where('lead_stages.created_by', '=', \Auth::user()->ownerId())->orderBy('lead_stages.pipeline_id')->orderBy('lead_stages.order')->get();
            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines;
            $stages = Stage::all();
            $customerStages = CustomerStage::all();

            $leads = Lead::where('created_by', $user->id)->get()->pluck('name', 'id');
            $leads->prepend(__('Select Leads'), '');

            $sources = Source::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
            // dd($pipelines);

            // dd($customerStages);
            return view('deals.create', compact('clients', 'customFields','users','generatedDealName','pipelines','leads','sources', 'stages','customerStages'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created redeal in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $usr = \Auth::user();
        if($usr->can('create deal'))
        {
            $countDeal = Deal::where('created_by', '=', $usr->ownerId())->count();
            $validator = \Validator::make(
                $request->all(), [
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }


            $pipeline = Pipeline::where('id', $request->pipeline_id)->first();
            $stage = Stage::where('id', $request->stage_id)->first();



            // dd($request->all());


                $deal        = new Deal();
                $deal->name = $request->generated_deal_name .
                (!empty($request->deal_name_additional) ? ' ' . $request->deal_name_additional : '');                // $deal->phone = $request->phone;
                if(empty($request->price))
                {
                    $deal->price = 0;
                }
                else
                {
                    $deal->price = $request->price;
                }
                $deal->pipeline_id = $request->pipeline_id;
                $deal->sources    = $request->deal_source;
                $deal->stage_id    = $request->stage_id;
                $deal->status      = 'Active';
                $deal->created_by  = $usr->id;

                $deal->deal_owner           = $request->deal_owner ?? $usr->id;
                if($request->create_from=='from_leads'){
                    $lead = Lead::where('id', $request->lead_id)->first();
                    $deal->company_website      = $lead->company_website;
                    $deal->company_entity_name  = $lead->company_entity_name;

                    $deal->company_entity_logo = $lead->company_entity_logo;
                    $deal->company_phone_ll1    = $lead->company_phone_ll1;
                    $deal->company_phone_ll2    = $lead->company_phone_ll2;
                    $deal->company_email        = $lead->company_email;
                    $deal->address1             = $lead->address1;
                    $deal->address2             = $lead->address2;
                    $deal->city                 = $lead->city;
                    $deal->region               = $lead->region;
                    $deal->country              = $lead->country;
                    $deal->zip_code             = $lead->zip_code;
                    $deal->company_linkedin     = $lead->company_linkedin;
                    $deal->company_location     = $lead->company_location;

                    // Primary Contact Info
                    $deal->salutation           = $lead->salutation;
                    $deal->first_name           = $lead->first_name;
                    $deal->last_name            = $lead->last_name;
                    $deal->mobile_primary       = $lead->mobile_primary;
                    $deal->mobile_secondary     = $lead->mobile_secondary;
                    $deal->email_work           = $lead->email_work;
                    $deal->email_personal       = $lead->email_personal;
                    $deal->phone_ll             = $lead->phone_ll;
                    $deal->company_phone_ll     = $lead->company_phone_ll;
                    $deal->extension            = $lead->extension;
                    $deal->linkedin_profile     = $lead->linkedin_profile;


                    $deal->currency             = $lead->currency;
                    $deal->industry             = $lead->industry;
                    $deal->note                 = $lead->note;
                    $deal->additional_contacts = $lead->additional_contacts;


                    $customerData = [
                        'name' => $lead->name,
                        'email' => $lead->email,
                        'contact' => $lead->company_phone_ll1,
                        'created_by' => $lead->created_by,
                        'is_active' => $lead->is_active,
                        'pipeline_id' => $request->customer_pipeline_id,
                        'stage_id' => $request->customer_stage_id,
                        'sources' => $lead->sources,
                        'products' => $lead->products,

                        'company_website' => $lead->company_website,
                        'company_entity_name' => $lead->company_entity_name,
                        'company_entity_logo' => $lead->company_entity_logo,
                        'company_phone_ll1' => $lead->company_phone_ll1,
                        'company_phone_ll2' => $lead->company_phone_ll2,
                        'company_email' => $lead->company_email,
                        'address1' => $lead->address1,
                        'address2' => $lead->address2,
                        'city' => $lead->city,
                        'region' => $lead->region,
                        'country' => $lead->country,
                        'zip_code' => $lead->zip_code,
                        'company_linkedin' => $lead->company_linkedin,
                        'company_location' => $lead->company_location,


                        'salutation' => $lead->salutation,
                        'first_name' => $lead->first_name,
                        'last_name' => $lead->last_name,
                        'mobile_primary' => $lead->mobile_secondary,
                        'mobile_secondary' => $lead->mobile_secondary,
                        'email_work' => $lead->email_work,
                        'email_personal' => $lead->email_personal,
                        'phone_ll' => $lead->phone_ll,
                        'company_phone_ll' => $lead->company_phone_ll,
                        'extension' => $lead->extension,
                        'linkedin_profile' => $lead->linkedin_profile,
                        'notes' => $lead->notes,
                        'additional_contacts' => $lead->additional_contacts,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // dd($customerData);
                    $customer=Customer::create($customerData);

                    $lead->delete();

                }
                else{
                    $deal->company_website      = $request->company_website;
                    $deal->company_entity_name  = $request->company_entity_name;

                    if ($request->hasFile('company_entity_logo')) {
                        $path = $request->file('company_entity_logo')->store('deals', 'public');
                        $deal->company_entity_logo = $path;
                    }
                    $deal->company_phone_ll1    = $request->company_phone_ll1;
                    $deal->company_phone_ll2    = $request->company_phone_ll2;
                    $deal->company_email        = $request->company_email;
                    $deal->address1             = $request->address1;
                    $deal->address2             = $request->address2;
                    $deal->city                 = $request->city;
                    $deal->region               = $request->region;
                    $deal->country              = $request->country;
                    $deal->zip_code             = $request->zip_code;
                    $deal->company_linkedin     = $request->company_linkedin;
                    $deal->company_location     = $request->company_location;

                    // Primary Contact Info
                    $deal->salutation           = $request->salutation;
                    $deal->first_name           = $request->first_name;
                    $deal->last_name            = $request->last_name;
                    $deal->mobile_primary       = $request->mobile_primary;
                    $deal->mobile_secondary     = $request->mobile_secondary;
                    $deal->email_work           = $request->email_work;
                    $deal->email_personal       = $request->email_personal;
                    $deal->phone_ll             = $request->phone_ll;
                    $deal->company_phone_ll     = $request->company_phone_ll;
                    $deal->extension            = $request->extension;
                    $deal->linkedin_profile     = $request->linkedin_profile;


                    $deal->currency             = $request->currency;
                    $deal->industry             = $request->industry;
                    $deal->note                 = $request->note;
                    $deal->additional_contacts = json_encode($request->additional_contacts);
                }
                // dd($deal);

                $deal->save();

                // dd($deal);
                if($request->create_from=='from_leads'){
                    // $clients = User::whereIN('id', array_filter($request->deal_owner))->get()->pluck('email', 'id')->toArray();

                }
                else{
                    $clients = User::whereIN('id', array_filter($request->clients))->get()->pluck('email', 'id')->toArray();

                }

                    $dealArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                ];

                $dArr = [
                    'deal_name' => $deal->name,
                    'deal_pipeline' => $pipeline->name,
                    'deal_stage' => $stage->name,
                    'deal_status' => $deal->status,
                    'deal_price' => $usr->priceFormat($deal->price),
                ];

                if($request->create_from !='from_leads'){

                foreach(array_keys($clients) as $client)
                {
                    ClientDeal::create(
                        [
                            'deal_id' => $deal->id,
                            'client_id' => $client,
                        ]
                    );
                }
            }

                if($usr->type=='company'){
                    $usrDeals = [
                        $usr->id,

                    ];
                }else{
                    $usrDeals = [
                        $usr->id,
                        $usr->ownerId()
                    ];
                }

                foreach($usrDeals as $usrDeal)
                {
                    UserDeal::create(
                        [
                            'user_id' => $usrDeal,
                            'deal_id' => $deal->id,
                        ]
                    );
                }

                CustomField::saveData($deal, $request->customField);

                // Send Email
                $setings = Utility::settings();
                if($setings['deal_assigned'] == 1)
                {
                    if($request->create_from=='from_leads'){
                        // $clients = User::whereIN('id', array_filter($request->deal_owner))->get()->pluck('email', 'id')->toArray();

                    }
                    else{
                        $clients = User::whereIN('id', array_filter($request->clients))->get()->pluck('email', 'id')->toArray();

                    }
                    $dealAssignArr = [
                        'deal_name' => $deal->name,
                        'deal_pipeline' => $pipeline->name,
                        'deal_stage' => $stage->name,
                        'deal_status' => $deal->status,
                        'deal_price' => $usr->priceFormat($deal->price),
                    ];
                    if($request->create_from !='from_leads'){

                    $resp = Utility::sendEmailTemplate('deal_assigned',  $clients, $dealAssignArr);

                    }
                    if ($request->create_from == 'from_leads') {
                        return redirect()->back()->with('success', __('Deal successfully created!'));
                    }

                    return redirect()->back()->with('success', __('Deal successfully created!')  .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

                }

                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());
                $dealNotificationArr = [
                    'user_name' => \Auth::user()->name,
                    'deal_name' => $deal->name,
                ];
                //Slack Notification
                if(isset($setting['deal_notification']) && $setting['deal_notification'] ==1)
                {
                    Utility::send_slack_msg('new_deal', $dealNotificationArr);
                }
                //Telegram Notification
                if(isset($setting['telegram_deal_notification']) && $setting['telegram_deal_notification'] ==1)
                {
                    Utility::send_telegram_msg('new_deal', $dealNotificationArr);
                }

                //webhook
                $module ='New Deal';
                $webhook=  Utility::webhookSetting($module);

                if($webhook)
                {
                    $parameter = json_encode($deal);
                    $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
                    if($status == true)
                    {
                        return redirect()->back()->with('success', __('Deal successfully created!')  .((!empty ($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Webhook call failed.'));
                    }
                }

                return redirect()->back()->with('success', __('Deal successfully created!')  .((!empty ($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));

            }

        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified redeal.
     *
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Deal $deal)
    {
        if($deal->is_active)
        {
            $calenderTasks = [];
            if(\Auth::user()->can('view task'))
            {
                foreach($deal->tasks as $task)
                {
                    $calenderTasks[] = [
                        'title' => $task->name,
                        'start' => $task->date,
                        'url' => route(
                            'deals.tasks.show', [
                                                  $deal->id,
                                                  $task->id,
                                              ]
                        ),
                        'className' => ($task->status) ? 'bg-success border-success' : 'bg-warning border-warning',
                    ];
                }

            }
            $permission        = [];
            $customFields      = CustomField::where('module', '=', 'deal')->get();
            $deal->customField = CustomField::getData($deal, 'deal')->toArray();

            // dd($deal->tasks);
            $meetings = DealMeeting::where('deal_id', $deal->id)->get();
            $visits = DealVisit::where('deal_id', '=', $deal->id)->get();
            $previousDeal = Deal::where('id', '<', $deal->id)
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->first();

            $nextDeal = Deal::where('id', '>', $deal->id)
                ->where('is_active', 1)
                ->orderBy('id', 'asc')
                ->first();
                if (request()->ajax()) {
                    // Return the partial view if the request is AJAX
                    return view('deals.show-html', compact('deal', 'customFields', 'calenderTasks', 'permission','meetings','visits','previousDeal', 'nextDeal'));
                }
            return view('deals.show', compact('deal', 'customFields', 'calenderTasks', 'permission','meetings','visits','previousDeal', 'nextDeal'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified redeal.
     *
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Deal $deal)
    {
        if(\Auth::user()->can('edit deal'))
        {
            if($deal)
            {
                $user = \Auth::user();

                $pipelines         = Pipeline::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $sources           = Source::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $products          = ProductService::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');
                $deal->customField = CustomField::getData($deal, 'deal');
                $customFields      = CustomField::where('module', '=', 'deal')->get();

                $deal->sources  = explode(',', $deal->sources);
                $deal->products = explode(',', $deal->products);
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
                $generatedDealName = $deal->name;
                $generatedDealName = substr($deal->name, 0, 11);
                $deal_name_additional = substr($deal->name, 11);
                $leads = Lead::where('created_by', $user->id)->get()->pluck('name', 'id');
                $clients      = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', 'client')->get()->pluck('name', 'id');
                $stages = Stage::all();
                $pipelines = Pipeline::all(); // Fetch all pipelines
                $user = \Auth::user(); // or Auth::user()

                $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

                $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                    $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                    return in_array($permissionName, $userPermissions);
                });



                $pipelines = $filteredPipelines->pluck('name', 'id');

                return view('deals.edit', compact('deal', 'pipelines', 'sources', 'products', 'customFields','users','generatedDealName','leads','clients','stages','deal_name_additional'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified redeal in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deal $deal)
    {
        if(\Auth::user()->can('edit deal'))
        {
            if($deal)
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'pipeline_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $usr=\Auth::user();
                $deal->name = $request->generated_deal_name .
                (!empty($request->deal_name_additional) ? ' ' . $request->deal_name_additional : '');                $deal->pipeline_id = $request->pipeline_id;
                $deal->sources    = $request->deal_source;
                $deal->stage_id    = $request->stage_id;
                $deal->status      = 'Active';
                $deal->created_by  = $usr->id;
                $deal->deal_owner           = $request->deal_owner ?? $usr->id;

                if(empty($request->price))
                {
                    $deal->price = 0;
                }
                else
                {
                    $deal->price = $request->price;
                }
                $deal->company_website      = $request->company_website;
                    $deal->company_entity_name  = $request->company_entity_name;

                    if ($request->hasFile('company_entity_logo')) {
                        $path = $request->file('company_entity_logo')->store('deals', 'public');
                        $deal->company_entity_logo = $path;
                    }
                    $deal->company_phone_ll1    = $request->company_phone_ll1;
                    $deal->company_phone_ll2    = $request->company_phone_ll2;
                    $deal->company_email        = $request->company_email;
                    $deal->address1             = $request->address1;
                    $deal->address2             = $request->address2;
                    $deal->city                 = $request->city;
                    $deal->region               = $request->region;
                    $deal->country              = $request->country;
                    $deal->zip_code             = $request->zip_code;
                    $deal->company_linkedin     = $request->company_linkedin;
                    $deal->company_location     = $request->company_location;

                    // Primary Contact Info
                    $deal->salutation           = $request->salutation;
                    $deal->first_name           = $request->first_name;
                    $deal->last_name            = $request->last_name;
                    $deal->mobile_primary       = $request->mobile_primary;
                    $deal->mobile_secondary     = $request->mobile_secondary;
                    $deal->email_work           = $request->email_work;
                    $deal->email_personal       = $request->email_personal;
                    $deal->phone_ll             = $request->phone_ll;
                    $deal->company_phone_ll     = $request->company_phone_ll;
                    $deal->extension            = $request->extension;
                    $deal->linkedin_profile     = $request->linkedin_profile;


                    $deal->currency             = $request->currency;
                    $deal->industry             = $request->industry;
                    $deal->note                 = $request->note;
                $deal->save();

                CustomField::saveData($deal, $request->customField);

                return redirect()->back()->with('success', __('Deal successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified redeal from storage.
     *
     * @param \App\Deal $deal
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deal $deal)
    {
        if(\Auth::user()->can('delete deal'))
        {
            if($deal)
            {
                DealDiscussion::where('deal_id', '=', $deal->id)->delete();
                DealFile::where('deal_id', '=', $deal->id)->delete();
                ClientDeal::where('deal_id', '=', $deal->id)->delete();
                UserDeal::where('deal_id', '=', $deal->id)->delete();
                DealTask::where('deal_id', '=', $deal->id)->delete();
                ActivityLog::where('deal_id', '=', $deal->id)->delete();
//                ClientPermission::where('deal_id', '=', $deal->id)->delete();

                $deal->delete();

                return redirect()->back()->with('success', __('Deal successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function deal($id)
    {
        if(self::$dealData == null)
        {
            $deal = Deal::find($id);
            self::$dealData = $deal;
        }
        return self::$dealData;
    }

    public function order(Request $request)
    {
        $usr = \Auth::user();

        if($usr->can('move deal'))
        {
            $post       = $request->all();
            $deal       = $this->deal($post['deal_id']);
            $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $deal->id)->get()->pluck('client_id')->toArray();
            $deal_users = $deal->users->pluck('id')->toArray();
            $usrs       = User::whereIN('id', array_merge($deal_users, $clients))->get()->pluck('email', 'id')->toArray();

            if($deal->stage_id != $post['stage_id'])
            {
                $newStage = Stage::find($post['stage_id']);
                ActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Move',
                        'remark' => json_encode(
                            [
                                'title' => $deal->name,
                                'old_status' => $deal->stage->name,
                                'new_status' => $newStage->name,
                            ]
                        ),
                    ]
                );

                $dealArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                    'old_status' => $deal->stage->name,
                    'new_status' => $newStage->name,
                ];

                $dArr = [
                    'deal_name' => $deal->name,
                    'deal_pipeline' => $deal->email,
                    'deal_stage' => $deal->stage->name,
                    'deal_status' => $deal->status,
                    'deal_price' => $usr->priceFormat($deal->price),
                    'deal_old_stage' => $deal->stage->name,
                    'deal_new_stage' => $newStage->name,
                ];

                // Send Email
                Utility::sendEmailTemplate('Move Deal', $usrs, $dArr);
            }

            foreach($post['order'] as $key => $item)
            {
                $deal           = $this->deal($item);
                $deal->order    = $key;
                $deal->stage_id = $post['stage_id'];
                $deal->save();
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function labels($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $labels   = Label::where('pipeline_id', '=', $deal->pipeline_id)->where('created_by', \Auth::user()->creatorId())->get();
                $selected = $deal->labels();
                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }
                else
                {
                    $selected = [];
                }

                return view('deals.labels', compact('deal', 'labels', 'selected'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function labelStore($id, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                if($request->labels)
                {
                    $deal->labels = implode(',', $request->labels);
                }
                else
                {
                    $deal->labels = $request->labels;
                }
                $deal->save();

                return redirect()->back()->with('success', __('Labels successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userEdit($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $users = User::where('created_by', '=', \Auth::user()->ownerId())->where('type','!=','client')->whereNOTIn(
                    'id', function ($q) use ($deal){
                    $q->select('user_id')->from('user_deals')->where('deal_id', '=', $deal->id);
                }
                )->get();

                foreach($users as $key => $user)
                {
                    if(!$user->can('manage deal'))
                    {
                        $users->forget($key);
                    }
                }
                $users = $users->pluck('name', 'id');

                $users->prepend(__('Select Users'), '');

                return view('deals.users', compact('deal', 'users'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function userUpdate($id, Request $request)
    {
        $usr = \Auth::user();
        if($usr->can('edit deal'))
        {
            $deal = Deal::find($id);
            $resp = '';

            if($deal)
            {
                if(!empty($request->users))
                {
                    $users = User::whereIN('id', array_filter($request->users))->get()->pluck('email', 'id')->toArray();

                    $dealArr = [
                        'deal_id' => $deal->id,
                        'name' => $deal->name,
                        'updated_by' => $usr->id,
                    ];

                    $dArr = [
                        'deal_name' => $deal->name,
                        'deal_pipeline' => $deal->pipeline->name,
                        'deal_stage' => $deal->stage->name,
                        'deal_status' => $deal->status,
                        'deal_price' => $usr->priceFormat($deal->price),
                    ];

                    foreach(array_keys($users) as $user)
                    {
                        UserDeal::create(
                            [
                                'deal_id' => $deal->id,
                                'user_id' => $user,
                            ]
                        );
                    }

                    // Send Email
                    $resp = Utility::sendEmailTemplate('Assign Deal', $users, $dArr);
                }

                if(!empty($users) && !empty($request->users))
                {
                    return redirect()->back()->with('success', __('Users successfully updated!') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                }
                else
                {
                    return redirect()->back()->with('error', __('Please Select Valid User!'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function userDestroy($id, $user_id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                UserDeal::where('deal_id', '=', $deal->id)->where('user_id', '=', $user_id)->delete();

                return redirect()->back()->with('success', __('User successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function clientEdit($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $clients = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', 'client')->whereNOTIn(
                    'id', function ($q) use ($deal){
                    $q->select('client_id')->from('client_deals')->where('deal_id', '=', $deal->id);
                }
                )->get()->pluck('name', 'id');

                return view('deals.clients', compact('deal', 'clients'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function clientUpdate($id, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                if(!empty($request->clients))
                {
                    $clients = array_filter($request->clients);
                    foreach($clients as $client)
                    {
                        ClientDeal::create(
                            [
                                'deal_id' => $deal->id,
                                'client_id' => $client,
                            ]
                        );
                    }
                }

                if(!empty($clients) && !empty($request->clients))
                {
                    return redirect()->back()->with('success', __('Clients successfully updated!'))->with('status', 'clients');
                }
                else
                {
                    return redirect()->back()->with('error', __('Please Select Valid Clients!'))->with('status', 'clients');
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function clientDestroy($id, $client_id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                ClientDeal::where('deal_id', '=', $deal->id)->where('client_id', '=', $client_id)->delete();

                return redirect()->back()->with('success', __('Client successfully deleted!'))->with('status', 'clients');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function productEdit($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $products = ProductService::where('created_by', '=', \Auth::user()->ownerId())->whereNOTIn('id', explode(',', $deal->products))->get()->pluck('name', 'id');

                return view('deals.products', compact('deal', 'products'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function productUpdate($id, Request $request)
    {
        $usr = \Auth::user();
        if($usr->can('edit deal'))
        {
            $deal       = Deal::find($id);
            $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $deal_users = $deal->users->pluck('id')->toArray();

            if($deal->created_by == $usr->ownerId())
            {
                if(!empty($request->products))
                {
                    $products       = array_filter($request->products);
                    $old_products   = explode(',', $deal->products);
                    $deal->products = implode(',', array_merge($old_products, $products));
                    $deal->save();

                    $objProduct = ProductService::whereIN('id', $products)->get()->pluck('name', 'id')->toArray();
                    ActivityLog::create(
                        [
                            'user_id' => $usr->id,
                            'deal_id' => $deal->id,
                            'log_type' => 'Add Product',
                            'remark' => json_encode(['title' => implode(",", $objProduct)]),
                        ]
                    );

                    $productArr = [
                        'deal_id' => $deal->id,
                        'name' => $deal->name,
                        'updated_by' => $usr->id,
                    ];

                }

                if(!empty($products) && !empty($request->products))
                {
                    return redirect()->back()->with('success', __('Products successfully updated!'))->with('status', 'products');
                }
                else
                {
                    return redirect()->back()->with('error', __('Please Select Valid Product!'))->with('status', 'general');
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
        }
    }

    public function productDestroy($id, $product_id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $products = explode(',', $deal->products);
                foreach($products as $key => $product)
                {
                    if($product_id == $product)
                    {
                        unset($products[$key]);
                    }
                }
                $deal->products = implode(',', $products);
                $deal->save();

                return redirect()->back()->with('success', __('Products successfully deleted!'))->with('status', 'products');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'products');
        }
    }

    public function fileUpload($id, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $request->validate(['file' => 'required']);
                $file_name = $request->file->getClientOriginalName();
                $file_path = $request->deal_id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();
                $request->file->storeAs('deal_files', $file_path);

                $file                 = DealFile::create(
                    [
                        'deal_id' => $request->deal_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                    ]
                );
                $return               = [];
                $return['is_success'] = true;
                $return['download']   = route(
                    'deals.file.download', [
                                             $deal->id,
                                             $file->id,
                                         ]
                );
                $return['delete']     = route(
                    'deals.file.delete', [
                                           $deal->id,
                                           $file->id,
                                       ]
                );

                ActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Upload File',
                        'remark' => json_encode(['file_name' => $file_name]),
                    ]
                );

                return response()->json($return);
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function fileDownload($id, $file_id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $file = DealFile::find($file_id);
                if($file)
                {
                    $file_path = storage_path('deal_files/' . $file->file_path);
                    $filename  = $file->file_name;

                    return \Response::download(
                        $file_path, $filename, [
                                      'Content-Length: ' . filesize($file_path),
                                  ]
                    );
                }
                else
                {
                    return redirect()->back()->with('error', __('File is not exist.'));
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function fileDelete($id, $file_id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $file = DealFile::find($file_id);
                if($file)
                {
                    $path = storage_path('deal_files/' . $file->file_path);
                    if(file_exists($path))
                    {
                        \File::delete($path);
                    }
                    $file->delete();

                    return response()->json(['is_success' => true], 200);
                }
                else
                {
                    return response()->json(
                        [
                            'is_success' => false,
                            'error' => __('File is not exist.'),
                        ], 200
                    );
                }
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function noteStore($id, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $deal->notes = $request->notes;
                $deal->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'success' => __('Note successfully saved!'),
                    ], 200
                );
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function taskCreate($id)
    {
        if(\Auth::user()->can('create task'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $priorities = DealTask::$priorities;
                $status     = DealTask::$status;
                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates
                $recurrances = DealVisit::$recurrances;

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); // Assuming 'name' is the attribute for user names
                // dd($assignedUsers);

                return view('deals.tasks', compact('deal', 'priorities', 'status', 'assignedUsers','recurrances'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function taskStore($id, Request $request)
    {
        $usr = \Auth::user();
        if($usr->can('create task'))
        {
            $deal       = Deal::find($id);
            $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $deal_users = $deal->users->pluck('id')->toArray();
            $usrs       = User::whereIN('id', array_merge($deal_users, $clients))->get()->pluck('email', 'id')->toArray();

            // dd($clients,$deal_users,$usrs);
            if($deal->created_by == $usr->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'date' => 'required',
                                       'time' => 'required',
                                       'priority' => 'required',
                                       'status' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $assignedUsers = $request->input('assigned_users') ? json_encode($request->input('assigned_users')) : null;

                $dealTask = DealTask::create(
                    [
                        'deal_id' => $deal->id,
                        'name' => $request->name,
                        'date' => $request->date,
                        'time' => date('H:i:s', strtotime($request->date . ' ' . $request->time)),
                        'priority' => $request->priority,
                        'status' => $request->status,
                        'recurrence_status' => $request->recurrence_status,
                        'recurrence' => $request->recurrence,
                        'repeat_interval' => $request->repeat_interval,
                        'end_recurrence' => $request->end_recurrence,
                        'reminder' => $request->reminder,
                        'created_by' =>\Auth::user()->name,
                        'assigned_users' => $assignedUsers,

                    ]
                );

                ActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Create Task',
                        'remark' => json_encode(['title' => $dealTask->name]),
                    ]
                );

                $taskArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                ];


                $taskArrDeal = [
                            'task_creator' => $dealTask->created_by,
                            'task_name' => $dealTask->name,
                            'task_priority' => $dealTask->priority,
                            'task_status' => $dealTask->status,
                            'from' => 'Deal',
                            'from_name' => $deal->name,
                            'pipeline' => $deal->pipeline->name,
                            'stage' => $deal->stage->name,
                            'link' => route('deals.show', $deal->id),

                ];

                // Send Email
                Utility::sendEmailTemplate('new_task', $usrs, $taskArrDeal);

                // dd($taskArrDeal);
                return redirect()->back()->with('success', __('Task successfully created!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function taskShow($id, $task_id)
    {
        if(\Auth::user()->can('view task'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $task = DealTask::find($task_id);

                return view('deals.tasksShow', compact('task', 'deal'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function taskEdit($id, $task_id)
    {
        if(\Auth::user()->can('edit task'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $priorities = DealTask::$priorities;
                $status     = DealTask::$status;
                $task       = DealTask::find($task_id);
                $user = \Auth::user();
                $userRole = $user->type;
                $hierarchy = Hierarchy::whereNull('child')->first();

                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates
                $recurrances = DealVisit::$recurrances;

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); // Assuming 'name' is the attribute for user names
                // dd($assignedUsers);
                return view('deals.tasks', compact('task','deal', 'priorities', 'status', 'assignedUsers','recurrances'));

            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function taskUpdate($id, $task_id, Request $request)
    {
        if(\Auth::user()->can('edit task'))
        {
            $deal = Deal::find($id);
            if($deal)
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'date' => 'required',
                                       'time' => 'required',
                                       'priority' => 'required',
                                       'status' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $task = DealTask::find($task_id);
                $assignedUsers = $request->input('assigned_users') ? json_encode($request->input('assigned_users')) : null;

                $task->update(
                    [
                        'name' => $request->name,
                        'date' => $request->date,
                        'time' => date('H:i:s', strtotime($request->date . ' ' . $request->time)),
                        'priority' => $request->priority,
                        'status' => $request->status,
                        'recurrence_status' => $request->recurrence_status,
                        'recurrence' => $request->recurrence,
                        'repeat_interval' => $request->repeat_interval,
                        'end_recurrence' => $request->end_recurrence,
                        'reminder' => $request->reminder,
                        'created_by' =>\Auth::user()->name,
                        'assigned_users' => $assignedUsers,

                    ]
                );

                return redirect()->back()->with('success', __('Task successfully updated!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function taskUpdateStatus($id, $task_id, Request $request)
    {
        if(\Auth::user()->can('edit task'))
        {
            $deal = Deal::find($id);
            if($deal)
            {

                $validator = \Validator::make(
                    $request->all(), [
                                       'status' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return response()->json(
                        [
                            'is_success' => false,
                            'error' => $messages->first(),
                        ], 401
                    );
                }

                $task = DealTask::find($task_id);
                if($request->status)
                {
                    $task->status = 0;
                }
                else
                {
                    $task->status = 1;
                }
                $task->save();

                return response()->json(
                    [
                        'is_success' => true,
                        'success' => __('Task successfully updated!'),
                        'status' => $task->status,
                        'status_label' => __(DealTask::$status[$task->status]),
                    ], 200
                );
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function taskDestroy($id, $task_id)
    {
        if(\Auth::user()->can('delete task'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $task = DealTask::find($task_id);
                $task->delete();

                return redirect()->back()->with('success', __('Task successfully deleted!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function sourceEdit($id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $sources  = Source::where('created_by', '=', \Auth::user()->ownerId())->get();
                $selected = $deal->sources();

                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }

                return view('deals.sources', compact('deal', 'sources', 'selected'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function sourceUpdate($id, Request $request)
    {
        $usr = \Auth::user();

        if($usr->can('edit deal'))
        {
            $deal       = Deal::find($id);
            $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $deal_users = $deal->users->pluck('id')->toArray();

            if($deal->created_by == $usr->ownerId())
            {
                if(!empty($request->sources) && count($request->sources) > 0)
                {
                    $deal->sources = implode(',', $request->sources);
                }
                else
                {
                    $deal->sources = "";
                }

                $deal->save();
                ActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Update Sources',
                        'remark' => json_encode(['title' => 'Update Sources']),
                    ]
                );

                $dealArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                ];

                return redirect()->back()->with('success', __('Sources successfully updated!'))->with('status', 'sources');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
        }
    }

    public function sourceDestroy($id, $source_id)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $sources = explode(',', $deal->sources);
                foreach($sources as $key => $source)
                {
                    if($source_id == $source)
                    {
                        unset($sources[$key]);
                    }
                }
                $deal->sources = implode(',', $sources);
                $deal->save();

                return redirect()->back()->with('success', __('Sources successfully deleted!'))->with('status', 'sources');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'sources');
        }
    }

    public function permission($id, $clientId)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal     = Deal::find($id);
            $client   = User::find($clientId);
            $selected = $client->clientPermission($deal->id);
            if($selected)
            {
                $selected = explode(',', $selected->permissions);
            }
            else
            {
                $selected = [];
            }
            $permissions = Deal::$permissions;

            return view('deals.permissions', compact('deal', 'client', 'selected', 'permissions'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function permissionStore($id, $clientId, Request $request)
    {
        if(\Auth::user()->can('edit deal'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $client      = User::find($clientId);
                $permissions = $client->clientPermission($deal->id);
                if($permissions)
                {
                    if(!empty($request->permissions) && count($request->permissions) > 0)
                    {
                        $permissions->permissions = implode(',', $request->permissions);
                    }
                    else
                    {
                        $permissions->permissions = "";
                    }
                    $permissions->save();

                    return redirect()->back()->with('success', __('Permissions successfully updated!'))->with('status', 'clients');
                }
                elseif(!empty($request->permissions) && count($request->permissions) > 0)
                {
                    ClientPermission::create(
                        [
                            'client_id' => $clientId,
                            'deal_id' => $deal->id,
                            'permissions' => implode(',', $request->permissions),
                        ]
                    );

                    return redirect()->back()->with('success', __('Permissions successfully updated!'))->with('status', 'clients');
                }
                else
                {
                    return redirect()->back()->with('error', __('Invalid Permission.'))->with('status', 'clients');
                }
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'clients');
        }
    }

    public function jsonUser(Request $request)
    {
        $users = [];
        if(!empty($request->deal_id))
        {
            $deal  = Deal::find($request->deal_id);
            $users = $deal->users->pluck('name', 'id');
        }

        return response()->json($users, 200);
    }

    public function changePipeline(Request $request)
    {
        $user                   = \Auth::user();
        $user->default_pipeline = $request->default_pipeline_id;
        $user->save();

        return redirect()->back();
    }

    public function discussionCreate($id)
    {
        $deal = Deal::find($id);
        if($deal)
        {
            return view('deals.discussions', compact('deal'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function discussionStore($id, Request $request)
    {
        $usr        = \Auth::user();
        $deal       = Deal::find($id);
        $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
        $deal_users = $deal->users->pluck('id')->toArray();

        if($deal)
        {
            $discussion             = new DealDiscussion();
            $discussion->comment    = $request->comment;
            $discussion->deal_id    = $deal->id;
            $discussion->created_by = \Auth::user()->id;
            $discussion->save();

            $dealArr = [
                'deal_id' => $deal->id,
                'name' => $deal->name,
                'updated_by' => $usr->id,
            ];

            return redirect()->back()->with('success', __('Message successfully added!'))->with('status', 'discussion');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'discussion');
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $deal         = Deal::where('id', '=', $id)->first();
        $deal->status = $request->deal_status;
        $deal->save();

        return redirect()->back();
    }

    // Deal Calls
    public function callCreate($id)
    {
        if(\Auth::user()->can('create deal call'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $users = UserDeal::where('deal_id', '=', $deal->id)->get();

                return view('deals.calls', compact('deal', 'users'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function callStore($id, Request $request)
    {
        $usr = \Auth::user();

        if($usr->can('create deal call'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == $usr->ownerId())
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'subject' => 'required',
                                       'call_type' => 'required',
                                       'user_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                DealCall::create(
                    [
                        'deal_id' => $deal->id,
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                ActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Create Deal Call',
                        'remark' => json_encode(['title' => 'Create new Deal Call']),
                    ]
                );

                $dealArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                ];

                return redirect()->back()->with('success', __('Call successfully created!'))->with('status', 'calls');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
        }
    }

    public function callEdit($id, $call_id)
    {
        if(\Auth::user()->can('edit deal call'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $call  = DealCall::find($call_id);
                $users = UserDeal::where('deal_id', '=', $deal->id)->get();

                return view('deals.calls', compact('call', 'deal', 'users'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function callUpdate($id, $call_id, Request $request)
    {
        if(\Auth::user()->can('edit deal call'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'subject' => 'required',
                                       'call_type' => 'required',
                                       'user_id' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $call = DealCall::find($call_id);

                $call->update(
                    [
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                return redirect()->back()->with('success', __('Call successfully updated!'))->with('status', 'calls');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function callDestroy($id, $call_id)
    {
        if(\Auth::user()->can('delete deal call'))
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $task = DealCall::find($call_id);
                $task->delete();

                return redirect()->back()->with('success', __('Call successfully deleted!'))->with('status', 'calls');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'calls');
        }
    }

    // Deal email
    public function emailCreate($id)
    {
        if(\Auth::user()->can('create deal email'))
        {
            $deal = Deal::find($id);
            $emailTemplates = DealEmailTemplate::where('page_name', 'deal')->get();
            if($deal)
            {
                return view('deals.emails', compact('deal','emailTemplates'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function emailStore($id, Request $request)
    {
        // dd('hi');
        if(\Auth::user()->can('create deal email'))
        {
            $deal = Deal::find($id);

            if($deal)
            {
                $settings  = Utility::settings();
                $validator = \Validator::make(
                    $request->all(), [
                        'to' => 'required|email',
                        'subject' => 'required',
                        'description' => 'required',
                    ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                DealEmail::create(
                    [
                        'deal_id' => $deal->id,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ]
                );
                if ($request->has('save_as_template')) {
                    $emailData = [
                    'to' => $request->to,
                    'subject' => $request->subject,
                    'description' => $request->description,
                    'page_name' => 'deal',
                    ];

                    DealEmailTemplate::create($emailData);
            }
                $dealEmail =
                    [
                        'deal_name' => $deal->name,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ];

                    if ($request->has('scheduled_time') && $request->scheduled_time) {
                        $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->scheduled_time, config('app.timezone'));
                        $delay = $scheduledTime->diffInMinutes(now())-180;
                        // echo now();

                        // dd($delay);
                        if ($delay > 0) {
                            try {
                                Mail::to($request->to)->later(now()->addSeconds($delay), new SendDealEmail($dealEmail, $settings));

                                return redirect()->back()->with('success', __('Email has been scheduled!'))->with('status', 'emails');
                            } catch (\Exception $e) {
                                $smtp_error = __('E-Mail has not been sent due to SMTP configuration');
                                \Log::error('SMTP Error: ' . $e->getMessage());
                                return redirect()->back()->with('error', $smtp_error)->with('status', 'smtp_error');
                                }
                        } else {
                            return redirect()->back()->with('error', __('Scheduled time must be in the future.'))->with('status', 'emails');
                        }
                    }
                    else{



                try
                {
                    Mail::to($request->to)->send(new SendDealEmail($dealEmail, $settings));
                }
                catch(\Exception $e)
                {
                    $smtp_error = __('E-Mail has been not sent due to SMTP configuration');
                }

            }


                ActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'deal_id' => $deal->id,
                        'log_type' => 'Create Deal Email',
                        'remark' => json_encode(['title' => 'Create new Deal Email']),
                    ]
                );

                return redirect()->back()->with('success', __('Email successfully created!') . ((isset($smtp_error)) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''))->with('status', 'emails');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
        }
    }

    public function export()
    {
        $name = 'Deal_' . date('Y-m-d i:h:s');
        $data = Excel::download(new DealExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function importFile()
    {
        return view('deals.import');
    }

    public function import(Request $request)
    {

        $rules = [
            'file' => 'required|mimes:csv,txt',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $deals = (new DealImport())->toArray(request()->file('file'))[0];

        $totalDeal = count($deals) - 1;
        $errorArray    = [];
        for($i = 1; $i <= count($deals) - 1; $i++)
        {

            $deal = $deals[$i];

            $dealData = new Deal();

            $user = User::where('name', $deal[5])->where('type','client')->where('created_by', \Auth::user()->creatorId())->first();
            $pipeline = PipeLine::where('name', $deal[3])->where('created_by', \Auth::user()->creatorId())->first();
            $stage = Stage::where('name', $deal[4])->where('created_by', \Auth::user()->creatorId())->first();

            $dealData->name      = $deal[0];
            $dealData->phone             = $deal[1];
            $dealData->price            = $deal[2];
            // $dealData->user_id     = !empty($user) ? $user->id : 3;
            $dealData->pipeline_id  = !empty($pipeline) ? $pipeline->id : 1;
            $dealData->stage_id    = !empty($stage) ? $stage->id: 1;
            $dealData->created_by       = \Auth::user()->creatorId();
            $dealData->status       = 'Active';

            if(empty($dealData))
            {
                $errorArray[] = $dealData;
            }
            else
            {
                $dealData->save();

                $clientData = new ClientDeal();
                $clientData->client_id = $user->id;
                $clientData->deal_id = $dealData->id;
                $clientData->save();

                $userData = new UserDeal();
                $userData->user_id = \Auth::user()->creatorId();
                $userData->deal_id = $dealData->id;
                $userData->save();

            }
        }

        $errorRecord = [];
        if(empty($errorArray))
        {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }
        else
        {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalDeal . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function dealFilter(Request $request)
    {
        $usr      = \Auth::user();

        if($usr->can('manage deal'))
        {
            if($usr->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->where('id', '=', $usr->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->ownerId())->first();
            }

            // $pipelines = Pipeline::where('created_by', '=', $usr->ownerId())->get()->pluck('name', 'id');

            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines->pluck('name', 'id');

            if($usr->type == 'client')
            {
                $id_deals = $usr->clientDeals->pluck('id');
            }
            else
            {
                $id_deals = $usr->deals->pluck('id');
            }

            $deals       = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->get();
            $curr_month  = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereMonth('created_at', '=', date('m'))->get();
            $curr_week   = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereBetween(
                'created_at', [
                                \Carbon\Carbon::now()->startOfWeek(),
                                \Carbon\Carbon::now()->endOfWeek(),
                            ]
            )->get();
            $last_30days = Deal::whereIn('id', $id_deals)->where('pipeline_id', '=', $pipeline->id)->whereDate('created_at', '>', \Carbon\Carbon::now()->subDays(30))->get();
            // Deal Summary
            $cnt_deal                = [];
            $cnt_deal['total']       = Deal::getDealSummary($deals);
            $cnt_deal['this_month']  = Deal::getDealSummary($curr_month);
            $cnt_deal['this_week']   = Deal::getDealSummary($curr_week);
            $cnt_deal['last_30days'] = Deal::getDealSummary($last_30days);

                        // dd($request->all(),$pipeline);

                        if ($request->has('saveFilter') && $request->input('saveFilter') == 'on') {

                            $request->validate([
                                'filterName' => 'required|string|max:255',
                            ]);

                            // Create a new filter with the filter criteria
                            AllFilter::create([
                                'saved_by' => auth()->id(),
                                'pipeline_id' => $request->input('pipeline_id'),
                                'page_name' => 'deal',
                                'title' => $request->input('filterName'),
                                'criteria' => json_encode($request->except(['_token', 'saveFilter', 'filterName','saved_filter','pipeline_id'])),

                            ]);
                        }
                            if ($savedFilter = request('saved_filter')) {
                                $savedFilterData = json_decode($savedFilter, true);

                                if (is_array($savedFilterData)) {
                                    $filters = $savedFilterData;
                                } else {

                                }
                            } else {
                                $filters = [
                                    'textSearch' => request('textSearch'),
                                    'stages' => request('stages'),
                                    'source' => request('source'),
                                    'users' => request('users'),
                                    'startDate' => request('startDate'),
                                    'endDate' => request('endDate'),
                                    'location' => request('location')
                                ];
                            }



                            $allfilter = AllFilter::where('saved_by', \Auth::user()->id)
                            ->where('page_name', 'deal')
                            ->where('pipeline_id', $pipeline->id)
                            ->get();

                            $allfilter = $allfilter->map(function ($filter) {
                            $filter->criteria = json_decode($filter->criteria, true);
                            return $filter;
                        });
            $stages = $pipeline->Stages->pluck('name', 'id');
            $sources = Source::all()->pluck('name', 'id');
            $users = User::all()->pluck('name', 'id');
            $clients      = User::where('created_by', '=', \Auth::user()->ownerId())->where('type', 'client')->get()->pluck('name', 'id');

            return view('deals.index', compact('pipelines', 'pipeline', 'cnt_deal','allfilter','stages','sources','users', 'clients'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function basicinfoEdit($id)
    {
        if(\Auth::user())
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
                $users->prepend(__('Select User'), '');
                return view('deals.basicinfo-edit', compact('deal','users'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }
    public function basicinfoUpdate($id, Request $request)
    {
        // Check if user is authenticated
        if (\Auth::user()) {
            $deal = Deal::find($id);

            if ($deal) {
                // Validate the incoming request data
                $request->validate([
                    'lead_owner' => 'required|exists:users,id',
                    'company_website' => 'nullable',
                    'company_entity_name' => 'required|string|max:255',
                    'company_entity_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'company_phone_ll1' => 'nullable|string',
                    'company_phone_ll2' => 'nullable|string',
                    'company_email' => 'nullable|email',
                    'address1' => 'nullable|string',
                    'address2' => 'nullable|string',
                    'city' => 'nullable|string',
                    'region' => 'nullable|string',
                    'country' => 'nullable|string',
                    'zip_code' => 'nullable|string',
                    'company_linkedin' => 'nullable|url',
                    'company_location' => 'nullable|string',
                ]);

                // Update lead fields
                $deal->deal_owner = $request->lead_owner;
                $deal->company_website = $request->company_website;
                $deal->company_entity_name = $request->company_entity_name;
                $deal->company_phone_ll1 = $request->company_phone_ll1;
                $deal->company_phone_ll2 = $request->company_phone_ll2;
                $deal->company_email = $request->company_email;
                $deal->address1 = $request->address1;
                $deal->address2 = $request->address2;
                $deal->city = $request->city;
                $deal->region = $request->region;
                $deal->country = $request->country;
                $deal->zip_code = $request->zip_code;
                $deal->company_linkedin = $request->company_linkedin;
                $deal->company_location = $request->company_location;

                // Handle file upload for company logo
                if ($request->hasFile('company_entity_logo')) {
                    $path = $request->file('company_entity_logo')->store('leads', 'public'); // Store in 'storage/app/public/logos'
                    $deal->company_entity_logo = $path; // Save the file path to the lead model
                }

                // Save changes to lead
                $deal->save();

                return response()->json([
                    'is_success' => true,
                    'message' => __(' basic information updated successfully.')
                ]);
            } else {
                // Lead not found
                return response()->json([
                    'is_success' => false,
                    'error' => __('Lead not found.'),
                ], 404);
            }
        } else {
            // Unauthorized access
            return response()->json([
                'is_success' => false,
                'error' => __('Permission Denied.'),
            ], 401);
        }
    }
    public function primarycontactEdit($id)
    {
        if(\Auth::user())
        {
            $deal = Deal::find($id);
            if($deal)
            {
                return view('deals.primary-contactinfo', compact('deal'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function primarycontactUpdate($id, Request $request)
    {
        // Check if the user is authenticated
        if (\Auth::user()) {
            $deal = Deal::find($id);

            if ($deal) {
                // Validate the incoming request data
                $request->validate([
                    'salutation' => 'required|string|in:Mr,Mrs,Ms',
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'mobile_primary' => 'nullable|string',
                    'mobile_secondary' => 'nullable|string',
                    'email_work' => 'nullable|email',
                    'email_personal' => 'nullable|email',
                    'phone_ll' => 'nullable|string',
                    'company_phone_ll' => 'nullable|string',
                    'extension' => 'nullable|integer',
                    'linkedin_profile' => 'nullable|url',
                ]);

                $deal->salutation = $request->salutation;
                $deal->first_name = $request->first_name;
                $deal->last_name = $request->last_name;
                $deal->mobile_primary = $request->mobile_primary;
                $deal->mobile_secondary = $request->mobile_secondary;
                $deal->email_work = $request->email_work;
                $deal->email_personal = $request->email_personal;
                $deal->phone_ll = $request->phone_ll;
                $deal->company_phone_ll = $request->company_phone_ll;
                $deal->extension = $request->extension;
                $deal->linkedin_profile = $request->linkedin_profile;

                $deal->save();

                return response()->json([
                    'is_success' => true,
                    'message' => __('Primary contact information updated successfully.')
                ]);
            } else {
                // Lead not found
                return response()->json([
                    'is_success' => false,
                    'error' => __('Lead not found.'),
                ], 404);
            }
        } else {
            // Unauthorized access
            return response()->json([
                'is_success' => false,
                'error' => __('Permission Denied.'),
            ], 401);
        }
    }
    public function contactCreate($id)
    {
        if(\Auth::user()->can('create deal email'))
        {
            $deal = Deal::find($id);

            if($deal->created_by == \Auth::user()->creatorId())
            {
                return view('deals.additional-contact', compact('deal'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function contactStore($id, Request $request)
    {
        $deal = Deal::findOrFail($id);

        $contactsArray = json_decode($deal->additional_contacts, true) ?? [];

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone_mobile' => 'nullable|string|max:255',
        ]);

        $newContact = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'title' => $validatedData['title'],
            'department' => $validatedData['department'],
            'phone_mobile' => $validatedData['phone_mobile'],
        ];
        $contactsArray[] = $newContact;

        $deal->additional_contacts = json_encode($contactsArray);
        $deal->save();


        return response()->json(['success' => __('Additional contact added successfully.')]);
    }
    public function contactDistroy($dealID, $index)
    {
        $deal = Deal::findOrFail($dealID);
        $additionalContacts = json_decode($deal->additional_contacts, true) ?? [];

        if (isset($additionalContacts[$index])) {
            // Remove the specified contact
            unset($additionalContacts[$index]);
            $additionalContacts = array_values($additionalContacts); // Reindex the array
            $deal->additional_contacts = json_encode($additionalContacts);
            $deal->save();
        }

        return redirect()->back()->with('success', 'Contact deleted successfully.');
    }
    public function meetingCreate($id)
    {
        if(\Auth::user()->can('create meeting'))
        {


                $members   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $settings = Utility::settings();

            return view('deals.meeting', compact('members','settings','id'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function meetingStore($id,Request $request)
    {

        $validator = \Validator::make(
            $request->all(), [

                               'members' => 'required',
                               'title' => 'required',
                               'date' => 'required',
                               'time' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        if(\Auth::user()->can('create meeting'))
        {
            $meeting                = new DealMeeting();
            $meeting->members   = json_encode($request->members);
            $meeting->title         = $request->title;
            $meeting->date          = $request->date;
            $meeting->time          = $request->time;
            $meeting->note          = $request->note;
            $meeting->deal_id       = $id;
            $meeting->created_by = \Auth::user()->id;

            $meeting->save();

            // if(in_array('0', $request->employee_id))
            // {
            //     $departmentEmployee = Employee::whereIn('department_id', $request->department_id)->get()->pluck('id');
            //     $departmentEmployee = $departmentEmployee;
            // }
            // else
            // {

            //     $departmentEmployee = $request->employee_id;
            // }
            // foreach($departmentEmployee as $employee)
            // {
            //     $meetingEmployee              = new MeetingEmployee();
            //     $meetingEmployee->meeting_id  = $meeting->id;
            //     $meetingEmployee->employee_id = $employee;
            //     $meetingEmployee->created_by  = \Auth::user()->creatorId();
            //     $meetingEmployee->save();
            // }


            //For Notification
            $setting  = Utility::settings(\Auth::user()->creatorId());
            $meetingNotificationArr = [
                'meeting_title' =>  $request->title,
                'branch_name' =>   '',
                'meeting_date' =>  $request->date,
                'meeting_time' =>  $request->time,
            ];
            //Slack Notification
            if(isset($setting['support_notification']) && $setting['support_notification'] ==1)
            {
                Utility::send_slack_msg('new_meeting', $meetingNotificationArr);
            }
            //Telegram Notification
            if(isset($setting['telegram_meeting_notification']) && $setting['telegram_meeting_notification'] ==1)
            {
                Utility::send_telegram_msg('new_meeting', $meetingNotificationArr);
            }

            //For Google Calendar
            if($request->get('synchronize_type')  == 'google_calender')
            {
                $type ='meeting';
                $request1=new LeadMeeting();
                $request1->title=$request->title;
                $request1->start_date=$request->date;
                $request1->end_date=$request->date;
                Utility::addCalendarData($request1 , $type);
            }

            //webhook
            // $module ='New Meeting';
            // $webhook =  Utility::webhookSetting($module);
            // if($webhook)
            // {
            //     $parameter = json_encode($meetingEmployee);
            //     $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
            //     if($status == true)
            //     {
            //         return redirect()->route('meeting.index')->with('success', __('Meeting  successfully created.'));
            //     }
            //     else
            //     {
            //         return redirect()->back()->with('error', __('Webhook call failed.'));
            //     }
            // }

            return redirect()->back()->with('success', __('Meeting  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function meetingEdit($id, $meeting_id)
    {
        if(\Auth::user()->can('edit meeting'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->id)
            {
                $members   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $settings = Utility::settings();
                $meeting= DealMeeting::find($meeting_id);
                $meeting->members = json_decode($meeting->members, true);

                return view('deals.meeting', compact('members','settings','deal','meeting'));

            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }

    public function meetingUpdate($id, $meeting_id, Request $request)
    {
        if(\Auth::user()->can('edit meeting'))
        {
            // Validate the request
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'date' => 'required',
                    'time' => 'required',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Retrieve the meeting instance
            $meeting = DealMeeting::find($meeting_id);
            if (!$meeting) {
                return redirect()->back()->with('error', __('Meeting not found.'));
            }

            // Check if the authenticated user is the creator of the meeting
            if($meeting->created_by == \Auth::user()->id)
            {
                $meeting->members   = json_encode($request->members);
                $meeting->title     = $request->title;
                $meeting->date      = $request->date;
                $meeting->time      = $request->time;
                $meeting->note      = $request->note;

                $meeting->save();
                session()->flash('success', 'Meeting successfully updated.');

                return redirect()->back()->with('success', __('Meeting successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function meetingDestroy($id, $meeting_id)
    {
        if(\Auth::user()->can('delete meeting'))
        {
            $deal = Deal::find($id);
            if($deal->created_by == \Auth::user()->id)
            {
                $meeting = DealMeeting::find($meeting_id);
                $meeting->delete();

                return redirect()->back()->with('success', __('Meeting successfully deleted!'))->with('status', 'meeting');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'meeting');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'meeting');
        }
    }

    public function visitCreate($id)
    {
        if(\Auth::user())
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $recurrances = DealVisit::$recurrances;
                $status     = DealVisit::$status;

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); // Assuming 'name' is the attribute for user names
                // dd($assignedUsers);
                return view('deals.visit', compact('deal', 'recurrances', 'status', 'assignedUsers'));

            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }
    public function visitStore($id, Request $request)
    {
        $usr = \Auth::user();
        if($usr)
        {
            // dd($request->all());
            $assignedUsersArray = $request->input('assigned_users');

            $deal       = Deal::find($id);
            // $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $deal_users = array_map('intval', $assignedUsersArray); // Convert to array of integers
            $usrs       = User::whereIN('id', array_merge($deal_users))->get()->pluck('email', 'id')->toArray();

            // dd($deal_users, );
            if($deal->created_by == $usr->ownerId())
            {
                $validator = \Validator::make(
                        $request->all(), [
                            'title' => 'required',
                            'date' => 'required',
                            'time' => 'required',
                            'status' => 'required',
                        ]
                    );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                // Process additional fields
                $assignedUsers = $request->input('assigned_users') ? json_encode($request->input('assigned_users')) : null;

                $filePaths = [];

                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {

                        $path = $file->store('leads/visit', 'public');


                        $filePaths[] = $path;
                    }
                }
                // Create the DealVisit entry
                $DealVisit = DealVisit::create([
                    'deal_id' => $deal->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'assigned_users' => $assignedUsers,
                    'date' => $request->date,
                    'time' => date('H:i:s', strtotime($request->date . ' ' . $request->time)),
                    'location' => $request->location,
                    'status' => $request->status,
                    'recurrence_status' => $request->recurrence_status,
                    'recurrence' => $request->recurrence,
                    'repeat_interval' => $request->repeat_interval,
                    'end_recurrence' => $request->end_recurrence,
                    'reminder' => $request->reminder,
                    'files' => json_encode($filePaths),
                    'created_by' =>\Auth::user()->name,
                ]);

                // ActivityLog::create(
                //     [
                //         'user_id' => $usr->id,
                //         'deal_id' => $DealVisit->id,
                //         'log_type' => 'Create Deal',
                //         'remark' => json_encode(['title' => $DealVisit->title]),
                //     ]
                // );

                $taskArr = [
                    'deal_id' => $deal->id,
                    'name' => $deal->name,
                    'updated_by' => $usr->id,
                ];

                $taskArr = [
                    'visit_creator' => $DealVisit->created_by,
                    'visit_name' => $DealVisit->title,
                    'from' => 'Deal',
                    'from_name' => $deal->name,
                    'pipeline' => $deal->pipeline->name,
                    'stage' => $deal->stage->name,

                ];

                Utility::sendEmailTemplate('new_visit', $usrs, $taskArr);


                // dd($tArr);
                // dd($request->all());

                // Send Email
                // Utility::sendEmailTemplate('Create Task', $usrs, $tArr);

                return redirect()->back()->with('success', __('Visit successfully created!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }
    public function visitEdit($id, $visit_id)
    {
        if(\Auth::user())
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $recurrances = DealVisit::$recurrances;
                $status     = DealVisit::$status;

                $hierarchy = Hierarchy::whereNull('child')->first();

                    $user = \Auth::user();
                    $userRole = $user->type;

                    $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                    $assignedUsers = $usersUnder->push($user)->unique('id');

                    $assignedUsers = $assignedUsers->pluck('name', 'id')->toArray(); // No need for json_decode



                $visit       = DealVisit::find($visit_id);
                // $fileIds = json_decode($visit->file_ids, true); // This will be an array of strings like ["30,31,32"]
                // $fileIdsArray = explode(',', $fileIds[0]); // Converts "30,31,32" into [30, 31, 32]
                // $visitFiles = DealFile::whereIn('id', $fileIdsArray)->get();
                return view('deals.visit', compact('deal', 'recurrances', 'status', 'assignedUsers', 'visit'));
            }
            else
            {
                return response()->json(
                    [
                        'is_success' => false,
                        'error' => __('Permission Denied.'),
                    ], 401
                );
            }
        }
        else
        {
            return response()->json(
                [
                    'is_success' => false,
                    'error' => __('Permission Denied.'),
                ], 401
            );
        }
    }
    public function visitUpdate($id, $visit_id, Request $request)
    {
        if(\Auth::user())
        {
            $deal = Deal::find($id);
            if($deal)
            {

                $validator = \Validator::make(
                    $request->all(), [
                        'title' => 'required',
                        'date' => 'required',
                        'time' => 'required',

                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                // dd($validator);

                $visit = DealVisit::find($visit_id);

                // dd($view);
                $visit->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'assigned_users' => json_encode($request->assigned_users),  // Ensure assigned users are saved as JSON
                    'date' => $request->date,
                    'time' => date('H:i:s', strtotime($request->date . ' ' . $request->time)),
                    'location' => $request->location,
                    'status' => $request->status,
                    'recurrance' => $request->recurrence,  // Ensure recurrence is saved correctly
                    'repeat_interval' => $request->repeat_interval, // Update repeat interval if provided
                    'end_recurrence' => $request->end_recurrence, // Update end recurrence date if provided
                    'reminder' => $request->reminder,  // Update reminder time
                    // 'file_ids' => $request->file_ids ? json_encode($request->file_ids) : null,  // Update file ids if files are uploaded
                ]);



                return redirect()->back()->with('success', __('Task successfully updated!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function visitDestroy($id, $visit)
    {
        if(\Auth::user())
        {
            $deal = Deal::find($id);
            if($deal)
            {
                $visit = DealVisit::find($visit);
                $visit->delete();

                return redirect()->back()->with('success', __('Task successfully deleted!'))->with('status', 'tasks');
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'tasks');
        }
    }

    public function json(Request $request)
    {
        $stages = new Stage();
        if($request->pipeline_id && !empty($request->pipeline_id))
        {


            $stages = $stages->where('pipeline_id', '=', $request->pipeline_id);
            $stages = $stages->get()->pluck('name', 'id');
        }
        else
        {
            $stages = [];
        }

        return response()->json($stages);
    }

}
