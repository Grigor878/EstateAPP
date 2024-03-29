<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminHomeResource;
use App\Models\Employe;
use Illuminate\Http\Request;
use App\Models\Home;
use App\Services\HomeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;


class HomeController extends Controller
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function addHome(Request $request) {
        $data = $request->all();
        $employee = auth()->user();
        if($employee) {
            $home = new Home();
            $home->employee_id = $employee->id;
            $home->status = $employee->role == "admin" || $employee->role == "moderator" ? Home::STATUS_APPROVED: Home::STATUS_MODERATION;
            $home->photo = json_encode([]);
            $home->file = json_encode([]);
            $home->keywords = json_encode([]);
            $homeLanguageContsructor = $this->homeService->homeLanguageContsructor($data);
            if($homeLanguageContsructor['priceHistory']){
                if($home->price_history){
                    \Log::info(222);
                    \Log::info(Carbon::now()->addHours(4)->format('d/m/Y'));
                    $prices = json_decode($home->price_history);
                    array_push($prices, [
                        "price" => $homeLanguageContsructor['priceHistory'],
                        "date" => Carbon::now()->addHours(4)->format('d/m/Y'),
                    ]);
                    $home->price_history = json_encode($prices);
                }else {
                    \Log::info(111);
                    \Log::info(Carbon::now()->addHours(4)->format('d/m/Y'));
                    $priceDate =[];
                    array_push($priceDate, [
                        "price" => $homeLanguageContsructor['priceHistory'],
                        "date" => Carbon::now()->addHours(4)->format('d/m/Y'),
                    ]);
    
                    $home->price_history = json_encode($priceDate);
                }
            }
            $home->am =json_encode($homeLanguageContsructor['am']);
            $home->ru =json_encode($homeLanguageContsructor['ru']);
            $home->en =json_encode($homeLanguageContsructor['en']);
            $home->save();
            return response()->json($home->id);
        }
    }

    public function editHome($id, Request $request){
        $data = $request->all();
        $home = Home::findorFail($id);
        $homeLanguageContsructor = $this->homeService->homeLanguageContsructorEdit($id, $data);
        if($homeLanguageContsructor['editStatus']) {
            $home->status = auth()->user()->role == "admin" || auth()->user()->role == "moderator" ? Home::STATUS_APPROVED: Home::STATUS_MODERATION;
        }

        if(auth()->user()->role == "admin" || auth()->user()->role == "moderator"){
            $home->status =  Home::STATUS_APPROVED;
        }

        if($homeLanguageContsructor['priceHistory']){
            if($home->price_history){
                \Log::info(333);
                \Log::info(Carbon::now()->addHours(4)->format('d/m/Y'));

                $prices = json_decode($home->price_history, true);
                array_push($prices, [
                    "price" => $homeLanguageContsructor['priceHistory'],
                    "date" => Carbon::now()->addHours(4)->format('d/m/Y'),
                ]);
                $home->price_history = json_encode($prices);
            }else {
                \Log::info(444);
                \Log::info(Carbon::now()->addHours(4)->format('d/m/Y'));
                

                $priceDate =[];
                array_push($priceDate, [
                    "price" => $homeLanguageContsructor['priceHistory'],
                    "date" => Carbon::now()->addHours(4)->format('d/m/Y'),
                ]);

                $home->price_history = json_encode($priceDate);
            }
        }
        $home->am =json_encode($homeLanguageContsructor['am']);
        $home->ru =json_encode($homeLanguageContsructor['ru']);
        $home->en =json_encode($homeLanguageContsructor['en']);
        $home->save();
        return response()->json($home->id);
    }

    public function removeUselessImages() {
        dd('half done');
        $photoPath = public_path('images');
        if (File::isDirectory($photoPath)) {
            $files = File::files($photoPath);
            $allPhotoNames = array_map(function ($file) {
                return $file->getFilename();
            }, $files);
        } 
    }

    public function editKeyword($id, Request $request){
        $data = $request->all();
        $home = Home::findorFail($id);
        $home->keywords = json_encode($data);
        $home->save();
    }
    public function editYandexLocation($id, Request $request){
        $data = $request->all();
        $this->homeService->addEditYandexLocation($id, $data);
        return true;
    }

    public function activateHomeStatus($id) {
        $home = Home::find($id);
        if($home) {
            $home->update(['status' => Home::STATUS_APPROVED]);
            return response()->json([
                'success' => "Գույքը Ակտիվացված է:"
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'errors' => "Գույքը չի գտնվել"
        ], 404);
    }

    public function archiveHomeStatus($id) {
        $home = Home::find($id);
        if($home) {
            $home->update(['status' => Home::STATUS_ARCHIVED]);
            return response()->json([
                'success' => "Գույքը Ապաակտիվացված է:"
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'errors' => "Գույքը չի գտնվել"
        ], 404);
    }

    public function addReservPhoto($id, Request $request){
        $data = $request->all();
        $home = Home::find($id);
        if($home) {
            $photoName = json_decode($home->photo);
            foreach ($data as $key => $photo) {
                preg_match_all('/\d+/', $key, $matches);
                $fileName = round(microtime(true) * 1000).'.'.$photo->extension();
                $photo->move(public_path('images'), $fileName);
            
                if(is_numeric(strpos($key, 'visible'))) {
                $info = [
                    'name' => $fileName,
                    'visible' => 'true'
                ];
                } else {
                $info = [
                    'name' => $fileName,
                    'visible' => 'false'
                ];
                }
                $photoName[] = $info;
            }
            $home->photo = json_encode($photoName);
            $home->save();
        }
        return true;
    }

    public function addEditReservPhoto($id, Request $request){
        $data = $request->all();
        if(!$data) { 
            $home = Home::find($id);
            if($home) {
                $home->update(['photo' => json_encode([])]);
           }
        } else {
            $home = Home::find($id);
            if($home) {
                $photoName = json_decode($home->photo);
                for ($i = 0; $i < count($data); $i++) {
                    $photoName[] = "";
                }
                $condition = true;
                foreach ($data as $key => $photo) {
                    preg_match_all('/\d+/', $key, $matches);
                    $indexArray = (int) $matches[0][0];
                    if(gettype($photo) == 'string') {
                        if(is_numeric(strpos($key, 'visible'))) 
                        {
                            $info = [
                            'name' => $photo,
                            'visible' => 'true'
                            ];
                        } 
                        else 
                        {
                            $info = [
                            'name' => $photo,
                            'visible' => 'false'
                            ];
                        }
                        $photoName[$indexArray] = $info;
                    } 
                    if(gettype($photo) == 'object') {
                        if($condition){
                            $home->status = auth()->user()->role == "admin" || auth()->user()->role == "moderator" ? Home::STATUS_APPROVED: Home::STATUS_MODERATION;
                            $condition = false;
                        }
                        $fileName = round(microtime(true) * 1000).'.'.$photo->extension();
                        $photo->move(public_path('images'), $fileName);
                    
                        if(is_numeric(strpos($key, 'visible'))) {
                        $info = [
                            'name' => $fileName,
                            'visible' => 'true'
                        ];
                        } else {
                        $info = [
                            'name' => $fileName,
                            'visible' => 'false'
                        ];
                        }
                        $photoName[$indexArray] = $info;
                    }
    
                }
                $home->photo = json_encode($photoName);
                $home->save();
            }
        }
        return true;
    }

    

    public function editMultyPhoto($id, Request $request){
        $data = $request->all();
        if(!$data) { 
            $home = Home::find($id);
            if($home) {
                $home->update(['photo' => json_encode([])]);
           }
        } else {
            $home = Home::find($id);
            if($home) {
                $photoName = array_fill(0, count($data), '');
                logger('beforeEditPhoto', ['photoName' => json_encode($photoName), 'home_id' => $id, 'auth_user'=>auth()->user()->id]);
                $condition = true;
                foreach ($data as $key => $photo) {
                    preg_match_all('/\d+/', $key, $matches);
                    $indexArray = (int) $matches[0][0];
                    if(gettype($photo) == 'string') {
                        if(is_numeric(strpos($key, 'visible'))) 
                        {
                            $info = [
                            'name' => $photo,
                            'visible' => 'true'
                            ];
                        } 
                        else 
                        {
                            $info = [
                            'name' => $photo,
                            'visible' => 'false'
                            ];
                        }
                        $photoName[$indexArray] = $info;
                    } 
                    if(gettype($photo) == 'object') {
                        if($condition){
                            $home->status = auth()->user()->role == "admin" || auth()->user()->role == "moderator" ? Home::STATUS_APPROVED: Home::STATUS_MODERATION;
                            $condition = false;
                        }
                        $fileName = round(microtime(true) * 1000).'.'.$photo->extension();
                        $photo->move(public_path('images'), $fileName);
                    
                        if(is_numeric(strpos($key, 'visible'))) {
                        $info = [
                            'name' => $fileName,
                            'visible' => 'true'
                        ];
                        } else {
                        $info = [
                            'name' => $fileName,
                            'visible' => 'false'
                        ];
                        }
                        $photoName[$indexArray] = $info;
                    }
    
                }
                logger('EditPhoto', ['photoName' => json_encode($photoName), 'home_id' => $id, 'auth_user'=>auth()->user()->id]);
                $home->photo = json_encode($photoName);
                $home->save();
            }
        }
        return true;
    }

    public function editDocumentUpload($id, Request $request){
        $data = $request->all();
        if(!$data) { 
            $home = Home::find($id);
            if($home) {
                $home->update(['file' =>json_encode([])]);
           }
        } else {
            $home = Home::find($id);
            $fileNameArray = [];

            foreach ($data as $key => $file) { 
                if(gettype($file) == 'string') {
                    $fileNameArray[] = $file;
                }
                if(gettype($file) == 'object') { 
                    $fileName = round(microtime(true) * 1000).'.'.$file->extension();
                    $file->move(public_path('files'), $fileName);
                    $fileNameArray[] = $fileName;
                }
                $home->file = json_encode($fileNameArray);
                $home->save();
            }

        }
        return true;
    }

    public function getHome(Request $request) {
        $data = $request->all();

        $allHome = Home::orderByRaw("FIELD(status, 'moderation', 'approved', 'inactive', 'archived'), update_top_at DESC")
        ->select('id', 'home_id', 'am', 'ru', 'en', 'photo', 'file', 'keywords', 'status', 'created_at', 'updated_at')
        ->get() ;

        if($data){
            $allHome =  $this->homeService->getFilteredHomes($allHome, $data);            
        } else {
            foreach ($allHome as $key => $home) {
            $am = json_decode($home->am);
            $ru = json_decode($home->ru);
            $en = json_decode($home->en);

            $searchAllProperty = [];

            if(isset($am[0]->fields[2]->value)){
                array_push($searchAllProperty, $am[0]->fields[2]->value);
                array_push($searchAllProperty, $ru[0]->fields[2]->value);
                array_push($searchAllProperty, $en[0]->fields[2]->value);
            }

            if(isset($am[1]->fields[0]->communityStreet->value)){
                    array_push($searchAllProperty, $am[1]->fields[0]->communityStreet->value);
                    array_push($searchAllProperty, $ru[1]->fields[0]->communityStreet->value);
                    array_push($searchAllProperty, $en[1]->fields[0]->communityStreet->value);
            }

            if(isset($am[9]->fields[1]->value)){ 
                array_push($searchAllProperty, $am[9]->fields[1]->value);
            }

            if(isset( $am[9]->fields[2]->option[1]->value)){ 
                array_push($searchAllProperty,  $am[9]->fields[2]->option[1]->value);
            }
            
            if(isset( $am[9]->fields[2]->option[3]->value)){ 
                array_push($searchAllProperty,  $am[9]->fields[2]->option[3]->value);
            }

            if(isset($am[9]->fields[0]->value)){ 
                array_push($searchAllProperty, $am[9]->fields[0]->value);
            }

            if(isset( $am[9]->fields[2]->option[0]->value)){ 
                array_push($searchAllProperty,  $am[9]->fields[2]->option[0]->value);
            }
            
            if(isset( $am[9]->fields[2]->option[2]->value)){ 
                array_push($searchAllProperty,  $am[9]->fields[2]->option[2]->value);
            }

            if(isset($am[11]->fields[0]->value)){ 
                array_push($searchAllProperty, $am[11]->fields[0]->value);
            }
            if(isset($ru[11]->fields[0]->value)){ 
                array_push($searchAllProperty, $ru[11]->fields[0]->value);
            }
            if(isset($en[11]->fields[0]->value)){ 
                array_push($searchAllProperty, $en[11]->fields[0]->value);
            }
            if(isset($am[11]->fields[1]->value)){ 
                array_push($searchAllProperty, $am[11]->fields[1]->value);
            }
            if(isset($ru[11]->fields[1]->value)){ 
                array_push($searchAllProperty, $ru[11]->fields[1]->value);
            }
            if(isset($en[11]->fields[1]->value)){ 
                array_push($searchAllProperty, $en[11]->fields[1]->value);
            }

            array_push($searchAllProperty, $home->home_id);
            $home->searchAllProperty = $searchAllProperty;
            $home->selectedTransactionType = isset($am[0]->fields[0]->selectedOptionName)?$am[0]->fields[0]->selectedOptionName: '';
            $home->photo = json_decode($home->photo);
            $home->file = json_decode($home->file);
            $home->am = $am;
            $home->ru = $ru;
            $home->en = $en;
            $home->createdAt = Carbon::parse($home->created_at)->format('d/m/Y');
            $home->updatedAt = Carbon::parse($home->updated_at)->format('d/m/Y');
            
            $home->keywords = json_decode($home->keywords);
        }
    }
        return AdminHomeResource::collection($allHome);
     

        // return response()->json($allHome);
    }

    public function multyPhoto($id, Request $request){
        $data = $request->all();
        $home = Home::findorFail($id);
        $photoName = [];
        foreach ($data as $key => $photo) {
          $fileName = round(microtime(true) * 1000).'.'.$photo->extension();
          \Log::info($fileName);
          $photo->move(public_path('images'), $fileName);
         
          if(is_numeric(strpos($key, 'visible'))) {
            $info = [
              'name' => $fileName,
              'visible' => 'true'
            ];
          } else {
            $info = [
              'name' => $fileName,
              'visible' => 'false'
            ];
          }
          $photoName[] = $info;
        }
        $home->photo = json_encode($photoName);
        $home->save();
        \Log::info('multyPhoto'.$id, $photoName);

        return true;
      }

    public function documentUpload($id, Request $request) {
        $data = $request->all();
        $home = Home::findorFail($id);
        $fileNameArray = [];
        foreach ($data as $key => $file) {
            $fileName = round(microtime(true) * 1000).'.'.$file->extension();
            $file->move(public_path('files'), $fileName);
            $fileNameArray[] = $fileName;
          }
          $home->file = json_encode($fileNameArray);
          $home->save();
        \Log::info('documentUpload'.$id, $fileNameArray);

        return true;
    }
  

    public function addKeyword($id, Request $request) {
        $data = $request->all();
        $home = Home::findorFail($id);
        $home->keywords = json_encode($data);
        $home->save();
        \Log::info('addKeyword'.$id, $data);

        return true;
    }

    public function addYandexLocation($id, Request $request) {
        $data = $request->all();
        $this->homeService->addEditYandexLocation($id, $data);
        \Log::info('addYandexLocation'.$id, $data);
        
        return true;
    }

    public function getProperties($id) {
        $home = Home::select('id', 'home_id', 'am', 'photo', 'file', 'keywords', 'status', 'price_history', 'created_at', 'updated_at')
        ->find($id);
    
        if($home) {
            $am = json_decode($home->am);
            $agentId = (int) $am[11]->fields[0]->id;
            $managerId = (int) $am[11]->fields[1]->id;
            $employee = Employe::get();
            Employe::getAgentMangerData($agentId, $managerId, $employee, $am, null, null);
            $home->selectedTransactionType = isset($am[0]->fields[0]->selectedOptionName)?$am[0]->fields[0]->selectedOptionName: '';
            $home->photo = json_decode($home->photo);
            $home->file = json_decode($home->file);
            $home->createdAt = Carbon::parse($home->created_at)->format('d/m/Y');
            $home->updatedAt = Carbon::parse($home->updated_at)->format('d/m/Y');
            $home->keywords = json_decode($home->keywords);
            $home->priceHistory = json_decode($home->price_history);
            $home->am = $am;
            return response()->json($home);

        }
        return response()->json([
            'status' => 'error',
            'errors' => "Home not found"
        ], 422);
    }

    public function updateHomeDate($id) {
        $home = Home::find($id);
        if($home) {
            $dateNow = Carbon::now()->format('Y-m-d H:i:s');
            $home->update(['update_top_at' => $dateNow]);
            return response()->json([
                'success' => "Գույքը թարմացված է"
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'errors' => "Գույքը չի գտնվել"
        ], 404);
    }

    public function addInactiveHome($id, Request $request){
        $data = $request->all();
        $home = Home::find($id);
        if($home) {
            $home->update(['status'=> Home::STATUS_INACTIVE, 'inactive_at' => Carbon::parse($data['date'])->format('Y-m-d')]);
            return response()->json([
                'success' => "Գույքը թարմացված է"
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'errors' => "Գույքը չի գտնվել"
        ], 404);
    }

    public function getEditHome($id)
    {
        $home = $this->homeService->getEditHome($id);

        return response()->json($home);
    }

    
}
