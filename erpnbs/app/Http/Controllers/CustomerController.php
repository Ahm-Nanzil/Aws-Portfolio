<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Imports\CustomerImport;
use App\Models\Customer;
use App\Models\CustomField;
use App\Models\Transaction;
use App\Models\Utility;
use Auth;
use App\Models\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use App\Models\Pipeline;
use App\Models\CustomerStage;
use App\Models\Source;
use App\Models\DealEmailTemplate;
use App\Models\CustomerEmail;
use App\Models\UserLead;
use App\Models\Hierarchy;
use App\Models\CustomerCall;
use App\Models\CustomerTask;
use App\Models\Employee;
use App\Models\CustomerMeeting;
use App\Models\CustomerVisit;
use App\Mail\SendCustomerEmail;
use App\Models\CustomerDiscussion;
use App\Models\CustomerFile;
use App\Models\ProductService;
use App\Models\CustomerActivityLog;

class CustomerController extends Controller
{


    private static $customerData = NULL;


    public function dashboard()
    {
        $data['invoiceChartData'] = \Auth::user()->invoiceChartData();

        return view('customer.dashboard', $data);
    }

    public function index()
    {
        if(\Auth::user()->can('manage customer'))
        {
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->get();
            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines->pluck('name', 'id');

            $usr      = \Auth::user();
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
            return view('customer.index', compact('customers','pipelines','pipeline'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customer_list()
    {
        if(\Auth::user()->can('manage customer'))
        {
            $customers = Customer::where('created_by', \Auth::user()->creatorId())->get();
            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines->pluck('name', 'id');

            $usr      = \Auth::user();
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
            return view('customer.list', compact('customers','pipelines','pipeline'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create customer'))
        {
            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines;
            $stages = CustomerStage::all();
            $sources = Source::all()->pluck('name', 'id');

            return view('customer.create', compact('customFields','pipelines','stages','sources'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {
        if(\Auth::user()->can('create customer'))
        {

            $rules = [
                        'company_website' => 'required|unique:customers,company_website'

            ];


            $validator = \Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json([
                    'error' => $messages->first()
                ], 400);

            }
            $pipeline = Pipeline::where('id', '=', $request->pipeline)->first();

            $stage = CustomerStage::where('id', '=', $request->stage_id)->first();

            // dd($request->company_entity_logo);

            if(empty($stage))
            {
                return response()->json([
                    'error' => __('Please Create Customer Stage for This Pipeline.')
                ], 400);
            }
            else{
                $objCustomer    = \Auth::user();
                $creator        = User::find($objCustomer->creatorId());
                $total_customer = $objCustomer->countCustomers();


                    $default_language          = DB::table('settings')->select('value')->where('name', 'default_language')->first();


                    $customer                  = new Customer();
                    $customer->customer_id     = $this->customerNumber();
                    $customer->name            = $request->company_entity_name;
                    $customer->contact         = $request->company_phone_ll1;
                    $customer->email           = $request->company_email;
                    $customer->tax_number      = $request->tax_number;
                    $customer->pipeline_id     = $request->pipeline_id;
                    $customer->stage_id        = $request->stage_id;
                    $customer->sources          = implode(',', $request->sources);

                    $customer->created_by      = \Auth::user()->id;
                    $customer->billing_name    = $request->billing_name;
                    $customer->billing_country = $request->billing_country;
                    $customer->billing_state   = $request->billing_state;
                    $customer->billing_city    = $request->billing_city;
                    $customer->billing_phone   = $request->billing_phone;
                    $customer->billing_zip     = $request->billing_zip;
                    $customer->billing_address = $request->billing_address;

                    $customer->shipping_name    = $request->shipping_name;
                    $customer->shipping_country = $request->shipping_country;
                    $customer->shipping_state   = $request->shipping_state;
                    $customer->shipping_city    = $request->shipping_city;
                    $customer->shipping_phone   = $request->shipping_phone;
                    $customer->shipping_zip     = $request->shipping_zip;
                    $customer->shipping_address = $request->shipping_address;

                    $customer->lang = !empty($default_language) ? $default_language->value : '';
                    $customer->company_website      = $request->company_website;
                    $customer->company_entity_name  = $request->company_entity_name;

                    // Save company entity logo file
                    if ($request->hasFile('company_entity_logo')) {
                        $path = $request->file('company_entity_logo')->store('customers', 'public'); // Store in 'storage/app/public/logos'
                        $customer->company_entity_logo = $path; // Save the file path to the customer model
                    }
                    $customer->company_phone_ll1    = $request->company_phone_ll1;
                    $customer->company_phone_ll2    = $request->company_phone_ll2;
                    $customer->company_email        = $request->company_email;
                    $customer->address1             = $request->address1;
                    $customer->address2             = $request->address2;
                    $customer->city                 = $request->city;
                    $customer->region               = $request->region;
                    $customer->country              = $request->country;
                    $customer->zip_code             = $request->zip_code;
                    $customer->company_linkedin     = $request->company_linkedin;
                    $customer->company_location     = $request->company_location;

                     // Primary Contact Info
                    $customer->salutation           = $request->salutation;
                    $customer->first_name           = $request->first_name;
                    $customer->last_name            = $request->last_name;
                    $customer->mobile_primary       = $request->mobile_primary;
                    $customer->mobile_secondary     = $request->mobile_secondary;
                    $customer->email_work           = $request->email_work;
                    $customer->email_personal       = $request->email_personal;
                    $customer->phone_ll             = $request->phone_ll;
                    $customer->company_phone_ll     = $request->company_phone_ll;
                    $customer->extension            = $request->extension;
                    $customer->linkedin_profile     = $request->linkedin_profile;

                    $customer->notes                 = $request->notes;
                    $customer->additional_contacts = json_encode($request->additional_contacts);

                    $customer->save();
            }


                CustomField::saveData($customer, $request->customField);

                //For Notification
                $setting  = Utility::settings(\Auth::user()->creatorId());
                $customerNotificationArr = [
                    'user_name' => \Auth::user()->name,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                ];
                // dd($customer);

                //Twilio Notification
                if(isset($setting['twilio_customer_notification']) && $setting['twilio_customer_notification'] ==1)
                {
                    Utility::send_twilio_msg($request->contact,'new_customer', $customerNotificationArr);
                }


            return redirect()->back()->with('success', __('Customer successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($ids)
    {
        $id       = \Crypt::decrypt($ids);
        $customer = Customer::find($id);
        if (request()->ajax()) {
            // Return the partial view if the request is AJAX
            return view('customer.show-html', compact('customer'));
        }
        return view('customer.show', compact('customer'));
    }


    public function edit($id)
    {
        if(\Auth::user()->can('edit customer'))
        {
            $customer              = Customer::find($id);
            $customer->customField = CustomField::getData($customer, 'customer');

            $customFields = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

            $pipelines = Pipeline::all(); // Fetch all pipelines
            $user = \Auth::user(); // or Auth::user()

            $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

            $filteredPipelines = $pipelines->filter(function ($pipeline) use ($userPermissions) {
                $permissionName = 'manage pipeline ' . $pipeline->name; // Assuming you are checking for the 'manage' permission

                return in_array($permissionName, $userPermissions);
            });



            $pipelines = $filteredPipelines->pluck('name', 'id');
            // $pipelines->prepend(__('Select Pipeline'), '');
            $stages = CustomerStage::all();
            $sources = Source::all()->pluck('name', 'id');
            return view('customer.edit', compact('customer', 'customFields','pipelines','stages','sources'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, Customer $customer)
    {

        if(\Auth::user()->can('edit customer'))
        {

            $rules = [

                // 'company_website' => 'required|unique:customers,company_website'
            ];


            $validator = \Validator::make($request->all(), $rules);
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json([
                    'error' => $messages->first()
                ], 400);

            }

                    $customer->name            = $request->company_entity_name;
                    $customer->contact         = $request->company_phone_ll1;
                    $customer->email           = $request->company_email;
                    $customer->tax_number      = $request->tax_number;
                    $customer->pipeline_id     = $request->pipeline_id;
                    $customer->stage_id        = $request->stage_id;
                    $customer->sources          = implode(',', $request->sources);

                    $customer->created_by      = \Auth::user()->id;
            $customer->billing_name     = $request->billing_name;
            $customer->billing_country  = $request->billing_country;
            $customer->billing_state    = $request->billing_state;
            $customer->billing_city     = $request->billing_city;
            $customer->billing_phone    = $request->billing_phone;
            $customer->billing_zip      = $request->billing_zip;
            $customer->billing_address  = $request->billing_address;

            $customer->shipping_name    = $request->shipping_name;
            $customer->shipping_country = $request->shipping_country;
            $customer->shipping_state   = $request->shipping_state;
            $customer->shipping_city    = $request->shipping_city;
            $customer->shipping_phone   = $request->shipping_phone;
            $customer->shipping_zip     = $request->shipping_zip;
            $customer->shipping_address = $request->shipping_address;

            $customer->company_website      = $request->company_website;
                    $customer->company_entity_name  = $request->company_entity_name;

                    // Save company entity logo file
                    if ($request->hasFile('company_entity_logo')) {
                        $path = $request->file('company_entity_logo')->store('customers', 'public'); // Store in 'storage/app/public/logos'
                        $customer->company_entity_logo = $path; // Save the file path to the customer model
                    }
                    $customer->company_phone_ll1    = $request->company_phone_ll1;
                    $customer->company_phone_ll2    = $request->company_phone_ll2;
                    $customer->company_email        = $request->company_email;
                    $customer->address1             = $request->address1;
                    $customer->address2             = $request->address2;
                    $customer->city                 = $request->city;
                    $customer->region               = $request->region;
                    $customer->country              = $request->country;
                    $customer->zip_code             = $request->zip_code;
                    $customer->company_linkedin     = $request->company_linkedin;
                    $customer->company_location     = $request->company_location;

                     // Primary Contact Info
                    $customer->salutation           = $request->salutation;
                    $customer->first_name           = $request->first_name;
                    $customer->last_name            = $request->last_name;
                    $customer->mobile_primary       = $request->mobile_primary;
                    $customer->mobile_secondary     = $request->mobile_secondary;
                    $customer->email_work           = $request->email_work;
                    $customer->email_personal       = $request->email_personal;
                    $customer->phone_ll             = $request->phone_ll;
                    $customer->company_phone_ll     = $request->company_phone_ll;
                    $customer->extension            = $request->extension;
                    $customer->linkedin_profile     = $request->linkedin_profile;

                    $customer->notes                 = $request->notes;
                    $customer->save();

            CustomField::saveData($customer, $request->customField);

            return redirect()->back()->with('success', __('Customer successfully updated.'));
        }
        else
        {
            return response()->json([
                'error' => __('Permission denied.')
            ], 403);

        }
    }


    public function destroy(Customer $customer)
    {
        if(\Auth::user()->can('delete customer'))
        {
            if($customer)
            {

                $customer->delete();

                return redirect()->back()->with('success', __('Customer successfully deleted.'));
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

    function customerNumber()
    {
        $latest = Customer::where('created_by', '=', \Auth::user()->creatorId())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->customer_id + 1;
    }

    public function customerLogout(Request $request)
    {
        \Auth::guard('customer')->logout();

        $request->session()->invalidate();

        return redirect()->route('customer.login');
    }

    public function payment(Request $request)
    {

        if(\Auth::user()->can('manage customer payment'))
        {
            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer')->where('type', 'Payment');
            if(!empty($request->date))
            {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if(!empty($request->category))
            {
                $query->where('category', '=', $request->category);
            }
            $payments = $query->get();

            return view('customer.payment', compact('payments', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function transaction(Request $request)
    {
        if(\Auth::user()->can('manage customer payment'))
        {
            $category = [
                'Invoice' => 'Invoice',
                'Deposit' => 'Deposit',
                'Sales' => 'Sales',
            ];

            $query = Transaction::where('user_id', \Auth::user()->id)->where('user_type', 'Customer');

            if(!empty($request->date))
            {
                $date_range = explode(' - ', $request->date);
                $query->whereBetween('date', $date_range);
            }

            if(!empty($request->category))
            {
                $query->where('category', '=', $request->category);
            }
            $transactions = $query->get();

            return view('customer.transaction', compact('transactions', 'category'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profile()
    {
        $userDetail              = \Auth::user();
        $userDetail->customField = CustomField::getData($userDetail, 'customer');
        $customFields            = CustomField::where('created_by', '=', \Auth::user()->creatorId())->where('module', '=', 'customer')->get();

        return view('customer.profile', compact('userDetail', 'customFields'));
    }

    public function editprofile(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);

        $this->validate(
            $request, [
                        'name' => 'required|max:120',
                        'contact' => 'required',
                        'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                    ]
        );

        if($request->hasFile('profile'))
        {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir        = storage_path('uploads/avatar/');
            $image_path = $dir . $userDetail['avatar'];

            if(File::exists($image_path))
            {
                File::delete($image_path);
            }

            if(!file_exists($dir))
            {
                mkdir($dir, 0777, true);
            }

            $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);

        }

        if(!empty($request->profile))
        {
            $user['avatar'] = $fileNameToStore;
        }
        $user['name']    = $request['name'];
        $user['email']   = $request['email'];
        $user['contact'] = $request['contact'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function editBilling(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'billing_name' => 'required',
                        'billing_country' => 'required',
                        'billing_state' => 'required',
                        'billing_city' => 'required',
                        'billing_phone' => 'required',
                        'billing_zip' => 'required',
                        'billing_address' => 'required',
                    ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function editShipping(Request $request)
    {
        $userDetail = \Auth::user();
        $user       = Customer::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'shipping_name' => 'required',
                        'shipping_country' => 'required',
                        'shipping_state' => 'required',
                        'shipping_city' => 'required',
                        'shipping_phone' => 'required',
                        'shipping_zip' => 'required',
                        'shipping_address' => 'required',
                    ]
        );
        $input = $request->all();
        $user->fill($input)->save();

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }


    public function changeLanquage($lang)
    {

        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language Change Successfully!'));

    }


    public function export()
    {
        $name = 'customer_' . date('Y-m-d i:h:s');
        $data = Excel::download(new CustomerExport(), $name . '.xlsx'); ob_end_clean();

        return $data;
    }

    public function importFile()
    {
        return view('customer.import');
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

        $customers = (new CustomerImport())->toArray(request()->file('file'))[0];

        $totalCustomer = count($customers) - 1;
        $errorArray    = [];
        for($i = 1; $i <= count($customers) - 1; $i++)
        {
            $customer = $customers[$i];

            $customerByEmail = Customer::where('email', $customer[2])->first();
            if(!empty($customerByEmail))
            {
                $customerData = $customerByEmail;
            }
            else
            {
                $customerData = new Customer();
                $customerData->customer_id      = $this->customerNumber();
            }


            $customerData->customer_id             = $customer[0];
            $customerData->name             = $customer[1];
            $customerData->email            = $customer[2];
            $customerData->contact          = $customer[3];
            $customerData->is_active        = 1;
            $customerData->billing_name     = $customer[4];
            $customerData->billing_country  = $customer[5];
            $customerData->billing_state    = $customer[6];
            $customerData->billing_city     = $customer[7];
            $customerData->billing_phone    = $customer[8];
            $customerData->billing_zip      = $customer[9];
            $customerData->billing_address  = $customer[10];
            $customerData->shipping_name    = $customer[11];
            $customerData->shipping_country = $customer[12];
            $customerData->shipping_state   = $customer[13];
            $customerData->shipping_city    = $customer[14];
            $customerData->shipping_phone   = $customer[15];
            $customerData->shipping_zip     = $customer[16];
            $customerData->shipping_address = $customer[17];
            $customerData->balance          = $customer[18];
            $customerData->created_by       = \Auth::user()->creatorId();

            if(empty($customerData))
            {
                $errorArray[] = $customerData;
            }
            else
            {
                $customerData->save();
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
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalCustomer . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }

    public function showDeal($ids)
    {
        $id       = \Crypt::decrypt($ids);
        $customer = Customer::find($id);

        // dd($customer);

        return view('customer.show', compact('customer'));
    }

    public function order(Request $request)
    {
        if(\Auth::user())
        {
            $usr        = \Auth::user();
            $post       = $request->all();
            // dd($post);
            $customer       = $this->customer($post['customer_id']);
            // $customer_users = $customer->users->pluck('email', 'id')->toArray();

            if($customer->stage_id != $post['stage_id'])
            {
                $newStage = CustomerStage::find($post['stage_id']);

                CustomerActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'customer_id' => $customer->id,
                        'log_type' => 'Move',
                        'remark' => json_encode(
                            [
                                'title' => $customer->name,
                                'old_status' => $customer->stage->name,
                                'new_status' => $newStage->name,
                            ]
                        ),
                    ]
                );

                // $leadArr = [
                //     'lead_id' => $lead->id,
                //     'name' => $lead->name,
                //     'updated_by' => $usr->id,
                //     'old_status' => $lead->stage->name,
                //     'new_status' => $newStage->name,
                // ];

                // $lArr = [
                //     'lead_name' => $lead->name,
                //     'lead_email' => $lead->email,
                //     'lead_pipeline' => $lead->pipeline->name,
                //     'lead_stage' => $lead->stage->name,
                //     'lead_old_stage' => $lead->stage->name,
                //     'lead_new_stage' => $newStage->name,
                // ];

                // Send Email
                // Utility::sendEmailTemplate('Move Lead', $lead_users, $lArr);
                // dd($newStage);

            }

            foreach($post['order'] as $key => $item)
            {
                $customer           = $this->customer($item);
                // dd($customer);
                // $customer->order    = $key;
                $customer->stage_id = $post['stage_id'];
                $customer->save();
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }
    public function customer($id)
    {
        if(self::$customerData == null)
        {
            $customer = Customer::find($id);
            self::$customerData = $customer;
        }

        return self::$customerData;
    }
    public function changePipeline(Request $request)
    {
        $user                   = \Auth::user();
        $user->default_pipeline = $request->default_pipeline_id;
        $user->save();

        return redirect()->back();
    }

    public function basicinfoEdit($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $users = User::where('created_by', '=', \Auth::user()->creatorId())->where('type', '!=', 'client')->where('type', '!=', 'company')->where('id', '!=', \Auth::user()->id)->get()->pluck('name', 'id');
                $users->prepend(__('Select User'), '');
                return view('customer.basicinfo-edit', compact('customer','users'));
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
            $customer = Customer::find($id);

            if ($customer) {
                // Validate the incoming request data
                $request->validate([
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
                $customer->company_website = $request->company_website;
                $customer->company_entity_name = $request->company_entity_name;
                $customer->company_phone_ll1 = $request->company_phone_ll1;
                $customer->company_phone_ll2 = $request->company_phone_ll2;
                $customer->company_email = $request->company_email;
                $customer->address1 = $request->address1;
                $customer->address2 = $request->address2;
                $customer->city = $request->city;
                $customer->region = $request->region;
                $customer->country = $request->country;
                $customer->zip_code = $request->zip_code;
                $customer->company_linkedin = $request->company_linkedin;
                $customer->company_location = $request->company_location;

                // Handle file upload for company logo
                if ($request->hasFile('company_entity_logo')) {
                    $path = $request->file('company_entity_logo')->store('leads', 'public'); // Store in 'storage/app/public/logos'
                    $customer->company_entity_logo = $path; // Save the file path to the lead model
                }

                // Save changes to lead
                $customer->save();

                return redirect()->back()->with('success', 'Information updated successfully.');

            } else {
                return redirect()->back()->with('error', 'Customer not found.');

            }
        } else {
            return redirect()->route('login')->with('error', 'You must be logged in to update information.');

        }
    }

    public function sourceEdit($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $sources = Source::where('created_by', '=', \Auth::user()->creatorId())->get();

                $selected = $customer->sources();
                if($selected)
                {
                    $selected = $selected->pluck('name', 'id')->toArray();
                }

                return view('customer.sources', compact('customer', 'sources', 'selected'));
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

    public function sourceUpdate($id, Request $request)
    {
        if(\Auth::user())
        {
            $usr        = \Auth::user();
            $customer       = Customer::find($id);

            if($customer)
            {
                if(!empty($request->sources) && count($request->sources) > 0)
                {
                    $customer->sources = implode(',', $request->sources);
                }
                else
                {
                    $customer->sources = "";
                }

                CustomerActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'customer_id' => $customer->id,
                        'log_type' => 'Update Sources',
                        'remark' => json_encode(['title' => 'Update Sources']),
                    ]
                );

                $customer->save();




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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $sources = explode(',', $customer->sources);
                foreach($sources as $key => $source)
                {
                    if($source_id == $source)
                    {
                        unset($sources[$key]);
                    }
                }
                $customer->sources = implode(',', $sources);
                $customer->save();

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

    public function primarycontactEdit($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                return view('customer.primary-contactinfo', compact('customer'));
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
            $customer = Customer::find($id);

            if ($customer) {
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

                // Update the customer's primary contact fields
                $customer->salutation = $request->salutation;
                $customer->first_name = $request->first_name;
                $customer->last_name = $request->last_name;
                $customer->mobile_primary = $request->mobile_primary;
                $customer->mobile_secondary = $request->mobile_secondary;
                $customer->email_work = $request->email_work;
                $customer->email_personal = $request->email_personal;
                $customer->phone_ll = $request->phone_ll;
                $customer->company_phone_ll = $request->company_phone_ll;
                $customer->extension = $request->extension;
                $customer->linkedin_profile = $request->linkedin_profile;

                // Save changes to customer
                $customer->save();

                return redirect()->back()->with('success', __('Primary contact information updated successfully.'));

            } else {
                return redirect()->back()->with('error', __('Customer not found.'));

            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));

        }
    }
    public function contactCreate($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);

            if($customer)
            {
                return view('customer.additional-contact', compact('customer'));
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
        $customer = Customer::findOrFail($id); // Will throw a 404 if not found

        $contactsArray = json_decode($customer->additional_contacts, true) ?? [];

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
        $customer->additional_contacts = json_encode($contactsArray);
        $customer->save();

        // Render the updated contact list as a partial view and return it as JSON for AJAX
        // $updatedContactsView = view('leads.partials.additional_contacts_list', ['contacts' => $contactsArray])->render();

        return redirect()->back()->with('success', 'Contact created successfully.');
    }
    public function contactDistroy($customerId, $index)
    {
        $customer = Customer::findOrFail($customerId);
        $additionalContacts = json_decode($customer->additional_contacts, true) ?? [];

        if (isset($additionalContacts[$index])) {
            // Remove the specified contact
            unset($additionalContacts[$index]);
            $additionalContacts = array_values($additionalContacts); // Reindex the array
            $customer->additional_contacts = json_encode($additionalContacts);
            $customer->save();
        }

        return redirect()->back()->with('success', 'Contact deleted successfully.');
    }

    public function checkDuplicateWebsite(Request $request)
    {
        $request->validate([
            'website' => 'required|url',
        ]);

        $exists = Customer::where('company_website', $request->website)->exists();

        if ($exists) {
            return response()->json(['exists' => true, 'message' => 'This website is already registered.']);
        }

        return response()->json(['exists' => false, 'message' => 'This website is available.']);
    }

    public function emailCreate($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            $emailTemplates = DealEmailTemplate::where('page_name', 'customer')->get();

            if($customer->created_by == \Auth::user()->creatorId())
            {
                return view('customer.emails', compact('customer','emailTemplates'));
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
        if (\Auth::user()) {
            $customer = Customer::find($id);

            if ($customer) {
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
                $customerEmail = CustomerEmail::create(
                    [
                        'customer_id' => $customer->id,
                        'to' => $request->to,
                        'subject' => $request->subject,
                        'description' => $request->description,
                    ]
                );

                // $customerEmailData = [
                //     'customer_name' => $customer->name,
                //     'to' => $request->to,
                //     'subject' => $request->subject,
                //     'description' => $request->description,
                // ];
                $customerEmailData = [
                    'customer_name' => $customer->name,
                    'to' => $request->to,
                    'subject' => $request->subject,
                    'description' => $request->description,
                ];

                if ($request->has('save_as_template')) {
                    $emailData = [
                    'to' => $request->to,
                    'subject' => $request->subject,
                    'description' => $request->description,
                    'page_name' => 'customer',

                    ];

                    DealEmailTemplate::create($emailData);
            }

                    if ($request->has('scheduled_time') && $request->scheduled_time) {
                        $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->scheduled_time, config('app.timezone'));
                        $delay = $scheduledTime->diffInSeconds(now());

                        if ($delay > 0) {
                            try {
                                Mail::to($request->to)->later(now()->addSeconds($delay), new SendCustomerEmail($customerEmailData, $settings));
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
                                        Mail::to($request->to)->send(new SendCustomerEmail($customerEmailData, $settings));
                                    } catch (\Exception $e) {
                                        $smtp_error = __('E-Mail has not been sent due to SMTP configuration');
                                    }
                                }

                CustomerActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'customer_id' => $customer->id,
                        'log_type' => 'create customer email',
                        'remark' => json_encode(['title' => 'Create new Customer Email']),
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


    public function callCreate($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id');
                // $users = UserLead::where('lead_id', '=', $customer->id)->get();

                return view('customer.calls', compact('customer', 'assignedUsers'));
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
        if(\Auth::user())
        {
            $usr  = \Auth::user();
            $customer = Customer::find($id);
            if($customer)
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'subject' => 'required',
                                       'call_type' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $assignedUsers = $request->input('assigned_users') ? json_encode($request->input('assigned_users')) : null;

                $customerCall = CustomerCall::create(
                    [
                        'customer_id' => $customer->id,
                        'subject' => $request->subject,
                        'call_type' => $request->call_type,
                        'duration' => $request->duration,
                        'assigned_users' => $request->$assignedUsers,
                        'description' => $request->description,
                        'call_result' => $request->call_result,
                    ]
                );

                CustomerActivityLog::create(
                    [
                        'user_id' => $usr->id,
                        'customer_id' => $customer->id,
                        'log_type' => 'create customer call',
                        'remark' => json_encode(['title' => 'Create new Customer Call']),
                    ]
                );


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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $call  = customerCall::find($call_id);
                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id');
                return view('customer.calls', compact('call', 'customer', 'assignedUsers'));
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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'subject' => 'required',
                                       'call_type' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $call = CustomerCall::find($call_id);

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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $call = CustomerCall::find($call_id);
                $call->delete();

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

    public function taskCreate($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $priorities = CustomerTask::$priorities;
                $status     = CustomerTask::$status;
                $recurrances = CustomerTask::$recurrances;

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); // Assuming 'name' is the attribute for user names
                // dd($assignedUsers);
                return view('customer.tasks', compact('customer', 'priorities', 'status', 'recurrances','assignedUsers'));

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
        if($usr)
        {
            // dd($request->all());
            $assignedUsersArray = $request->input('assigned_users');

            $customer       = Customer::find($id);
            // $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $customer_users = array_map('intval', $assignedUsersArray); // Convert to array of integers
            $usrs       = User::whereIN('id', array_merge($customer_users))->get()->pluck('email', 'id')->toArray();

            // dd($customer_users, );
            if($customer)
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


                $customerTask = customerTask::create(
                    [
                        'customer_id' => $customer->id,
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
                //         'remark' => json_encode(['title' => $customerTasks->name]),
                //     ]
                // );





                $taskArr = [
                    'task_creator' => $usr->name,
                    'task_name' => $customerTask->name,
                    'task_priority' => $customerTask->priority,
                    'task_status' => $customerTask->status,
                    'from' => 'Customer',
                    'from_name' => $customer->name,
                    'pipeline' => $customer->pipeline->name,
                    'stage' => $customer->stage->name,
                    'link' => route('customer.show', $customer->id),


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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $priorities = CustomerTask::$priorities;
                $status     = CustomerTask::$status;
                $task       = CustomerTask::find($task_id);
                $recurrances = CustomerTask::$recurrances;

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); //

                return view('customer.tasks', compact('task','customer', 'priorities', 'status', 'recurrances','assignedUsers'));

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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
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

                $task = CustomerTask::find($task_id);
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
            $customer = Customer::find($id);
            if($customer)
            {
                $task = CustomerTask::find($task_id);
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
        if(\Auth::user())
        {


                $members   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $settings = Utility::settings();

            return view('customer.meeting', compact('members','settings','id'));
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

        // dd($id);
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        if(\Auth::user())
        {
            $meeting                = new CustomerMeeting();
            $meeting->members   = json_encode($request->members);
            $meeting->title         = $request->title;
            $meeting->date          = $request->date;
            $meeting->time          = $request->time;
            $meeting->note          = $request->note;
            $meeting->customer_id       = $id;
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
                $request1=new CustomerMeeting();
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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $members   = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $settings = Utility::settings();
                $meeting= CustomerMeeting::find($meeting_id);
                $meeting->members = json_decode($meeting->members, true);

                return view('customer.meeting', compact('members','settings','customer','meeting'));

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
        if(\Auth::user())
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
            $meeting = CustomerMeeting::find($meeting_id);
            if (!$meeting) {
                return redirect()->back()->with('error', __('Meeting not found.'));
            }

            // Check if the authenticated user is the creator of the meeting
            if($meeting)
            {
                $meeting->members   = json_encode($request->members);
                $meeting->title     = $request->title;
                $meeting->date      = $request->date;
                $meeting->time      = $request->time;
                $meeting->note      = $request->note;

                $meeting->save();

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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $meeting = CustomerMeeting::find($meeting_id);
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
            $customer = Customer::find($id);
            if($customer)
            {
                $recurrances = CustomerVisit::$recurrances;
                $status     = CustomerVisit::$status;

                $hierarchy = Hierarchy::whereNull('child')->first();

                $user = \Auth::user();
                $userRole = $user->type;
                $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                // Merge the logged-in user with the users under the roles
                $assignedUsers = $usersUnder->push($user)->unique('id'); // Ensure no duplicates

                // Convert assigned users to an associative array
                $assignedUsers = $assignedUsers->pluck('name', 'id'); // Assuming 'name' is the attribute for user names
                // dd($assignedUsers);
                return view('customer.visit', compact('customer', 'recurrances', 'status', 'assignedUsers'));

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

            $customer       = Customer::find($id);
            // $clients    = ClientDeal::select('client_id')->where('deal_id', '=', $id)->get()->pluck('client_id')->toArray();
            $customer_users = array_map('intval', $assignedUsersArray); // Convert to array of integers
            $usrs       = User::whereIN('id', array_merge($customer_users))->get()->pluck('email', 'id')->toArray();

            // dd($customer_users, );
            if($customer)
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
                $customerVisit = CustomerVisit::create([
                    'customer_id' => $customer->id,
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
                //         'deal_id' => $customerVisit->id,
                //         'log_type' => 'Create Deal',
                //         'remark' => json_encode(['title' => $customerVisit->title]),
                //     ]
                // );


                $taskArr = [
                    'visit_creator' => $customerVisit->created_by,
                    'visit_name' => $customerVisit->title,
                    'from' => 'Customer',
                    'from_name' => $customer->name,
                    'pipeline' => $customer->pipeline->name,
                    'stage' => $customer->stage->name,

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
            $customer = Customer::find($id);
            if($customer)
            {
                $recurrances = CustomerVisit::$recurrances;
                $status     = CustomerVisit::$status;

                $hierarchy = Hierarchy::whereNull('child')->first();

                    $user = \Auth::user();
                    $userRole = $user->type;

                    $usersUnder = $hierarchy->getUsersUnderRoles($userRole);

                    $assignedUsers = $usersUnder->push($user)->unique('id');

                    $assignedUsers = $assignedUsers->pluck('name', 'id')->toArray();



                $visit       = CustomerVisit::find($visit_id);
                // $fileIds = json_decode($visit->file_ids, true); // This will be an array of strings like ["30,31,32"]
                // $fileIdsArray = explode(',', $fileIds[0]); // Converts "30,31,32" into [30, 31, 32]
                // $visitFiles = DealFile::whereIn('id', $fileIdsArray)->get();
                return view('customer.visit', compact('customer', 'recurrances', 'status', 'assignedUsers', 'visit'));
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
            $customer = Customer::find($id);
            if($customer)
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

                $visit = CustomerVisit::find($visit_id);

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



                return redirect()->back()->with('success', __('Successfully updated!'))->with('status', 'tasks');
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
            $customer = Customer::find($id);
            if($customer)
            {
                $visit = CustomerVisit::find($visit);
                $visit->delete();

                return redirect()->back()->with('success', __('Successfully deleted!'))->with('status', 'tasks');
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

    public function discussionStore($id, Request $request)
    {
        $usr        = \Auth::user();
        $customer       = Customer::find($id);
        $customer_users = $customer->users->pluck('id')->toArray();

        if($customer)
        {
            $discussion             = new CustomerDiscussion();
            $discussion->comment    = $request->comment;
            $discussion->customer_id    = $customer->id;
            $discussion->created_by = $usr->id;
            $discussion->save();



            return redirect()->back()->with('success', __('Message successfully added!'))->with('status', 'discussion');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'))->with('status', 'discussion');
        }
    }

        public function fileUpload($id, Request $request)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $request->validate(['file' => 'required']);
                $file_name = $request->file->getClientOriginalName();
                $file_path = $request->customer_id . "_" . md5(time()) . "_" . $request->file->getClientOriginalName();
                $request->file->storeAs('customer_files', $file_path);
                $file                 = CustomerFile::create(
                    [
                        'customer_id' => $request->customer_id,
                        'file_name' => $file_name,
                        'file_path' => $file_path,
                    ]
                );
                $return               = [];
                $return['is_success'] = true;
                $return['file_id'] = $file->id;

                $return['download']   = route(
                    'customer.file.download', [
                                             $customer->id,
                                             $file->id,
                                         ]
                );
                $return['delete']     = route(
                    'customer.file.delete', [
                                           $customer->id,
                                           $file->id,
                                       ]
                );
                CustomerActivityLog::create(
                    [
                        'user_id' => \Auth::user()->id,
                        'customer_id' => $customer->id,
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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $file = CustomerFile::find($file_id);
                if($file)
                {
                    $file_path = storage_path('customer_files/' . $file->file_path);
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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $file = CustomerFile::find($file_id);
                if($file)
                {
                    $path = storage_path('customer_files/' . $file->file_path);
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

    public function productEdit($id)
    {
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $products = ProductService::where('created_by', '=', \Auth::user()->creatorId())->whereNOTIn('id', explode(',', $customer->products))->get()->pluck('name', 'id');

                return view('customer.products', compact('customer', 'products'));
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
        if(\Auth::user())
        {
            $usr        = \Auth::user();
            $customer       = Customer::find($id);
            $customer_users = $customer->users->pluck('id')->toArray();

            if($customer)
            {
                if(!empty($request->products))
                {
                    $products       = array_filter($request->products);
                    $old_products   = explode(',', $customer->products);
                    $customer->products = implode(',', array_merge($old_products, $products));
                    $customer->save();

                    $objProduct = ProductService::whereIN('id', $products)->get()->pluck('name', 'id')->toArray();

                    CustomerActivityLog::create(
                        [
                            'user_id' => $usr->id,
                            'customer_id' => $customer->id,
                            'log_type' => 'Add Product',
                            'remark' => json_encode(['title' => implode(",", $objProduct)]),
                        ]
                    );

                    $productArr = [
                        'lead_id' => $customer->id,
                        'name' => $customer->name,
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
        if(\Auth::user())
        {
            $customer = Customer::find($id);
            if($customer)
            {
                $products = explode(',', $customer->products);
                foreach($products as $key => $product)
                {
                    if($product_id == $product)
                    {
                        unset($products[$key]);
                    }
                }
                $customer->products = implode(',', $products);
                $customer->save();

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


    public function json(Request $request)
    {
        $customer_stages = new CustomerStage();
        if($request->pipeline_id && !empty($request->pipeline_id))
        {


            $customer_stages = $customer_stages->where('pipeline_id', '=', $request->pipeline_id);
            $customer_stages = $customer_stages->get()->pluck('name', 'id');
        }
        else
        {
            $customer_stages = [];
        }

        return response()->json($customer_stages);
    }





}
