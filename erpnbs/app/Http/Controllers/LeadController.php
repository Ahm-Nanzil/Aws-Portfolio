<?php

namespace App\Http\Controllers;

use App\Mail\SendLeadEmail;
use App\Models\ClientDeal;
use App\Models\Deal;
use App\Models\DealCall;
use App\Models\DealDiscussion;
use App\Models\DealEmail;
use App\Models\DealFile;
use App\Models\Label;
use App\Models\Lead;
use App\Models\LeadActivityLog;
use App\Models\LeadCall;
use App\Models\LeadDiscussion;
use App\Models\LeadEmail;
use App\Models\LeadFile;
use App\Models\LeadStage;
use App\Models\Pipeline;
use App\Models\ProductService;
use App\Models\Source;
use App\Models\Stage;
use App\Models\User;
use App\Models\UserDeal;
use App\Models\UserLead;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeadExport;
use App\Imports\LeadImport;
use App\Models\DealEmailTemplate;
use App\Models\LeadTasks;
use App\Models\Hierarchy;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeadMeeting;
use App\Models\LeadView;
use App\Models\AllFilter;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\DealVisit;
use App\Models\DealTask;
use App\Models\Customer;
use App\Models\CustomerVisit;
use App\Models\CustomerTask;


class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private static $leadData = NULL;

    public function index()
    {
        if(\Auth::user()->can('manage lead'))
        {
            if(\Auth::user()->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->where('id', '=', \Auth::user()->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->first();
            }

            // $pipelines = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines->pluck('name', 'id');
            $users = User::all()->pluck('name', 'id');
            $stages = $pipeline->leadStages->pluck('name', 'id');
            $sources = Source::all()->pluck('name', 'id');
            $allfilter = AllFilter::where('saved_by', \Auth::user()->id)
                      ->where('page_name', 'lead')
                      ->where('pipeline_id', $pipeline->id)
                      ->get();


            // Decode the 'criteria' field for each filter to ensure it's in the proper format
            $allfilter = $allfilter->map(function ($filter) {
                // Decode the JSON string in the 'criteria' field
                $filter->criteria = json_decode($filter->criteria, true);
                return $filter;
            });

            return view('leads.index', compact('pipelines', 'pipeline', 'users', 'stages', 'sources', 'allfilter'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function lead_list()
    {
        $usr = \Auth::user();

        if($usr->can('manage lead'))
        {
            if($usr->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->creatorId())->where('id', '=', $usr->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', $usr->creatorId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', $usr->creatorId())->first();
            }

            $pipelines = Pipeline::where('created_by', '=', $usr->creatorId())->get()->pluck('name', 'id');
            $leads     = Lead::select('leads.*')->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', $usr->id)->where('leads.pipeline_id', '=', $pipeline->id)->orderBy('leads.order')->get();

            return view('leads.list', compact('pipelines', 'pipeline', 'leads'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(\Auth::user()->can('create lead'))
        {
            $lead_stages = LeadStage::select('lead_stages.*', 'pipelines.name as pipeline')->join('pipelines', 'pipelines.id', '=', 'lead_stages.pipeline_id')->where('pipelines.created_by', '=', \Auth::user()->ownerId())->where('lead_stages.created_by', '=', \Auth::user()->ownerId())->orderBy('lead_stages.pipeline_id')->orderBy('lead_stages.order')->get();
            // $pipelines   = [];

            // foreach($lead_stages as $lead_stage)
            // {
            //     if(!array_key_exists($lead_stage->pipeline_id, $pipelines))
            //     {
            //         $pipelines[$lead_stage->pipeline_id]                = [];
            //         $pipelines[$lead_stage->pipeline_id]['name']        = $lead_stage['pipeline'];
            //         $pipelines[$lead_stage->pipeline_id]['lead_stages'] = [];
            //     }
            //     $pipelines[$lead_stage->pipeline_id]['lead_stages'][] = $lead_stage;
            // }
            // dd($pipelines);
            $pipelines = Pipeline::all();
            $user = \Auth::user();
            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            // Filter pipelines based on user permissions
            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name;
                return in_array($permissionName, $userPermissions);
            });

            // Prepare pipelines for the dropdown
            $pipelines = $filteredPipelines->map(function ($pipeline) {
                return [
                    'id' => $pipeline->id,
                    'name' => $pipeline->name,
                ];
            });

            $stages = LeadStage::all();

            $sources = Source::where('created_by', '=', \Auth::user()->ownerId())->get()->pluck('name', 'id');

            $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
            $users->prepend(__('Select User'), '');

            return view('leads.create', compact('users','pipelines','sources','stages'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // dd($request->all());





        $usr = \Auth::user();
        if($usr->can('create lead'))
        {
            $validator = \Validator::make(
                $request->all(), [

                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }






            $pipeline = Pipeline::where('id', '=', $request->pipeline)->first();

            $stage = LeadStage::where('id', '=', $request->lead_stage)->first();
            // End Default Field Value

            if(empty($stage))
            {
                return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
            }
            else
            {
                $lead              = new Lead();
                $lead->name        = $request->company_entity_name;
                $lead->email       = $request->company_email;
                $lead->phone       = $request->company_phone_ll1;
                $lead->subject     = '';
                // $lead->user_id = $request->has('lead_owner') ? $request->lead_owner : $usr->id;
                $lead->user_id = $request->lead_owner ?? $usr->id;

                $lead->pipeline_id = $pipeline->id;
                $lead->stage_id    = $stage->id;
                $lead->sources          = $request->lead_source;
                $lead->created_by  = $usr->id;
                $lead->date        = date('Y-m-d');

                // Lead Basic Info
                $lead->lead_owner           = $request->lead_owner ?? $usr->id;
                $lead->company_website      = $request->company_website;
                $lead->company_entity_name  = $request->company_entity_name;

                // Save company entity logo file
                if ($request->hasFile('company_entity_logo')) {
                    $path = $request->file('company_entity_logo')->store('leads', 'public'); // Store in 'storage/app/public/logos'
                    $lead->company_entity_logo = $path; // Save the file path to the lead model
                }
                $lead->company_phone_ll1    = $request->company_phone_ll1;
                $lead->company_phone_ll2    = $request->company_phone_ll2;
                $lead->company_email        = $request->company_email;
                $lead->address1             = $request->address1;
                $lead->address2             = $request->address2;
                $lead->city                 = $request->city;
                $lead->region               = $request->region;
                $lead->country              = $request->country;
                $lead->zip_code             = $request->zip_code;
                $lead->company_linkedin     = $request->company_linkedin;
                $lead->company_location     = $request->company_location;

                // Primary Contact Info
                $lead->salutation           = $request->salutation;
                $lead->first_name           = $request->first_name;
                $lead->last_name            = $request->last_name;
                $lead->mobile_primary       = $request->mobile_primary;
                $lead->mobile_secondary     = $request->mobile_secondary;
                $lead->email_work           = $request->email_work;
                $lead->email_personal       = $request->email_personal;
                $lead->phone_ll             = $request->phone_ll;
                $lead->company_phone_ll     = $request->company_phone_ll;
                $lead->extension            = $request->extension;
                $lead->linkedin_profile     = $request->linkedin_profile;


                $lead->currency             = $request->currency;
                $lead->industry             = $request->industry;
                $lead->note                 = $request->note;
                $lead->additional_contacts = json_encode($request->additional_contacts);
                // dd($lead);

                $lead->save();
                // dd($lead);


                    if($request->lead_owner!=\Auth::user()->id){
                        $usrLeads = [
                            $usr->id,
                            $request->lead_owner ?? $usr->id,
                        ];
                    }else{
                        $usrLeads = [
                            $request->lead_owner ?? $usr->id,
                        ];
                    }

                foreach($usrLeads as $usrLead)
                {
                    UserLead::create(
                        [
                            'user_id' => $usrLead,
                            'lead_id' => $lead->id,
                        ]
                    );
                }

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                ];
                $lArr    = [
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                    'lead_pipeline' => $pipeline->name,
                    'lead_stage' => $stage->name,
                ];

                $usrEmail = User::find( $request->lead_owner ?? $usr->id );

                $lArr    = [
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                    'lead_pipeline' => $pipeline->name,
                    'lead_stage' => $stage->name,
                ];



                // Send Email
                $setings = Utility::settings();

                if($setings['lead_assigned'] == 1)
                {

                    $usrEmail = User::find($request->lead_owner ?? $usr->id);
                    $leadAssignArr = [
                        'lead_name' => $lead->name,
                        'lead_email' => $lead->email,
                        'lead_subject' => $lead->subject,
                        'lead_pipeline' => $pipeline->name,
                        'lead_stage' => $stage->name,

                    ];

                    $resp = Utility::sendEmailTemplate('lead_assigned', [$usrEmail->id => $usrEmail->email], $leadAssignArr);

                }


                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());
                $leadArr = [
                    'user_name' => \Auth::user()->name,
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                ];
                //Slack Notification
                if(isset($setting['lead_notification']) && $setting['lead_notification'] ==1)
                {
                    Utility::send_slack_msg('new_lead', $leadArr);
                }

                //Telegram Notification
                if(isset($setting['telegram_lead_notification']) && $setting['telegram_lead_notification'] ==1)
                {
                    Utility::send_telegram_msg('new_lead', $leadArr);
                }
                //webhook
                $module ='New Lead';
                $webhook=  Utility::webhookSetting($module);
                if($webhook)
                {
                    $parameter = json_encode($lead);
                    // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                    $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);

                    if($status == true)
                    {

                        return redirect()->back()->with('success', __('Lead successfully created!') .((!empty ($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('Webhook call failed.'));
                    }
                }

                return redirect()->back()->with('success', __('Lead successfully created!') .((!empty ($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        if($lead->is_active)
        {
            $calenderTasks = [];
            $deal          = Deal::where('id', '=', $lead->is_converted)->first();
            $stageCnt      = LeadStage::where('pipeline_id', '=', $lead->pipeline_id)->where('created_by', '=', $lead->created_by)->get();
            $i             = 0;
            foreach($stageCnt as $stage)
            {
                $i++;
                if($stage->id == $lead->stage_id)
                {
                    break;
                }
            }
            $precentage = number_format(($i * 100) / count($stageCnt));

                $previousLead = Lead::where('id', '<', $lead->id)
                ->where('is_active', 1)
                ->orderBy('id', 'desc')
                ->first();

                $nextLead = Lead::where('id', '>', $lead->id)
                    ->where('is_active', 1)
                    ->orderBy('id', 'asc')
                    ->first();
                    $views = leadView::where('lead_id', '=', $lead->id)->get();
                    // dd($files);
                    $meetings = LeadMeeting::where('lead_id', $lead->id)->get();

                    if (request()->ajax()) {
                        // Return the partial view if the request is AJAX
                        return view('leads.show-html', compact('lead', 'calenderTasks', 'deal', 'precentage', 'previousLead', 'nextLead', 'meetings', 'views'));
                    }
                    // dd($lead->tasks);

                    return view('leads.show', compact('lead', 'calenderTasks', 'deal', 'precentage', 'previousLead', 'nextLead', 'meetings', 'views'));
                }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        if(\Auth::user()->can('edit lead'))
        {
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $pipelines = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $pipelines->prepend(__('Select Pipeline'), '');
                $sources        = Source::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $products       = ProductService::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $users          = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
                $lead->sources  = explode(',', $lead->sources);
                $lead->products = explode(',', $lead->products);
                $users->prepend(__('Select User'), '');

                // dd($lead);

                return view('leads.edit', compact('lead', 'pipelines', 'sources', 'products', 'users'));
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lead $lead)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $usr= \Auth::user();
            if($lead)
            {
                $validator = \Validator::make(
                    $request->all(), [

                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $lead->name        = $request->company_entity_name;
                $lead->email       = $request->company_email;
                $lead->phone       = $request->company_phone_ll1;
                $lead->subject     = '';
                $lead->user_id     = $request->lead_owner ?? $usr->id;
                $lead->pipeline_id = $request->pipeline_id;
                $lead->stage_id    = $request->stage_id;
                $lead->sources          = $request->lead_source;
                $lead->created_by  = $usr->id;
                $lead->date        = date('Y-m-d');

                // Lead Basic Info
                $lead->lead_owner           = $request->lead_owner ?? $usr->id;
                $lead->company_website      = $request->company_website;
                $lead->company_entity_name  = $request->company_entity_name;

                // Save company entity logo file
                if ($request->hasFile('company_entity_logo')) {
                    $path = $request->file('company_entity_logo')->store('leads', 'public'); // Store in 'storage/app/public/logos'
                    $lead->company_entity_logo = $path; // Save the file path to the lead model
                }
                $lead->company_phone_ll1    = $request->company_phone_ll1;
                $lead->company_phone_ll2    = $request->company_phone_ll2;
                $lead->company_email        = $request->company_email;
                $lead->address1             = $request->address1;
                $lead->address2             = $request->address2;
                $lead->city                 = $request->city;
                $lead->region               = $request->region;
                $lead->country              = $request->country;
                $lead->zip_code             = $request->zip_code;
                $lead->company_linkedin     = $request->company_linkedin;
                $lead->company_location     = $request->company_location;

                // Primary Contact Info
                $lead->salutation           = $request->salutation;
                $lead->first_name           = $request->first_name;
                $lead->last_name            = $request->last_name;
                $lead->mobile_primary       = $request->mobile_primary;
                $lead->mobile_secondary     = $request->mobile_secondary;
                $lead->email_work           = $request->email_work;
                $lead->email_personal       = $request->email_personal;
                $lead->phone_ll             = $request->phone_ll;
                $lead->company_phone_ll     = $request->company_phone_ll;
                $lead->extension            = $request->extension;
                $lead->linkedin_profile     = $request->linkedin_profile;


                $lead->currency             = $request->currency;
                $lead->industry             = $request->industry;
                $lead->note                 = $request->note;
                // $lead->additional_contacts = json_encode($request->additional_contacts);

                $lead->save();
                // dd($lead);

                return redirect()->back()->with('success', __('Lead successfully updated!'));
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
     * Remove the specified resource from storage.
     *
     * @param \App\Lead $lead
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        if(\Auth::user()->can('delete lead'))
        {
            if($lead)
            {
                LeadDiscussion::where('lead_id', '=', $lead->id)->delete();
                LeadFile::where('lead_id', '=', $lead->id)->delete();
                UserLead::where('lead_id', '=', $lead->id)->delete();
                LeadActivityLog::where('lead_id', '=', $lead->id)->delete();
                $lead->delete();

                return redirect()->back()->with('success', __('Lead successfully deleted!'));
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

    public function json(Request $request)
    {
        $lead_stages = new LeadStage();
        if($request->pipeline_id && !empty($request->pipeline_id))
        {


            $lead_stages = $lead_stages->where('pipeline_id', '=', $request->pipeline_id);
            $lead_stages = $lead_stages->get()->pluck('name', 'id');
        }
        else
        {
            $lead_stages = [];
        }

        return response()->json($lead_stages);
    }

    public function fileUpload($id, Request $request)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $request->validate(['file' => 'required']);
                $file_name = $request->file->getClientOriginalName();
                $file_path = $request->lead_id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();
                $request->file->storeAs('lead_files', $file_path);
                $file                 = LeadFile::create(
                    [
                        'lead_id' => $request->lead_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                    ]
                );
                $return               = [];
                $return['is_success'] = true;
                $return['file_id'] = $file->id;

                $return['download']   = route(
                    'leads.file.download', [
                                             $lead->id,
                                             $file->id,
                                         ]
                );
                $return['delete']     = route(
                    'leads.file.delete', [
                                           $lead->id,
                                           $file->id,
                                       ]
                );
                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
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
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $file = LeadFile::find($file_id);
                if($file)
                {
                    $file_path = storage_path('lead_files/' . $file->file_path);
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
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $file = LeadFile::find($file_id);
                if($file)
                {
                    $path = storage_path('lead_files/' . $file->file_path);
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
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $lead->notes = $request->notes;
                $lead->save();

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

    public function labels($id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $labels   = Label::where('pipeline_id', '=', $lead->pipeline_id)->where('created_by', \Auth::user()->creatorId())->get();
                $selected = $lead->labels();
                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }
                else
                {
                    $selected = [];
                }

                return view('leads.labels', compact('lead', 'labels', 'selected'));
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
        if(\Auth::user()->can('edit lead'))
        {
            $leads = Lead::find($id);
            if($leads->created_by == \Auth::user()->creatorId())
            {
                if($request->labels)
                {
                    $leads->labels = implode(',', $request->labels);
                }
                else
                {
                    $leads->labels = $request->labels;
                }
                $leads->save();

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
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);

            if($lead->created_by == \Auth::user()->creatorId())
            {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->whereNOTIn(
                    'id', function ($q) use ($lead){
                    $q->select('user_id')->from('user_leads')->where('lead_id', '=', $lead->id);
                }
                )->get();


                $users = $users->pluck('name', 'id');

                return view('leads.users', compact('lead', 'users'));
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
        if(\Auth::user()->can('edit lead'))
        {
            $usr  = \Auth::user();
            $lead = Lead::find($id);

            if($lead->created_by == $usr->creatorId())
            {
                if(!empty($request->users))
                {
                    $users   = array_filter($request->users);
                    $leadArr = [
                        'lead_id' => $lead->id,
                        'name' => $lead->name,
                        'updated_by' => $usr->id,
                    ];

                    foreach($users as $user)
                    {
                        UserLead::create(
                            [
                                'lead_id' => $lead->id,
                                'user_id' => $user,
                            ]
                        );
                    }
                }

                if(!empty($users) && !empty($request->users))
                {
                    return redirect()->back()->with('success', __('Users successfully updated!'));
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
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                UserLead::where('lead_id', '=', $lead->id)->where('user_id', '=', $user_id)->delete();

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

    public function productEdit($id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->whereNOTIn('id', explode(',', $lead->products))->get()->pluck('name', 'id');

                return view('leads.products', compact('lead', 'products'));
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
        if(\Auth::user()->can('edit lead'))
        {
            $usr        = \Auth::user();
            $lead       = Lead::find($id);
            $lead_users = $lead->users->pluck('id')->toArray();

            if($lead->created_by == \Auth::user()->creatorId())
            {
                if(!empty($request->products))
                {
                    $products       = array_filter($request->products);
                    $old_products   = explode(',', $lead->products);
                    $lead->products = implode(',', array_merge($old_products, $products));
                    $lead->save();

                    $objProduct = ProductService::whereIN('id', $products)->get()->pluck('name', 'id')->toArray();

                    LeadActivityLog::create(
                        [
                            'user_id' => $usr->id,
                            'lead_id' => $lead->id,
                            'log_type' => 'Add Product',
                            'remark' => json_encode(['title' => implode(",", $objProduct)]),
                        ]
                    );

                    $productArr = [
                        'lead_id' => $lead->id,
                        'name' => $lead->name,
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
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $products = explode(',', $lead->products);
                foreach($products as $key => $product)
                {
                    if($product_id == $product)
                    {
                        unset($products[$key]);
                    }
                }
                $lead->products = implode(',', $products);
                $lead->save();

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

    public function sourceEdit($id)
    {
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $sources = Source::where('created_by', '=', \Auth::user()->creatorId())->get();

                $selected = $lead->sources();
                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }

                return view('leads.sources', compact('lead', 'sources', 'selected'));
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
        if(\Auth::user()->can('edit lead'))
        {
            $usr        = \Auth::user();
            $lead       = Lead::find($id);
            $lead_users = $lead->users->pluck('id')->toArray();

            if($lead->created_by == \Auth::user()->creatorId())
            {
                if(!empty($request->sources) && count($request->sources) > 0)
                {
                    $lead->sources = implode(',', $request->sources);
                }
                else
                {
                    $lead->sources = "";
                }

                $lead->save();

                LeadActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'Update Sources',
                        'remark' => json_encode(['title' => 'Update Sources']),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
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
        if(\Auth::user()->can('edit lead'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $sources = explode(',', $lead->sources);
                foreach($sources as $key => $source)
                {
                    if($source_id == $source)
                    {
                        unset($sources[$key]);
                    }
                }
                $lead->sources = implode(',', $sources);
                $lead->save();

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

    public function discussionCreate($id)
    {
        $lead = Lead::find($id);
        if($lead->created_by == \Auth::user()->creatorId())
        {
            return view('leads.discussions', compact('lead'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function discussionStore($id, Request $request)
    {
        $usr        = \Auth::user();
        $lead       = Lead::find($id);
        $lead_users = $lead->users->pluck('id')->toArray();

        if($lead->created_by == $usr->creatorId())
        {
            $discussion             = new LeadDiscussion();
            $discussion->comment    = $request->comment;
            $discussion->lead_id    = $lead->id;
            $discussion->created_by = $usr->id;
            $discussion->save();

            $leadArr = [
                'lead_id' => $lead->id,
                'name' => $lead->name,
                'updated_by' => $usr->id,
            ];

            return redirect()->back()->with('success', __('Message successfully added!'))->with('status', 'discussion');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'discussion');
        }
    }

    public function lead($id)
    {
        if(self::$leadData == null)
        {
            $lead = Lead::find($id);
            self::$leadData = $lead;
        }

        return self::$leadData;
    }

    public function order(Request $request)
    {
        if(\Auth::user()->can('move lead'))
        {
            $usr        = \Auth::user();
            $post       = $request->all();
            $lead       = $this->lead($post['lead_id']);
            $lead_users = $lead->users->pluck('email', 'id')->toArray();

            if($lead->stage_id != $post['stage_id'])
            {
                $newStage = LeadStage::find($post['stage_id']);

                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'Move',
                        'remark' => json_encode(
                            [
                                'title' => $lead->name,
                                'old_status' => $lead->stage->name,
                                'new_status' => $newStage->name,
                            ]
                        ),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                    'old_status' => $lead->stage->name,
                    'new_status' => $newStage->name,
                ];

                $lArr = [
                    'lead_name' => $lead->name,
                    'lead_email' => $lead->email,
                    'lead_pipeline' => $lead->pipeline->name,
                    'lead_stage' => $lead->stage->name,
                    'lead_old_stage' => $lead->stage->name,
                    'lead_new_stage' => $newStage->name,
                ];

                // Send Email
                Utility::sendEmailTemplate('Move Lead', $lead_users, $lArr);
            }

            foreach($post['order'] as $key => $item)
            {
                $lead           = $this->lead($item);
                $lead->order    = $key;
                $lead->stage_id = $post['stage_id'];
                $lead->save();
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }




    public function showConvertToDeal($id)
    {

        $lead         = Lead::findOrFail($id);
        $exist_client = User::where('type', '=', 'client')->where('email', '=', $lead->email)->where('created_by', '=', \Auth::user()->creatorId())->first();
        $clients      = User::where('type', '=', 'client')->where('created_by', '=', \Auth::user()->creatorId())->get();
        $user = \Auth::user();
        // Generate the deal name: user initials + month-year + deal number
        $initials = strtoupper(substr($user->name, 0, 2));
        $monthYear = date('my'); // Current month and year in format MMYY
        $dealCount = Deal::count() + 1; // Assuming Deal is your model for deals, get the next deal number

        $generatedDealName = $initials . '-' . $monthYear . '-' . str_pad($dealCount, 3, '0', STR_PAD_LEFT); // Auto-generated deal name

        return view('leads.convert', compact('lead', 'exist_client', 'clients','generatedDealName'));
    }

    public function convertToDeal($id, Request $request)
    {

        // dd($request->all());

        $lead = Lead::findOrFail($id);
        $usr  = \Auth::user();

        if($request->client_check == 'exist')
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'clients' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $client = User::where('type', '=', 'client')->where('email', '=', $request->clients)->where('created_by', '=', $usr->creatorId())->first();

            if(empty($client))
            {
                return redirect()->back()->with('error', 'Client is not available now.');
            }
        }
        else
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'client_name' => 'required',
                                   'client_email' => 'required|email|unique:users,email',
                                   'client_password' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $role   = Role::findByName('client');
            $client = User::create(
                [
                    'name' => $request->client_name,
                    'email' => $request->client_email,
                    'password' => \Hash::make($request->client_password),
                    'type' => 'client',
                    'lang' => 'en',
                    'created_by' => $usr->creatorId(),
                ]
            );
            $client->assignRole($role);

            $cArr = [
                'email' => $request->client_email,
                'password' => $request->client_password,
            ];

            // Send Email to client if they are new created.
            Utility::sendEmailTemplate('New User', [$client->id => $client->email], $cArr);
        }

        // Create Deal
        $stage = Stage::where('pipeline_id', '=', $lead->pipeline_id)->first();
        if(empty($stage))
        {
            return redirect()->back()->with('error', __('Please Create Stage for This Pipeline.'));
        }

        $deal              = new Deal();
        $deal->name = $request->generated_deal_name .(!empty($request->deal_name_additional) ? ' ' . $request->deal_name_additional : '');
        $deal->price       = empty($request->price) ? 0 : $request->price;
        $deal->pipeline_id = $lead->pipeline_id;
        $deal->stage_id    = $stage->id;
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
        if (!empty($request->is_transfer))
        {
            $deal->sources     = in_array('sources', $request->is_transfer) ? $lead->sources : '';
            $deal->products    = in_array('products', $request->is_transfer) ? $lead->products : '';
            $deal->notes       = in_array('notes', $request->is_transfer) ? $lead->notes : '';
        }
        else
        {
            $deal->sources     = '';
            $deal->products    = '';
            $deal->notes       = '';
        }

        $deal->labels      = $lead->labels;
        $deal->status      = 'Active';
        $deal->created_by  = $lead->created_by;
        $deal->save();
        // end create deal

        // Make entry in ClientDeal Table
        ClientDeal::create(
            [
                'deal_id' => $deal->id,
                'client_id' => $client->id,
            ]
        );
        // end

        $dealArr = [
            'deal_id' => $deal->id,
            'name' => $deal->name,
            'updated_by' => $usr->id,
        ];
        // Send Notification

        // Send Mail
        $pipeline = Pipeline::find($lead->pipeline_id);
        $dArr     = [
            'deal_name' => $deal->name,
            'deal_pipeline' => $pipeline->name,
            'deal_stage' => $stage->name,
            'deal_status' => $deal->status,
            'deal_price' => $usr->priceFormat($deal->price),
        ];
        Utility::sendEmailTemplate('Assign Deal', [$client->id => $client->email], $dArr);

        // Make Entry in UserDeal Table
        $leadUsers = UserLead::where('lead_id', '=', $lead->id)->get();
        foreach($leadUsers as $leadUser)
        {
            UserDeal::create(
                [
                    'user_id' => $leadUser->user_id,
                    'deal_id' => $deal->id,
                ]
            );
        }
        // end

        //Transfer Lead Discussion to Deal
        if (!empty($request->is_transfer))
        {
        if(in_array('discussion', $request->is_transfer))
        {
            $discussions = LeadDiscussion::where('lead_id', '=', $lead->id)->where('created_by', '=', $usr->creatorId())->get();
            if(!empty($discussions))
            {
                foreach($discussions as $discussion)
                {
                    DealDiscussion::create(
                        [
                            'deal_id' => $deal->id,
                            'comment' => $discussion->comment,
                            'created_by' => $discussion->created_by,
                        ]
                    );
                }
            }
        }
        // end Transfer Discussion

        // Transfer Lead Files to Deal
        if(in_array('files', $request->is_transfer))
        {
            $files = LeadFile::where('lead_id', '=', $lead->id)->get();
            if(!empty($files))
            {
                foreach($files as $file)
                {
                    $location     = base_path() . '/storage/lead_files/' . $file->file_path;
                    $new_location = base_path() . '/storage/deal_files/' . $file->file_path;
                    $copied       = copy($location, $new_location);

                    if($copied)
                    {
                        DealFile::create(
                            [
                                'deal_id' => $deal->id,
                                'file_name' => $file->file_name,
                                'file_path' => $file->file_path,
                            ]
                        );
                    }
                }
            }
        }
        // end Transfer Files

        // Transfer Lead Calls to Deal
        if(in_array('calls', $request->is_transfer))
        {
            $calls = LeadCall::where('lead_id', '=', $lead->id)->get();
            if(!empty($calls))
            {
                foreach($calls as $call)
                {
                    DealCall::create(
                        [
                            'deal_id' => $deal->id,
                            'subject' => $call->subject,
                            'call_type' => $call->call_type,
                            'duration' => $call->duration,
                            'user_id' => $call->user_id,
                            'description' => $call->description,
                            'call_result' => $call->call_result,
                        ]
                    );
                }
            }
        }
        //end

        // Transfer Lead Emails to Deal
        if(in_array('emails', $request->is_transfer))
        {
            $emails = LeadEmail::where('lead_id', '=', $lead->id)->get();
            if(!empty($emails))
            {
                foreach($emails as $email)
                {
                    DealEmail::create(
                        [
                            'deal_id' => $deal->id,
                            'to' => $email->to,
                            'subject' => $email->subject,
                            'description' => $email->description,
                        ]
                    );
                }
            }
        }
    }
        // Update is_converted field as deal_id
        $lead->is_converted = $deal->id;
        $lead->save();

        //For Notification
        $setting  = Utility::settings(\Auth::user()->creatorId());
        $leadUsers = Lead::where('id', '=', $lead->id)->first();
        $leadUserArr = [
            'lead_user_name' => $leadUsers->name,
            'lead_name' => $lead->name,
            'lead_email' => $lead->email,
        ];
        //Slack Notification
        if(isset($setting['leadtodeal_notification']) && $setting['leadtodeal_notification'] ==1)
        {
            Utility::send_slack_msg('lead_to_deal_conversion', $leadUserArr);
        }
        //Telegram Notification
        if(isset($setting['telegram_leadtodeal_notification']) && $setting['telegram_leadtodeal_notification'] ==1)
        {
            Utility::send_telegram_msg('lead_to_deal_conversion', $leadUserArr);
        }

        //webhook
        $module ='Lead to Deal Conversion';
        $webhook=  Utility::webhookSetting($module);
        if($webhook)
        {
            $parameter = json_encode($lead);
            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
            $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
            if($status == true)
            {
                return redirect()->back()->with('success', __('Lead successfully converted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Webhook call failed.'));
            }
        }


        return redirect()->back()->with('success', __('Lead successfully converted'));
    }

    // Lead Calls
    public function callCreate($id)
    {
        if(\Auth::user()->can('create lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $users = UserLead::where('lead_id', '=', $lead->id)->get();

                return view('leads.calls', compact('lead', 'users'));
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
        if(\Auth::user()->can('create lead call'))
        {
            $usr  = \Auth::user();
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
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

                $leadCall = LeadCall::create(
                    [
                        'lead_id' => $lead->id,
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'user_id' => $request->user_id,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                LeadActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'create lead call',
                        'remark' => json_encode(['title' => 'Create new Lead Call']),
                    ]
                );

                $leadArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
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
        if(\Auth::user()->can('edit lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $call  = LeadCall::find($call_id);
                $users = UserLead::where('lead_id', '=', $lead->id)->get();

                return view('leads.calls', compact('call', 'lead', 'users'));
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
        if(\Auth::user()->can('edit lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
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

                $call = LeadCall::find($call_id);

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
        if(\Auth::user()->can('delete lead call'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->creatorId())
            {
                $task = LeadCall::find($call_id);
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

    // Lead email
    public function emailCreate($id)
    {
        if(\Auth::user()->can('create lead email'))
        {
            $lead = Lead::find($id);
            $emailTemplates = DealEmailTemplate::where('page_name', 'lead')->get();

            if($lead->created_by == \Auth::user()->creatorId())
            {
                return view('leads.emails', compact('lead','emailTemplates'));
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
        if (\Auth::user()->can('create lead email')) {
            $lead = Lead::find($id);

            if ($lead->created_by == \Auth::user()->creatorId()) {
                $settings = Utility::settings();
                $validator = \Validator::make(
                    $request->all(), [
                        'to' => 'required|email',
                        'subject' => 'required',
                        'description' => 'required',
                        'scheduled_time' => 'nullable|date' // Validate if the scheduled time is a valid date
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                // Create the LeadEmail record
                $leadEmail = LeadEmail::create(
                    [
                        'lead_id' => $lead->id,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ]
                );

                $leadEmailData = [
                    'lead_name' => $lead->name,
                    'to' => $request->to,
                    'subject' => $request->subject,
                    'description' => $request->description,
                ];

                if ($request->has('save_as_template')) {
                    $emailData = [
                    'to' => $request->to,
                    'subject' => $request->subject,
                    'description' => $request->description,
                    'page_name' => 'lead',

                    ];

                    DealEmailTemplate::create($emailData);
            }

                    if ($request->has('scheduled_time') && $request->scheduled_time) {
                        $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->scheduled_time, config('app.timezone'));
                        $delay = $scheduledTime->diffInSeconds(now());

                        if ($delay > 0) {
                            try {
                                Mail::to($request->to)->later(now()->addSeconds($delay), new SendLeadEmail($leadEmailData, $settings));
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
                    else {
                                    try {
                                        Mail::to($request->to)->send(new SendLeadEmail($leadEmailData, $settings));
                                    } catch (\Exception $e) {
                                        $smtp_error = __('E-Mail has not been sent due to SMTP configuration');
                                    }
                                }

                LeadActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'lead_id' => $lead->id,
                        'log_type' => 'create lead email',
                        'remark' => json_encode(['title' => 'Create new Deal Email']),
                    ]
                );

                return redirect()->back()->with('success', __('Email successfully created!') . (isset($smtp_error) ? '<br> <span class="text-danger">' . $smtp_error . '</span>' : ''))->with('status', 'emails');
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'emails');
        }
    }

    public function export()
    {
        $name = 'Lead_' . date('Y-m-d i:h:s');
        $data = Excel::download(new LeadExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function importFile()
    {
        return view('leads.import');
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

        $leads = (new LeadImport())->toArray(request()->file('file'))[0];

        $totalLead = count($leads) - 1;
        $errorArray    = [];
        for($i = 1; $i <= count($leads) - 1; $i++)
        {
            $lead = $leads[$i];

            $leadByEmail = Lead::where('email', $lead[1])->first();
            if(!empty($leadByEmail))
            {
                $leadData = $leadByEmail;
            }
            else
            {
                $leadData = new Lead();
            }

            $user = User::where('name', $lead[4])->where('created_by',\Auth::user()->creatorId())->first();
            $pipeline = PipeLine::where('name', $lead[5])->where('created_by',\Auth::user()->creatorId())->first();
            $stage = LeadStage::where('name', $lead[6])->where('created_by',\Auth::user()->creatorId())->first();

            $leadData->name      = $lead[0];
            $leadData->email             = $lead[1];
            $leadData->phone            = $lead[2];
            $leadData->subject          = $lead[3];
            $leadData->user_id     = !empty($user) ? $user->id : 3;
            $leadData->pipeline_id  = !empty($pipeline) ? $pipeline->id : 1;
            $leadData->stage_id    = !empty($stage) ? $stage->id: 1;
            $leadData->created_by       = \Auth::user()->creatorId();

            if(empty($leadData))
            {
                $errorArray[] = $leadData;
            }
            else
            {
                $leadData->save();


                $userData = new UserLead();
                $userData->user_id = \Auth::user()->creatorId();
                $userData->lead_id = $leadData->id;
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
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalLead . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function contactCreate($id)
    {
        if(\Auth::user()->can('create lead email'))
        {
            $lead = Lead::find($id);

            if($lead->created_by == \Auth::user()->creatorId())
            {
                return view('leads.additional-contact', compact('lead'));
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
        // Find the lead by its ID
        $lead = Lead::findOrFail($id); // Will throw a 404 if not found

        // Decode existing additional contacts into an array
        $contactsArray = json_decode($lead->additional_contacts, true) ?? [];

        // Validate the new contact data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'phone_mobile' => 'nullable|string|max:255',
        ]);

        // Create a new contact array and add it to the existing contacts
        $newContact = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'title' => $validatedData['title'],
            'department' => $validatedData['department'],
            'phone_mobile' => $validatedData['phone_mobile'],
        ];
        $contactsArray[] = $newContact;

        // Update the lead's additional contacts
        $lead->additional_contacts = json_encode($contactsArray);
        $lead->save();

        // Render the updated contact list as a partial view and return it as JSON for AJAX
        // $updatedContactsView = view('leads.partials.additional_contacts_list', ['contacts' => $contactsArray])->render();

        return response()->json(['success' => __('Additional contact added successfully.')]);
    }
    public function contactDistroy($leadId, $index)
    {
        $lead = Lead::findOrFail($leadId);
        $additionalContacts = json_decode($lead->additional_contacts, true) ?? [];

        if (isset($additionalContacts[$index])) {
            // Remove the specified contact
            unset($additionalContacts[$index]);
            $additionalContacts = array_values($additionalContacts); // Reindex the array
            $lead->additional_contacts = json_encode($additionalContacts);
            $lead->save();
        }

        return redirect()->back()->with('success', 'Contact deleted successfully.');
    }

    public function taskCreate($id)
    {
        if(\Auth::user()->can('create task'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->ownerId())
            {
                $priorities = LeadTasks::$priorities;
                $status     = LeadTasks::$status;
                $recurrances = LeadView::$recurrances;

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); // Assuming 'name' is the attribute for user names
                // dd($assignedUsers);
                return view('leads.tasks', compact('lead', 'priorities', 'status', 'recurrances','assignedUsers'));

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
            // dd($request->all());
            $assignedUsersArray = $request->input('assigned_users');

            $lead       = lead::find($id);
            // $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $lead_users = array_map('intval', $assignedUsersArray); // Convert to array of integers
            $usrs       = User::whereIN('id', array_merge($lead_users))->get()->pluck('email', 'id')->toArray();

            // dd($lead_users, );
            if($lead)
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


                $leadTask = LeadTasks::create(
                    [
                        'lead_id' => $lead->id,
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

                // ActivityLog::create(
                //     [
                //         'user_id' => $usr->id,
                //         'deal_id' => $deal->id,
                //         'log_type' => 'Create Task',
                //         'remark' => json_encode(['title' => $LeadTasks->name]),
                //     ]
                // );

                $taskArr = [
                    'lead_id' => $lead->id,
                    'name' => $lead->name,
                    'updated_by' => $usr->id,
                ];



                $taskArr = [
                    'task_creator' => $usr->name,
                    'task_name' => $leadTask->name,
                    'task_priority' => $leadTask->priority,
                    'task_status' => $leadTask->status,
                    'from' => 'Lead',
                    'from_name' => $lead->name,
                    'pipeline' => $lead->pipeline->name,
                    'stage' => $lead->stage->name,
                    'link' => route('leads.show', $lead->id),


                ];

                // dd($tArr);

                // Send Email

                Utility::sendEmailTemplate('new_task', $usrs, $taskArr);

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
    public function taskEdit($id, $task_id)
    {
        if(\Auth::user()->can('edit task'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->ownerId())
            {
                $priorities = LeadTasks::$priorities;
                $status     = LeadTasks::$status;
                $task       = LeadTasks::find($task_id);
                $recurrances = LeadView::$recurrances;

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); //

                return view('leads.tasks', compact('task','lead', 'priorities', 'status', 'recurrances','assignedUsers'));

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
            $lead = Lead::find($id);
            if($lead)
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

                $task = LeadTasks::find($task_id);
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

    public function taskDestroy($id, $task_id)
    {
        if(\Auth::user()->can('delete task'))
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->ownerId())
            {
                $task = LeadTasks::find($task_id);
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
    public function meetingCreate($id)
    {
        if(\Auth::user()->can('create meeting'))
        {


                $members   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $settings = Utility::settings();

            return view('leads.meeting', compact('members','settings','id'));
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
            $meeting                = new LeadMeeting();
            $meeting->members   = json_encode($request->members);
            $meeting->title         = $request->title;
            $meeting->date          = $request->date;
            $meeting->time          = $request->time;
            $meeting->note          = $request->note;
            $meeting->lead_id       = $id;
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
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->id)
            {
                $members   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $settings = Utility::settings();
                $meeting= LeadMeeting::find($meeting_id);
                $meeting->members = json_decode($meeting->members, true);

                return view('leads.meeting', compact('members','settings','lead','meeting'));

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
            $meeting = LeadMeeting::find($meeting_id);
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
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->ownerId())
            {
                $meeting = LeadMeeting::find($meeting_id);
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

    public function basicinfoEdit($id)
    {
        if(\Auth::user())
        {
            $lead = Lead::find($id);
            if($lead)
            {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
                $users->prepend(__('Select User'), '');
                return view('leads.basicinfo-edit', compact('lead','users'));
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
            $lead = Lead::find($id);
            $usr= \Auth::user();

            if ($lead) {
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
                $lead->lead_owner = $request->lead_owner ?? $usr->id;
                $lead->company_website = $request->company_website;
                $lead->company_entity_name = $request->company_entity_name;
                $lead->company_phone_ll1 = $request->company_phone_ll1;
                $lead->company_phone_ll2 = $request->company_phone_ll2;
                $lead->company_email = $request->company_email;
                $lead->address1 = $request->address1;
                $lead->address2 = $request->address2;
                $lead->city = $request->city;
                $lead->region = $request->region;
                $lead->country = $request->country;
                $lead->zip_code = $request->zip_code;
                $lead->company_linkedin = $request->company_linkedin;
                $lead->company_location = $request->company_location;

                // Handle file upload for company logo
                if ($request->hasFile('company_entity_logo')) {
                    $path = $request->file('company_entity_logo')->store('leads', 'public'); // Store in 'storage/app/public/logos'
                    $lead->company_entity_logo = $path; // Save the file path to the lead model
                }

                // Save changes to lead
                $lead->save();

                return response()->json([
                    'is_success' => true,
                    'message' => __('Lead basic information updated successfully.')
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
            $lead = Lead::find($id);
            if($lead)
            {
                return view('leads.primary-contactinfo', compact('lead'));
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
            $lead = Lead::find($id);

            if ($lead) {
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

                // Update the lead's primary contact fields
                $lead->salutation = $request->salutation;
                $lead->first_name = $request->first_name;
                $lead->last_name = $request->last_name;
                $lead->mobile_primary = $request->mobile_primary;
                $lead->mobile_secondary = $request->mobile_secondary;
                $lead->email_work = $request->email_work;
                $lead->email_personal = $request->email_personal;
                $lead->phone_ll = $request->phone_ll;
                $lead->company_phone_ll = $request->company_phone_ll;
                $lead->extension = $request->extension;
                $lead->linkedin_profile = $request->linkedin_profile;

                // Save changes to lead
                $lead->save();

                // Return success response
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


    public function viewCreate($id)
    {
        if(\Auth::user())
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->ownerId())
            {
                $recurrances = LeadView::$recurrances;
                $status     = LeadView::$status;

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); // Assuming 'name' is the attribute for user names
                // dd($assignedUsers);
                return view('leads.view', compact('lead', 'recurrances', 'status', 'assignedUsers'));

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
    public function viewStore($id, Request $request)
    {
        $usr = \Auth::user();
        if($usr)
        {
            // dd($request->all());
            $assignedUsersArray = $request->input('assigned_users');

            $lead       = lead::find($id);
            // $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $lead_users = array_map('intval', $assignedUsersArray); // Convert to array of integers
            $usrs       = User::whereIN('id', array_merge($lead_users))->get()->pluck('email', 'id')->toArray();

            // dd($lead_users, );
            if($lead)
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

                $assignedUsers = $request->input('assigned_users') ? json_encode($request->input('assigned_users')) : null;


                $filePaths = [];

                if ($request->hasFile('files')) {
                    foreach ($request->file('files') as $file) {

                        $path = $file->store('leads/visit', 'public');


                        $filePaths[] = $path;
                    }
                }
                // dd($filePaths);

                    $leadView = LeadView::create([
                    'lead_id' => $lead->id,
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
                //         'deal_id' => $deal->id,
                //         'log_type' => 'Create Task',
                //         'remark' => json_encode(['title' => $leadViews->name]),
                //     ]
                // );

                // dd($leadView);



                $templateArr = [
                    'visit_creator' => \Auth::user()->name,
                    'visit_name' => $leadView->title,
                    'from' => 'Lead',
                    'from_name' => $lead->name,
                    'pipeline' => $lead->pipeline->name,
                    'stage' => $lead->stage->name,
                    'task_link' => route('leads.show', $lead->id),


                ];

                // dd($tArr);
                // dd($request->all());

                // Send Email
                Utility::sendEmailTemplate('new_visit',  $usrs, $templateArr);

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
    public function viewEdit($id, $view_id)
    {
        if(\Auth::user())
        {
            $lead = Lead::find($id);
            if($lead->created_by == \Auth::user()->ownerId())
            {
                $recurrances = LeadView::$recurrances;
                $status     = LeadView::$status;

                $hierarchy = Hierarchy::whereNull('child')->first();

                // Get the current authenticated user
                    $user = \Auth::user();
                    $userRole = $user->type;

                    // Get users under specific roles
                    $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                    // Merge the logged-in user with users under the roles and remove duplicates
                    $assignedUsers = $usersUnder->push($user)->unique('id');

                    // Convert assigned users to an associative array (id => name)
                    $assignedUsers = $assignedUsers->pluck('name', 'id')->toArray(); // No need for json_decode



                $view       = LeadView::find($view_id);
                // $fileIds = json_decode($view->files, true); // This will be an array of strings like ["30,31,32"]
                // $fileIdsArray = explode(',', $fileIds[0]); // Converts "30,31,32" into [30, 31, 32]
                // $visitFiles = LeadFile::whereIn('id', $fileIdsArray)->get();
        // dd($visitFiles);
                return view('leads.view', compact('lead', 'recurrances', 'status', 'assignedUsers', 'view'));
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
    public function viewUpdate($id, $view_id, Request $request)
    {
        if(\Auth::user())
        {
            $lead = Lead::find($id);
            if($lead)
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

                $view = LeadView::find($view_id);

                // dd($view);
                $view->update([
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

    public function viewDestroy($id, $view)
    {
        if(\Auth::user())
        {
            $lead = Lead::find($id);
            if($lead)
            {
                $view = LeadView::find($view);
                $view->delete();

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



public function leadFilter(Request $request)
{



        if(\Auth::user()->can('manage lead'))
        {
            if(\Auth::user()->default_pipeline)
            {
                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->where('id', '=', \Auth::user()->default_pipeline)->first();
                if(!$pipeline)
                {
                    $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->first();
                }
            }
            else
            {
                $pipeline = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->first();
            }

            $pipelines = Pipeline::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            $users = User::all()->pluck('name', 'id');
            $stages = $pipeline->leadStages->pluck('name', 'id');
            $sources = Source::all()->pluck('name', 'id');

            // dd($request->all(),$pipeline);

            if ($request->has('saveFilter') && $request->input('saveFilter') == 'on') {

                $request->validate([
                    'filterName' => 'required|string|max:255',
                ]);

                // Create a new filter with the filter criteria
                AllFilter::create([
                    'saved_by' => auth()->id(),
                    'pipeline_id' => $request->input('pipeline_id'),
                    'page_name' => 'lead',
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
                ->where('page_name', 'lead')
                ->where('pipeline_id', $pipeline->id)
                ->get();

                $allfilter = $allfilter->map(function ($filter) {
                $filter->criteria = json_decode($filter->criteria, true);
                return $filter;
            });
            return view('leads.index', compact('pipelines', 'pipeline', 'users', 'stages', 'sources', 'filters', 'allfilter'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function changePipeline(Request $request)
    {
        $user                   = \Auth::user();
        $user->default_pipeline = $request->default_pipeline_id;
        $user->save();

        return redirect()->route('leads.index');
    }


    public function recurrenceCheck()
    {
        $leadViews = LeadView::where('recurrence_status', 1)->get();
        $leadTasks = LeadTasks::where('recurrence_status', 1)->get();
        $dealTasks = DealTask::where('recurrence_status', 1)->get();
        $dealvisits = DealVisit::where('recurrence_status', 1)->get();
        $customerTasks = CustomerTask::where('recurrence_status', 1)->get();
        $customerVisits = CustomerVisit::where('recurrence_status', 1)->get();

        $currentTime = Carbon::now();
        $currentTimeOnly = $currentTime->format('H:i');
        // dd($currentTimeOnly);
        foreach ($leadViews as $visit) {
            if (Carbon::parse($visit->end_recurrence) > $currentTime) {
                $lead = Lead::find($visit->lead_id);
                $assignedUsers = json_decode($visit->assigned_users, true);
                $usrs = User::whereIn('id', array_merge($assignedUsers))->get()->pluck('email', 'id')->toArray();

                $recurrenceType = $visit->recurrence;
                $repeatInterval = $visit->repeat_interval;
                $creationDate = Carbon::parse($visit->created_at);
                $shouldClone = false;

                switch ($recurrenceType) {
                    case 1: // Daily
                        $shouldClone = $creationDate->diffInDays($currentTime) >= $repeatInterval;
                        break;
                    case 2: // Weekly
                        $shouldClone = $creationDate->diffInWeeks($currentTime) >= $repeatInterval;
                        break;
                    case 3: // Monthly
                        $shouldClone = $creationDate->diffInMonths($currentTime) >= $repeatInterval;
                        break;
                    case 4: // Yearly
                        $shouldClone = $creationDate->diffInYears($currentTime) >= $repeatInterval;
                        break;
                    default:
                        break;
                }

                if ($shouldClone) {


                    if (is_null($visit->reminder)) {
                        $this->cloneLeadView($visit);
                        $visit->recurrence_status = 0;
                        $visit->save();

                        $taskArr = [
                            'visit_creator' => $visit->created_by,
                            'visit_name' => $visit->title,
                            'from' => 'Lead',
                            'from_name' => $lead->name,
                            'pipeline' => $lead->pipeline->name,
                            'stage' => $lead->stage->name,
                            'link' => route('leads.show', $lead->id),

                        ];
                        Utility::sendEmailTemplate('new_visit', $usrs, $taskArr);
                    }
                }
            }
        }
        foreach ($leadTasks as $task) {
            if (Carbon::parse($task->end_recurrence) > $currentTime) {
                $lead = Lead::find($task->lead_id);
                $assignedUsers = json_decode($task->assigned_users, true);
                $usrs = User::whereIn('id', array_merge($assignedUsers))->get()->pluck('email', 'id')->toArray();

                $recurrenceType = $task->recurrence;
                $repeatInterval = $task->repeat_interval;
                $creationDate = Carbon::parse($task->created_at);
                $shouldClone = false;

                switch ($recurrenceType) {
                    case 1: // Daily
                        $shouldClone = $creationDate->diffInDays($currentTime) >= $repeatInterval;
                        break;
                    case 2: // Weekly
                        $shouldClone = $creationDate->diffInWeeks($currentTime) >= $repeatInterval;
                        break;
                    case 3: // Monthly
                        $shouldClone = $creationDate->diffInMonths($currentTime) >= $repeatInterval;
                        break;
                    case 4: // Yearly
                        $shouldClone = $creationDate->diffInYears($currentTime) >= $repeatInterval;
                        break;
                    default:
                        break;
                }

                if ($shouldClone) {
                    if (is_null($task->reminder)) {
                        $reminderTime = null;
                    } else {
                        $reminderTime = Carbon::createFromFormat('H:i:s', $task->reminder)->format('H:i');
                    }

                    if (is_null($task->reminder) || $reminderTime === $currentTimeOnly) {
                        // dd('hi');

                        $this->cloneLeadTask($task);
                        $task->recurrence_status = 0;
                        $task->save();

                        $taskArr = [
                            'task_creator' => $task->created_by,
                            'task_name' => $task->name,
                            'task_priority' => $task->priority,
                            'task_status' => $task->status,
                            'from' => 'Lead',
                            'from_name' => $lead->name,
                            'pipeline' => $lead->pipeline->name,
                            'stage' => $lead->stage->name,
                            'link' => route('leads.show', $lead->id),

                        ];
                        Utility::sendEmailTemplate('new_task', $usrs, $taskArr);
                    }
                }
            }
        }
        foreach ($dealTasks as $dealTask) {
            if (Carbon::parse($dealTask->end_recurrence) > $currentTime) {
                $deal = Deal::find($dealTask->deal_id);
                $assignedUsers = json_decode($dealTask->assigned_users, true);
                $usrs = User::whereIn('id', array_merge($assignedUsers))->get()->pluck('email', 'id')->toArray();

                $recurrenceType = $dealTask->recurrence;
                $repeatInterval = $dealTask->repeat_interval;
                $creationDate = Carbon::parse($dealTask->created_at);
                $shouldClone = false;

                switch ($recurrenceType) {
                    case 1: // Daily
                        $shouldClone = $creationDate->diffInDays($currentTime) >= $repeatInterval;
                        break;
                    case 2: // Weekly
                        $shouldClone = $creationDate->diffInWeeks($currentTime) >= $repeatInterval;
                        break;
                    case 3: // Monthly
                        $shouldClone = $creationDate->diffInMonths($currentTime) >= $repeatInterval;
                        break;
                    case 4: // Yearly
                        $shouldClone = $creationDate->diffInYears($currentTime) >= $repeatInterval;
                        break;
                    default:
                        break;
                }

                if ($shouldClone) {
                    if (is_null($dealTask->reminder)) {
                        $reminderTime = null;
                    } else {
                        $reminderTime = Carbon::createFromFormat('H:i:s', $dealTask->reminder)->format('H:i');
                    }

                    if (is_null($dealTask->reminder) || $reminderTime === $currentTimeOnly) {
                        // dd('hi');

                        $this->clonedealTask($dealTask);
                        $dealTask->recurrence_status = 0;
                        $dealTask->save();

                        $dealTaskArr = [
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
                        Utility::sendEmailTemplate('new_task', $usrs, $dealtaskArr);
                    }
                }
            }
        }
        foreach ($dealvisits as $visit) {
            if (Carbon::parse($visit->end_recurrence) > $currentTime) {
                $deal = Deal::find($visit->deal_id);
                $assignedUsers = json_decode($visit->assigned_users, true);
                $usrs = User::whereIn('id', array_merge($assignedUsers))->get()->pluck('email', 'id')->toArray();

                $recurrenceType = $visit->recurrence;
                $repeatInterval = $visit->repeat_interval;
                $creationDate = Carbon::parse($visit->created_at);
                $shouldClone = false;

                switch ($recurrenceType) {
                    case 1: // Daily
                        $shouldClone = $creationDate->diffInDays($currentTime) >= $repeatInterval;
                        break;
                    case 2: // Weekly
                        $shouldClone = $creationDate->diffInWeeks($currentTime) >= $repeatInterval;
                        break;
                    case 3: // Monthly
                        $shouldClone = $creationDate->diffInMonths($currentTime) >= $repeatInterval;
                        break;
                    case 4: // Yearly
                        $shouldClone = $creationDate->diffInYears($currentTime) >= $repeatInterval;
                        break;
                    default:
                        break;
                }

                if ($shouldClone) {


                    if (is_null($visit->reminder)) {
                        $this->cloneDealVisit($visit);
                        $visit->recurrence_status = 0;
                        $visit->save();

                        $taskArr = [
                            'visit_creator' => $visit->created_by,
                            'visit_name' => $visit->title,
                            'from' => 'Deal',
                            'from_name' => $deal->name,
                            'pipeline' => $deal->pipeline->name,
                            'stage' => $deal->stage->name,

                        ];
                        Utility::sendEmailTemplate('new_visit', $usrs, $taskArr);
                    }
                }
            }
        }
        foreach ($customerTasks as $task) {
            if (Carbon::parse($task->end_recurrence) > $currentTime) {
                $customer = Customer::find($task->customer_id);
                $assignedUsers = json_decode($task->assigned_users, true);
                $usrs = User::whereIn('id', array_merge($assignedUsers))->get()->pluck('email', 'id')->toArray();

                $recurrenceType = $task->recurrence;
                $repeatInterval = $task->repeat_interval;
                $creationDate = Carbon::parse($task->created_at);
                $shouldClone = false;

                switch ($recurrenceType) {
                    case 1: // Daily
                        $shouldClone = $creationDate->diffInDays($currentTime) >= $repeatInterval;
                        break;
                    case 2: // Weekly
                        $shouldClone = $creationDate->diffInWeeks($currentTime) >= $repeatInterval;
                        break;
                    case 3: // Monthly
                        $shouldClone = $creationDate->diffInMonths($currentTime) >= $repeatInterval;
                        break;
                    case 4: // Yearly
                        $shouldClone = $creationDate->diffInYears($currentTime) >= $repeatInterval;
                        break;
                    default:
                        break;
                }

                if ($shouldClone) {
                    if (is_null($task->reminder)) {
                        $reminderTime = null;
                    } else {
                        $reminderTime = Carbon::createFromFormat('H:i:s', $task->reminder)->format('H:i');
                    }

                    if (is_null($task->reminder) || $reminderTime === $currentTimeOnly) {
                        // dd('hi');

                        $this->clonecustomerTask($task);
                        $task->recurrence_status = 0;
                        $task->save();

                        $taskArr = [
                            'task_creator' => $task->created_by,
                            'task_name' => $task->name,
                            'task_priority' => $task->priority,
                            'task_status' => $task->status,
                            'from' => 'Customer',
                            'from_name' => $customer->name,
                            'pipeline' => $customer->pipeline->name,
                            'stage' => $customer->stage->name,
                            'link' => route('customer.show', $customer->id),

                        ];
                        Utility::sendEmailTemplate('new_task', $usrs, $taskArr);
                    }
                }
            }
        }
        foreach ($customerVisits as $visit) {
            if (Carbon::parse($visit->end_recurrence) > $currentTime) {
                $customer = Customer::find($visit->customer_id);
                $assignedUsers = json_decode($visit->assigned_users, true);
                $usrs = User::whereIn('id', array_merge($assignedUsers))->get()->pluck('email', 'id')->toArray();

                $recurrenceType = $visit->recurrence;
                $repeatInterval = $visit->repeat_interval;
                $creationDate = Carbon::parse($visit->created_at);
                $shouldClone = false;

                switch ($recurrenceType) {
                    case 1: // Daily
                        $shouldClone = $creationDate->diffInDays($currentTime) >= $repeatInterval;
                        break;
                    case 2: // Weekly
                        $shouldClone = $creationDate->diffInWeeks($currentTime) >= $repeatInterval;
                        break;
                    case 3: // Monthly
                        $shouldClone = $creationDate->diffInMonths($currentTime) >= $repeatInterval;
                        break;
                    case 4: // Yearly
                        $shouldClone = $creationDate->diffInYears($currentTime) >= $repeatInterval;
                        break;
                    default:
                        break;
                }

                if ($shouldClone) {


                    if (is_null($visit->reminder)) {
                        $this->cloneCustomerVisit($visit);
                        $visit->recurrence_status = 0;
                        $visit->save();

                        $taskArr = [
                            'visit_creator' => $visit->created_by,
                            'visit_name' => $visit->title,
                            'from' => 'Customer',
                            'from_name' => $customer->name,
                            'pipeline' => $customer->pipeline->name,
                            'stage' => $customer->stage->name,

                        ];
                        Utility::sendEmailTemplate('new_visit', $usrs, $taskArr);
                    }
                }
            }
        }
    }


    private function cloneLeadView($visit)
    {
        LeadView::create([
            'lead_id' => $visit->lead_id,
            'title' => $visit->title,
            'description' => $visit->description,
            'assigned_users' => $visit->assigned_users,
            'date' => $visit->date,
            'time' => $visit->time,
            'location' => $visit->location,
            'status' => $visit->status,
            'recurrence_status' => 1, // Set recurrence_status as active
            'recurrence' => $visit->recurrence, // Keep the same recurrence type
            'repeat_interval' => $visit->repeat_interval,
            'end_recurrence' => $visit->end_recurrence, // Optionally adjust this as needed
            'reminder' => $visit->reminder,
            'files' => $visit->files, // Include files if needed
            'created_by' => $visit->created_by, // Include files if needed

        ]);
    }
    private function cloneLeadTask($task)
    {
        LeadTasks::create([
            'lead_id' => $task->lead_id,
            'name' => $task->name,
            'date' => $task->date,
            'time' => $task->time,
            'priority' => $task->priority,
            'status' => $task->status,
            'recurrence' => $task->recurrence, // Keep the same recurrence type
            'repeat_interval' => $task->repeat_interval,
            'end_recurrence' => $task->end_recurrence, // Optionally adjust this as needed
            'reminder' => $task->reminder,
            'recurrence_status' => 1, // Set recurrence_status as active
            'assigned_users' => $task->assigned_users,
            'created_by' => $task->created_by, // Include files if needed
        ]);
    }
    private function clonedealTask($task)
    {
        DealTask::create([
            'deal_id' => $task->deal_id,
            'name' => $task->name,
            'date' => $task->date,
            'time' => $task->time,
            'priority' => $task->priority,
            'status' => $task->status,
            'recurrence' => $task->recurrence, // Keep the same recurrence type
            'repeat_interval' => $task->repeat_interval,
            'end_recurrence' => $task->end_recurrence, // Optionally adjust this as needed
            'reminder' => $task->reminder,
            'recurrence_status' => 1, // Set recurrence_status as active
            'assigned_users' => $task->assigned_users,
            'created_by' => $task->created_by, // Include files if needed
        ]);
    }
    private function cloneDealVisit($visit)
    {
        DealVisit::create([
            'deal_id' => $visit->deal_id,
            'title' => $visit->title,
            'description' => $visit->description,
            'assigned_users' => $visit->assigned_users,
            'date' => $visit->date,
            'time' => $visit->time,
            'location' => $visit->location,
            'status' => $visit->status,
            'recurrence_status' => 1, // Set recurrence_status as active
            'recurrence' => $visit->recurrence, // Keep the same recurrence type
            'repeat_interval' => $visit->repeat_interval,
            'end_recurrence' => $visit->end_recurrence, // Optionally adjust this as needed
            'reminder' => $visit->reminder,
            'files' => $visit->files, // Include files if needed
            'created_by' => $visit->created_by, // Include files if needed

        ]);
    }
    private function clonecustomerTask($task)
    {
        CustomerTask::create([
            'customer_id' => $task->customer_id,
            'name' => $task->name,
            'date' => $task->date,
            'time' => $task->time,
            'priority' => $task->priority,
            'status' => $task->status,
            'recurrence' => $task->recurrence, // Keep the same recurrence type
            'repeat_interval' => $task->repeat_interval,
            'end_recurrence' => $task->end_recurrence, // Optionally adjust this as needed
            'reminder' => $task->reminder,
            'recurrence_status' => 1, // Set recurrence_status as active
            'assigned_users' => $task->assigned_users,
            'created_by' => $task->created_by, // Include files if needed
        ]);
    }
    private function cloneCustomerVisit($visit)
    {
        CustomerVisit::create([
            'customer_id' => $visit->customer_id,
            'title' => $visit->title,
            'description' => $visit->description,
            'assigned_users' => $visit->assigned_users,
            'date' => $visit->date,
            'time' => $visit->time,
            'location' => $visit->location,
            'status' => $visit->status,
            'recurrence_status' => 1, // Set recurrence_status as active
            'recurrence' => $visit->recurrence, // Keep the same recurrence type
            'repeat_interval' => $visit->repeat_interval,
            'end_recurrence' => $visit->end_recurrence, // Optionally adjust this as needed
            'reminder' => $visit->reminder,
            'files' => $visit->files, // Include files if needed
            'created_by' => $visit->created_by, // Include files if needed

        ]);
    }

}
