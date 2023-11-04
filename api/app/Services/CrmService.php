<?php
namespace App\Services;

use App\Http\Resources\CrmHomesResource;
use App\Http\Resources\CrmUserResource;
use App\Http\Resources\CrmUserStructureResource;
use App\Models\CrmUser;
use App\Models\CrmUserHasFile;
use App\Models\CrmUserHasHome;
use App\Models\Employe;
use App\Models\Home;
use Carbon\Carbon;


class CrmService
{
    public $keyToValue = [
        "new-client"=> "Նոր հաճախորդ",
        "contract-show"=> "Պայմանագիր-ցուցադրություն",
        "pay"=> "Նախավճար",
        "apartment"=> "Բնակարան",
        "open"=> "Գործարքի բացում",
        "fail"=> "Ձախողում",
        "sucess"=> "Հաջողված գործարք",
        'privateHouse' => "Առանձնատուն",
        'commercial' => "Կոմերցիոն",
        'sale' => "Վաճառք",
        'rent' => "Վարձակալություն",
        'private_house' => "Առանձնատուն",
    ];


    public function getHomesForCrm()
    {
        $allHome = Home::all();

        return CrmHomesResource::collection($allHome);

    }

    public function addCrmUser($request)
    {
        $user = new CrmUser();
        $user->name = $request['name'];
        $user->phone = $request['phone'];
        $user->email = $request['email'];
        $user->employee_id = $request['specialist'];
        $user->contract_number = $request['contractNumber'];
        $user->source = $request['source'];
        $user->deal = $request['deal'];
        $user->property_type = $request['propertyType'];
        $user->room = $request['room'];
        $user->budget = $request['budget'];
        $user->comment = $request['comment'];
        $user->status = $request['status'];
        $user->save();

        $displayedHomes = json_decode($request['displayedHomes'], true);

        $homeToInsert = [];
        foreach ($displayedHomes as $key => $home) {
            $homeToInsert[] = [
                'user_id' => $user->id,
                'home_id' => $home['id'],
                'display_at' => Carbon::parse($home['date']),
            ];
        }

        if($homeToInsert) { 
            CrmUserHasHome::insert($homeToInsert);
        }

        $filesToInsert = [];
        foreach ($request as $key => $item) {
            if(gettype($item) == 'object'){
                $fileName = round(microtime(true) * 1000).'.'.$item->extension();
                $realName = $item->getClientOriginalName();
                $path = 'crmfiles/' . $fileName;
                $filesToInsert[] = [
                    'user_id' => $user->id,
                    'name' => $fileName,
                    'real_name' => $realName,
                    'path' => $path,
                ];
                $item->move(public_path('crmfiles'), $fileName);

            }
        }

        if($filesToInsert) { 
            CrmUserHasFile::insert($filesToInsert);
        }

        return $user;
    }

    public function editCrmUser($request)
    {
        //check status employee, if agent remove telephone and mail
        //jnjel tnery nor tazeqy avelacnel   
        //STUGEL ete ka tuny kam filen el chavelacnel
        // $crmList = Cr
        dd($request);
    }

    public function getCrmUsers()
    {
        $users = CrmUser::with('homes')->get();
        // dd($users);
        $customResoucre = $this->makeResoucre($users);
        // return CrmUserResource::collection($users);

        return $customResoucre;
        
    }

    public function makeResoucre($users)
    {
//avelacnel paymany agenti yev admini depqerum 
        $employee = Employe::all();

        $customResource = [];

        foreach ($users as $user) {
            $searchable = [];

            $agent = $this->getAgentName($employee, $user->employee_id);

            $transactionDecode = json_decode($user->property_type);
            $transactionType = [];
            foreach ($transactionDecode as $key => $value) {
                $transaction = $this->keyToValue[$value];
                $transactionType[] = $transaction;
                $searchable[] = $transaction;
            }

            $dealDecode = json_decode($user->deal);
            $deal = [];
            foreach ($dealDecode as $key => $value) { 
                $type = $this->keyToValue[$value];
                $deal[] = $type;
                $searchable[] = $type;
            }

            $status =  $this->keyToValue[$user->status];
            array_push($searchable, $user->name, $user->phone, $agent, $status);

            $customResource[] = [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'property_type' => $transactionDecode,
                'deal' => $dealDecode,
                'room' => $user->room,
                'budget' => $user->budget,
                'agent' => $agent, 
                'status' => $user->status,
                'searchable' => $searchable,
            ];
        }
        return $customResource;
    }


    public function getAgentName($employee, $agentId)
    {
        try {
            if($agentId){
                $name = $employee->where('id', $agentId)->first();
                if($name->full_name){
                    $agentName = json_decode($name->full_name, true);
        
                    return $agentName['am'];
                }
            }
        } catch (\Throwable $th) {
            dd($agentId,  $th->getMessage());
        }
        

    }

    public function getEditUser($id)
    {
        $user = CrmUser::with('homes', 'files')->find($id);

        return new CrmUserStructureResource($user);
    }

    // public function recoverEmployeeRights($homeId): bool
    // {
    //     $auth = auth()->user();
    //     // if($auth->id == $homeId){
    //     $authCrmHomeIds = CrmUser::where('employee_id', $authId)->get()->pluck('id')->toArray();

    //     return in_array($homeId, $authCrmHomeIds);
    // }

 
}