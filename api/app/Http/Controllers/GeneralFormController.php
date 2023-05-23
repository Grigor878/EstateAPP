<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GlobalForm;
use App\Models\ConfigAddress;
use App\Services\GeneralFormService;


class GeneralFormController extends Controller
{
    protected $generalFormService;

    public function __construct(GeneralFormService $generalFormService)
    {
        $this->generalFormService = $generalFormService;
    }
    
    public function addGlobalForm(Request $request) {
        $data = $request->all();
        $formAm = new GlobalForm();
        $formAm->am = json_encode($data);
        $formAm->save();

    }

    public function getAddedFields () {
        $form = GlobalForm::findorFail(1);

        $form->am = json_decode($form->am);
        $form->ru = json_decode($form->ru);
        $form->en = json_decode($form->en);
        return response()->json($form);
    }

    public function createAddress(Request $request) {
      $data = $request->all();
      $newAddress = new ConfigAddress();
      $newAddress->am = $data['am']['value'];
      $newAddress->ru = $data['ru']['value'];
      $newAddress->en = $data['en']['value'];
      $newAddress->addressId = $data['am']['id'];
      $newAddress->communityId = $data['am']['communityId'];
      $newAddress->save();
      $address = ConfigAddress::all();
      return response()->json($address);
    }

    public function getAddress () {
      $address = ConfigAddress::all();
      return response()->json($address);
    }

    public function getAddressForStructure () {
      $address =  \DB::table('config_addresses')
      ->select(\DB::raw('id as addressId, am as value, addressId as id, communityId, am as name'))
      ->get();
      return response()->json($address);
    }

    public function deleteAddress (Request $request) {
      $id = $request->all();
      $item = ConfigAddress::find($id['id']);
      if (!$item) {
          return response()->json(['message' => 'Address not found'], 404);
      }
      $item->delete();
      $address = ConfigAddress::all();
      return response()->json($address);
    }
    

    public function getFormStructure() {

        // $structure = $this->generalFormService->getAmDistrict();
        // $lalalal = GlobalForm::LALALA;
        // dd(111,$structure);
        $form = GlobalForm::findorFail(1);
        // $obj = [
        //   'am' => [
        //     'name'=> "announcement",
        //     'id' => 'announcement_id1',
        //     'val' => 'Ավելացրած'
        //   ]
        // ] ;
        $form->am = json_decode($form->am);
        $form->ru = json_decode($form->ru);
        $form->en = json_decode($form->en);

      return json_decode(GlobalForm::findorFail(1)->am);
    }

    public function documentUpload(Request $request) {
      $data = $request->all();
      dd($data);
    }

    public function multyPhoto(Request $request){
      $data = $request->all();
      $photoName = [];
      foreach ($data as $key => $photo) {
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
      return response()->json($photoName);
    }

    public function addGlobalFormField(Request $request) {
        $data = $request->all();
       $this->generalFormService->addGeneralField($data);
        return json_decode(GlobalForm::findorFail(1)->am);
    }

    public function removeGlobalFormField(Request $request) {
        $data = $request->all();
        $this->generalFormService->removeGeneralField($data);
        return json_decode(GlobalForm::findorFail(1)->am);
    }

    public function getAllStructure() {
      // $form = GlobalForm::findorFail(1);
      // $form->am = json_decode($form->am);
      // $form->ru = json_decode($form->ru);
      // $form->en = json_decode($form->en);
      // return response()->json($form);
      $str = [
        [
          'name' => "announcement",
          'title'=> "Հայտարարություն",
          'added'=> [],
          "fields" => [
            [
              "key" => "transactionType",
              "title" => "ԳՈՐԾԱՐՔԻ ՏԵՍԱԿ*",
              "type" => "select",
              "style" => "306px",
              "option" => [
                [
                  "id"=> 1,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => "",
                  "getOptionName" => ""
                ],
                [
                  "id"=> 2,
                  "name"=> "Վաճառք",
                  "value" => "Վաճառք",
                  "getOptionName" => "sale"
                ],
                [
                  "id"=> 3,
                  "name"=> "Վարձակալություն",
                  "value" => "Վարձակալություն",
                  "getOptionName" => "rent"
                ]
              ]
            ],
            [
              "key" => "propertyType",
              "title" => "ԳՈՒՅՔԻ ՏԵՍԱԿ*",
              "type" => "select",
              "style" => "306px",
              "option" => [
                [
                  "id"=> 1,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => "",
                  "getOptionName" => ""
                ],
                [
                  "id"=> 2,
                  "name"=> "Բնակարան",
                  "value" => "Բնակարան",
                  "getOptionName" => "house"
                ],
                [
                  "id"=> 3,
                  "name"=> "Առանձնատուն",
                  "value" => "Առանձնատուն",
                  "getOptionName" => "privateHouse"
                ],
                [
                  "id"=> 4,
                  "name"=> "Կոմերցիոն (առանձնատուն)",
                  "value" => "Կոմերցիոն (առանձնատուն)",
                  "getOptionName" => "commercialHouse"
                ],
                [
                  "id"=> 5,
                  "name"=> "Կոմերցիոն (բնակարան)",
                  "value" => "Կոմերցիոն (բնակարան)",
                  "getOptionName" => "commercialApartment"
                ],
              ]
            ],
            [
              "key" => "announcementTitle",
              "title" => "ՀԱՅՏԱՐԱՐՈՒԹՅԱՆ ՎԵՐՆԱԳԻՐ*",
              "type" => "text",
              "style" => "629px",
              "option" => []
            ],
            [
              "key" => "announcementDesc",
              "title" => "ՀԱՅՏԱՐԱՐՈՒԹՅԱՆ ՆԿԱՐԱԳՐՈՒԹՅՈՒՆ*",
              "type" => "text",
              "style" => "629px",
              "option" => []
            ],
           [
            "key" => "announcementType",
            "title" => "ՀԱՅՏԱՐԱՐՈՒԹՅԱՆ ՏԵՍԱԿ*",
            "type" => "select",
            "style" => "306px",
            "option" => [
              [
                "id"=> 1,
                "name"=> "Ընտրեք տեսակը",
                "value" => "",
                "getOptionName" => ""
              ],
              [
                "id"=> 2,
                "name"=> "Հասարակ",
                "value" => "Հասարակ",
                "getOptionName" => "simple"
              ],
              [
                "id"=> 3,
                "name"=> "Տոպ",
                "value" => "Տոպ",
                "getOptionName" => "top"
              ],
              [
                "id"=> 4,
                "name"=> "Շտապ",
                "value" => "Շտապ",
                "getOptionName" => "urgent"
              ],
            ]
          ],
          ]
        ],
        [
          'name' => "location",
          'title'=> "Գտնվելու Վայրը - Երևան",
          'added'=> [],
          "fields" => [
            [
              "key" => "community",
              "title" => "Համայնք*",
              "type" => "select",
              "style" => "629px",
              "option" => [
                [
                  "id"=> 1,
                  "name"=> "Ընտրեք",
                  "value" => "",
                  "getOptionName" => ""
                ],
                [
                  "id"=> 2,
                  "name"=> "Աջափնյակ",
                  "value" => "Աջափնյակ",
                  "getOptionName" => "ajapnyak"
                ],
                [
                  "id"=> 3,
                  "name"=> "Արաբկիր",
                  "value" => "Արաբկիր",
                  "getOptionName" => "arabkir"
                ],
                [
                  "id"=> 4,
                  "name"=> "Ավան",
                  "value" => "Ավան",
                  "getOptionName" => "avan"
                ],
                [
                  "id"=> 5,
                  "name"=> "Դավթաշեն",
                  "value" => "Դավթաշեն",
                  "getOptionName" => "davtashen"
                ],
                [
                  "id"=> 6,
                  "name"=> "Էրեբունի",
                  "value" => "Էրեբունի",
                  "getOptionName" => "erebuni"
                ],
                [
                  "id"=> 7,
                  "name"=> "Քանաքեռ-Զեյթուն",
                  "value" => "Քանաքեռ-Զեյթուն",
                  "getOptionName" => "zeytun"
                ],
                [
                  "id"=> 8,
                  "name"=> "Կենտրոն",
                  "value" => "Կենտրոն",
                  "getOptionName" => "kentron"
                ],
                [
                  "id"=> 9,
                  "name"=> "Մալաթիա-Սեբաստիա",
                  "value" => "Մալաթիա-Սեբաստիա",
                  "getOptionName" => "malatia"
                ],
                [
                  "id"=> 10,
                  "name"=> "Նորք-Մարաշ",
                  "value" => "Նորք-Մարաշ",
                  "getOptionName" => "norqMarash"
                ],
                [
                  "id"=> 11,
                  "name"=> "Նոր Նորք",
                  "value" => "Նոր Նորք",
                  "getOptionName" => "norNorq"
                ],
                [
                  "id"=> 12,
                  "name"=> "Նուբարաշեն",
                  "value" => "Նուբարաշեն",
                  "getOptionName" => "nubarashen"
                ],
                [
                  "id"=> 13,
                  "name"=> "Շենգավիթ",
                  "value" => "Շենգավիթ",
                  "getOptionName" => "shengavit"
                ],
                [
                  "id"=> 14,
                  "name"=> "Վահագնի թաղամաս",
                  "value" => "Վահագնի թաղամաս",
                  "getOptionName" => "vahagni"
                ],
                [
                  "id"=> 15,
                  "name"=> "Այլ",
                  "value" => "Այլ",
                  "getOptionName" => "other"
                ]
              ]
            ],
            [
              "key" => "street",
              "title" => "Փողոց*",
              "type" => "select",
              "style" => "283px",
              "option" => []
            ],
            [
              "key" => "building",
              "title" => "Շենք*",
              "type" => "inputNumber",
              "style" => "100px",
              "option" => []
            ],
            [
              "key" => "entrance",
              "title" => "Մուտք*",
              "type" => "inputNumber",
              "style" => "100px",
              "option" => []
            ],
            [
              "key" => "apartment",
              "title" => "Բնակարան*",
              "type" => "inputNumber",
              "style" => "100px",
              "option" => []
            ],
            [
              "key" => "map",
              "title" => "MAP PIN*",
              "type" => "map",
              "style" => "631px",
              "option" => []
            ],
            [
              "key" => "realAddress",
              "title" => "Իրական հասցե*",
              "type" => "inputText",
              "style" => "629px",
              "placeholder" => "Հասցե",
              "option" => []
            ],
          ]
        ],
        [
          'name' => "price",
          'title'=> "Գինը",
          'added'=> [
            [
              "key" => "priceAdded",
              "title" => "Ավել Գինը*",
              "type" => "inputText",
              "style" => "width:80%",
              "option" => []
            ],
          ],
          "fields" => [
            [
              "key" => "totalPrice",
              "title" => "Ընդհանուր գինը*",
              "type" => "inputNumberSymbol",
              "style" => "202px",
              "option" => [
                [
                  "id"=> "priceUsd",
                  "name"=> "Գինը դոլարով",
                  "symbol"=>'usd',
                  "value" => ""
                ],
                [
                  "id"=> "priceAmd",
                  "name"=> "Գինը դրամով",
                  "symbol"=>'amd',
                  "value" => ""
                ],
                [
                  "id"=> "priceRub",
                  "name"=> "Գինը ռուբլիով",
                  "symbol"=>'rub',
                  "value" => ""
                ]
              ]
            ],
            [
              "key" => "priceNegotiable",
              "title" => "Գինը պայմանագրային",
              "type" => "checkbox",
              "style" => "",
              "option" => [
                "status" => false
               ]
            ],
            [
              "key" => "sqmPrice",
              "title" => "Գինը 1 քմ*",
              "type" => "inputNumberSymbol",
              "style" => "202px",
              "option" => [
                [
                  "id"=> "sqmPriceUsd",
                  "name"=> "Գինը դոլարով",
                  "symbol"=>'usd',
                  "value" => ""
                ],
                [
                  "id"=> "sqmPriceAmd",
                  "name"=> "Գինը դրամով",
                  "symbol"=>'amd',
                  "value" => ""
                ],
                [
                  "id"=> "sqmPriceRub",
                  "name"=> "Գինը ռուբլիով",
                  "symbol"=>'rub',
                  "value" => ""
                ]
              ]
            ],
            [
              "key" => "downPayment",
              "title" => "Կանխավճարի չափ*",
              "type" => "inputNumberSymbol",
              "style" => "202px",
              "option" => [
                [
                  "id"=> "downPaymentUsd",
                  "name"=> "Գինը դոլարով",
                  "symbol"=>'usd',
                  "value" => ""
                ],
                [
                  "id"=> "downPaymentAmd",
                  "name"=> "Գինը դրամով",
                  "symbol"=>'amd',
                  "value" => ""
                ],
                [
                  "id"=> "downPaymentRub",
                  "name"=> "Գինը ռուբլիով",
                  "symbol"=>'rub',
                  "value" => ""
                ]
              ]
            ],
            [
              "key" => "paymentMethod",
              "title" => "Վճարման կարգը*",
              "type" => "multiselect",
              "style" => "306px",
              "option" => [
              [
                "label"=> "Բանկային փոխանցում",
                "value" => "bankTransfer"
              ],
              [
                "label"=> "Հիպոթեքային վարկ",
                "value" => "mortgageLoan"
              ],
              [
                "label"=> "Այլ",
                "value" => "other"
              ],
              ]
            ],
            [
            "key" => "preferredBank",
            "title" => "Նախընտրած բանկը*",
            "type" => "multiselect",
            "style" => "306px",
            "option" => [
              [
                "label"=> "Ամերիա բանկ",
                "value" => "ameriaBank"
              ],
              [
                "label"=> "Էվոկաբանկ",
                "value" => "evocaBank"
              ],
              [
                "label"=> "Ինեկոբանկ",
                "value" => "inecoBank"
              ],
              [
                "label"=> "ԱյԴի բանկ",
                "value" => "idBank"
              ],
              [
                "label"=> "Ակբա բանկ",
                "value" => "acbaBank"
              ],
              [
                "label"=> "Մելլաթ բանկ",
                "value" => "mellatBank"
              ],
              [
                "label"=> "ՀայԷկոնոմ բանկ",
                "value" => "armeconomBank"
              ],
              [
                "label"=> "HSBC բանկ",
                "value" => "HSBC"
              ],
              [
                "label"=> "Յունիբանկ",
                "value" => "uniBank"
              ],
              [
                "label"=> "Հայբիզնեսբանկ",
                "value" => "armbusinessMank"
              ],
              [
                "label"=> "Կոնվերս բանկ",
                "value" => "converseBank"
              ],
              [
                "label"=> "Արարատ բանկ",
                "value" => "araratBank"
              ],
              [
                "label"=> "Ֆասթ բանկ",
                "value" => "fastBank"
              ],
              [
                "label"=> "Արմսվիսբանկ",
                "value" => "armswissBank"
              ],
              [
                "label"=> "Արցախ բանկ",
                "value" => "artsakh"
              ],
              [
                "label"=> "Բիբլոս Բանկ Արմենիա",
                "value" => "biblos"
              ],
              [
                "label"=> "Արդշինբանկ",
                "value" => "ardshin"
              ],
              [
                "label"=> "ՎՏԲ-Հայաստան բանկ",
                "value" => "vtb"
              ],
              [
                "label"=> "Այլ",
                "value" => "other"
              ],
            ]
            ],
          ]
        ],
        [
          'name' => "houseDescription",
          'title'=> "Տան Նկարագիր",
          'added'=> [],
          "fields" => [
            [
              "key" => "surface",
              "title" => "Մակերես*",
              "type" => "inputNumberSymbol",
              "style" => "306px",
              "option" => [
                [
                  "id"=> "surface",
                  "name"=> "Նշեք մակերեսը",
                  "symbol"=>'մ.ք.',
                  "value" => ""
                ],
              ]
            ],
            [
              "key" => "ceilingHeight",
              "title" => "Առաստաղի բարձրությունը*",
              "type" => "inputNumberSymbol",
              "style" => "306px",
              "option" => [
                [
                  "id"=> "ceilingHeight",
                  "name"=> "Նշեք բարձրությունը ",
                  "symbol"=>'մետր',
                  "value" => ""
                ],
              ]
            ],
            [
              "key" => "numberOfRooms",
              "title" => "Սենյակների քանակ*",
              "type" => "numSelect",
              "style" => "306px",
              "option" => [
                [
                  "id"=> "numberOfRooms",
                  "value" => "1"
                ],
                [
                  "id"=> "numberOfRooms",
                  "value" => "2"
                ],
                [
                  "id"=> "numberOfRooms",
                  "value" => "3"
                ],
                [
                  "id"=> "numberOfRooms",
                  "value" => "4"
                ],
                [
                  "id"=> "numberOfRooms",
                  "value" => "5"
                ],
                [
                  "id"=> "numberOfRooms",
                  "value" => "6"
                ],
                [
                  "id"=> "numberOfRooms",
                  "value" => "7+"
                ],
              ]
            ],
            [
              "key" => "numberOfBedrooms",
              "title" => "Ննջասենյակի քանակ*",
              "type" => "numSelect",
              "style" => "306px",
              "option" => [
                [
                  "id"=> "numberOfBedrooms",
                  "value" => "1"
                ],
                [
                  "id"=> "numberOfBedrooms",
                  "value" => "2"
                ],
                [
                  "id"=> "numberOfBedrooms",
                  "value" => "3"
                ],
                [
                  "id"=> "numberOfBedrooms",
                  "value" => "4"
                ],
                [
                  "id"=> "numberOfBedrooms",
                  "value" => "5"
                ],
                [
                  "id"=> "numberOfBedrooms",
                  "value" => "6"
                ],
                [
                  "id"=> "numberOfBedrooms",
                  "value" => "7+"
                ],
              ]
            ],
            [
              "key" => "numberOfBathrooms",
              "title" => "Սահանգույցների քանակ*",
              "type" => "numSelect",
              "style" => "306px",
              "option" => [
                [
                  "id"=> "numberOfBathrooms",
                  "value" => "1"
                ],
                [
                  "id"=> "numberOfBathrooms",
                  "value" => "2"
                ],
                [
                  "id"=> "numberOfBathrooms",
                  "value" => "3"
                ],
                [
                  "id"=> "numberOfBathrooms",
                  "value" => "4"
                ],
                [
                  "id"=> "numberOfBathrooms",
                  "value" => "5+"
                ],
              ]
            ],
            [
              "key" => "numberOpenBalconies",
              "title" => "Բաց պատշգամբների քանակ*",
              "type" => "numSelect",
              "style" => "306px",
              "option" => [
                [
                  "id"=> "numberOpenBalconies",
                  "value" => "0"
                ],
                [
                  "id"=> "numberOpenBalconies",
                  "value" => "1"
                ],
                [
                  "id"=> "numberOpenBalconies",
                  "value" => "2"
                ],
                [
                  "id"=> "numberOpenBalconies",
                  "value" => "3"
                ],
                [
                  "id"=> "numberOpenBalconies",
                  "value" => "4"
                ],
                [
                  "id"=> "numberOpenBalconies",
                  "value" => "5"
                ],
                [
                  "id"=> "numberOpenBalconies",
                  "value" => "6"
                ],
              ]
            ],
            [
              "key" => "numberCloseBalconies",
              "title" => "Փակ պատշգամբների քանակ*",
              "type" => "numSelect",
              "style" => "629px",
              "option" => [
                [
                  "id"=> "numberCloseBalconies",
                  "value" => "0"
                ],
                [
                  "id"=> "numberCloseBalconies",
                  "value" => "1"
                ],
                [
                  "id"=> "numberCloseBalconies",
                  "value" => "2"
                ],
                [
                  "id"=> "numberCloseBalconies",
                  "value" => "3"
                ],
                [
                  "id"=> "numberCloseBalconies",
                  "value" => "4"
                ],
                [
                  "id"=> "numberCloseBalconies",
                  "value" => "5"
                ],
                [
                  "id"=> "numberCloseBalconies",
                  "value" => "6"
                ],
              ]
            ],
            [
              "key" => "floor",
              "title" => "Հարկը*",
              "type" => "inputNumber",
              "style" => "306px",
              "option" => []
            ],
            [
              "key" => "houseCondition",
              "title" => "Տան վիճակ*",
              "type" => "select",
              "style" => "306px",
              "option" => [
                [
                  "id"=> 0,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => "",
                  "getOptionName" => ""
                ],
                [
                  "id"=> 1,
                  "name"=> "Պետական վիճակ",
                  "value" => "Պետական վիճակ",
                  "getOptionName" => "stateCondition"
                ],
                [
                  "id"=> 2,
                  "name"=> "Լավ",
                  "value" => "Լավ",
                  "getOptionName" => "good"
                ],
                [
                  "id"=> 3,
                  "name"=> "Զրոյական",
                  "value" => "Զրոյական",
                  "getOptionName" => "zero"
                ],
                [
                  "id"=> 4,
                  "name"=> "Վերանորոգված",
                  "value" => "Վերանորոգված",
                  "getOptionName" => "renovated"
                ],
              ]
            ],
            [
              "key" => "parking",
              "title" => "Ավտոկայանատեղի*",
              "type" => "select",
              "style" => "306px",
              "option" => [
                [
                  "id"=> 0,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => "",
                  "getOptionName" => ""
                ],
                [
                  "id"=> 1,
                  "name"=> "Քարե",
                  "value" => "Քարե",
                  "getOptionName" => "stoned"
                ],
                [
                  "id"=> 2,
                  "name"=> "Ստորգետնյա",
                  "value" => "Ստորգետնյա",
                  "getOptionName" => "underground"
                ],
                [
                  "id"=> 3,
                  "name"=> "Բաց ավտոկայանատեղի",
                  "value" => "Բաց ավտոկայանատեղի",
                  "getOptionName" => "openParking"
                ],
                [
                  "id"=> 4,
                  "name"=> "Ազատ տարածություն",
                  "value" => "Ազատ տարածություն",
                  "getOptionName" => "freeSpace"
                ],
              ]
            ],
            [
              "key" => "kitchenType",
              "title" => "Խոհանոցի տիպ*",
              "type" => "select",
              "style" => "306px",
              "option" => [
                [
                  "id"=> 0,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => "",
                  "getOptionName" => ""
                ],
                [
                  "id"=> 1,
                  "name"=> "Առանձին",
                  "value" => "Առանձին",
                  "getOptionName" => "separately"
                ],
                [
                  "id"=> 2,
                  "name"=> "Ստուդիո",
                  "value" => "Ստուդիո",
                  "getOptionName" => "studio"
                ],
                [
                  "id"=> 3,
                  "name"=> "Նախագծված չէ",
                  "value" => "Նախագծված չէ",
                  "getOptionName" => "notDesigned"
                ],
              ]
            ],
          ]
        ],
        [
          'name' => "buildingDescription",
          'title'=> "Շինության նկարագիր",
          'added'=> [],
          "fields" => [
           [
            "key" => "buildingType",
            "title" => "Շինության տիպ*",
            "type" => "select",
            "style" => "306px",
            "option" => [
                [
                  "id"=> 0,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => "",
                  "getOptionName" => ""
                ],
                [
                  "id"=> 1,
                  "name"=> "Մոնոլիտ",
                  "value" => "Մոնոլիտ",
                  "getOptionName" => "monolith"
                ],
                [
                  "id"=> 2,
                  "name"=> "Քարե",
                  "value" => "Քարե",
                  "getOptionName" => "stone"
                ],
                [
                  "id"=> 3,
                  "name"=> "Պանելային",
                  "value" => "Պանելային",
                  "getOptionName" => "panel"
                ],
                [
                  "id"=> 4,
                  "name"=> "Այլ",
                  "value" => "Այլ",
                  "getOptionName" => "other"
                ],
            ]
           ],
           [
            "key" => "statement",
            "title" => "ՀԱՐԿԱՅՆՈՒԹՅՈՒՆ*",
            "placeholder" => "Ex.",
            "type" => "inputText",
            "style" => "306px",
            "option" => [],
           ],
           [
            "key" => "newBuilt",
            "type" => "checkbox",
            "title" => "Նորակառույց",
            "style" => "612px",
           ],
           [
            "key" => "buildingConstructionYear",
            "title" => "Կառուցման տարին*",
            "type" => "inputNumber",
            "style" => "306px",
            "option" => [],
           ],
           [
            "key" => "orentation",
            "title" => "կողմնորոշումը*",
            "type" => "multiselect",
            "style" => "306px",
            "option" => [
              [
                "label"=> "Հյուսիսային",
                "value" => "north"
              ],
              [
                "label"=> "Հարավային",
                "value" => "south"
              ],
              [
                "label"=> "Արևելյան",
                "value" => "east"
              ],
              [
                "label"=> "Արևմտյան",
                "value" => "west"
              ],
              [
                "label"=> "Հարավ-Արևելյան",
                "value" => "southEast"
              ],
              [
                "label"=> "Հարավ-Արևմտյան",
                "value" => "southWest"
              ],
              [
                "label"=> "Հյուսիս-Արևելյան",
                "value" => "northEast"
              ],
              [
                "label"=> "Հյուսիս-Արևմտյան",
                "value" => "northWest"
              ],
            ]
           ],
           [
            "key" => "propertyTax",
            "title" => "Տարեկան գույքահարկ*",
            "type" => "inputNumberSymbol",
            "style" => "202px",
            "option" => [
              [
                "id"=> "propertyTaxUsd",
                "name"=> "Գինը դոլարով",
                "symbol"=>'usd',
                "value" => ""
              ],
              [
                "id"=> "propertyTaxAmd",
                "name"=> "Գինը դրամով",
                "symbol"=>'amd',
                "value" => ""
              ],
              [
                "id"=> "propertyTaxRub",
                "name"=> "Գինը ռուբլիով",
                "symbol"=>'rub',
                "value" => ""
              ]
            ]
           ],
           [
            "key" => "monthlyFee",
            "title" => "ԱմսԱկան Սպասարկման Վճար*",
            "type" => "inputNumberSymbol",
            "style" => "202px",
            "option" => [
              [
                "id"=> "monthlyFeeUsd",
                "name"=> "Գինը դոլարով",
                "symbol"=>'usd',
                "value" => ""
              ],
              [
                "id"=> "monthlyFeeAmd",
                "name"=> "Գինը դրամով",
                "symbol"=>'amd',
                "value" => ""
              ],
              [
                "id"=> "monthlyFeeRub",
                "name"=> "Գինը ռուբլիով",
                "symbol"=>'rub',
                "value" => ""
              ]
            ]
           ],
          ]
        ],
        [
          'name' => "mainFacility",
          'title'=> "Կոմունալ հարմարություններ",
          'added'=> [],
          "fields" => [
            [
              "key" => "individualHeatingSystem",
              "title" => "Անհատական ջեռուցման համակարգ",
              "type" => "checkbox",
              "style" => "371px",
            ],
            [
              "key" => "electricity",
              "title" => "Էլեկտրոէներգիա",
              "type" => "checkbox",
            ],
            [
              "key" => "centralizedHeatingSystem",
              "title" => "Կենտրոնացված ջեռուցման համակարգ",
              "type" => "checkbox",
              "style" => "371px",
            ],
            [
              "key" => "gas",
              "title" => "Գազ",
              "type" => "checkbox",
              "style" => "157px",
            ],
            [
              "key" => "airConditioner",
              "title" => "Օդորակիչ",
              "type" => "checkbox",
              "style" => "371px",
            ],
            [
              "key" => "centralizedCoolingSystem",
              "title" => "Կենտրոնացած հովացման համակարգ",
              "type" => "checkbox",
            ],
          ],
        ],
        [
          'name' => "otherFacility",
          'title'=> "Այլ հարմարություններ",
          'added'=> [],
          "fields" => [
            [
              "key" => "furniture",
              "title" => "Կահույք",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "storageRoom",
              "title" => "Խորդանոց",
              "type" => "checkbox",
            ],
            [
              "key" => "technics",
              "title" => "Տեխնիկա",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "wardrobe",
              "title" => "Զգեստապահարան",
              "type" => "checkbox",
            ],
            [
              "key" => "elevator",
              "title" => "Վերելակ",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "laundryRoom",
              "title" => "Լվացքատուն",
              "type" => "checkbox",
            ],
            [
              "key" => "closedEconomyBalcony",
              "title" => "Փակ տնտեսական պատշգամբ",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "park",
              "title" => "Զբոսայգի",
              "type" => "checkbox",
            ],
            [
              "key" => "europeWindow",
              "title" => "Եվրոպատուհան",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "firstLine",
              "title" => "Առաջին գիծ",
              "type" => "checkbox",
            ],
            [
              "key" => "laminate",
              "title" => "Լամինատ",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "courtyardBuilding",
              "title" => "Միջբակային շենք",
              "type" => "checkbox",
            ],
            [
              "key" => "parquetFloor",
              "title" => "Մանրահատակ",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "nearStop",
              "title" => "Կանգառի մոտ",
              "type" => "checkbox",
            ],
            [
              "key" => "tiled",
              "title" => "Սալիկապատված",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "sunnySide",
              "title" => "Արևկողմ",
              "type" => "checkbox",
            ],
            [
              "key" => "presgranite",
              "title" => "Պռեսգրանիտ",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "heatedFloor",
              "title" => "Տաքացվող հատակ",
              "type" => "checkbox",
            ],
            [
              "key" => "beautifulView",
              "title" => "Գեղեցիկ տեսարան",
              "type" => "checkbox",
              "style" => "309px",
            ],
            
            [
              "key" => "gate",
              "title" => "Դարպաս",
              "type" => "checkbox",
            ],
            [
              "key" => "securitySystem",
              "title" => "Անվտանգության համակարգ",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "fenced",
              "title" => "Պարսպապատ",
              "type" => "checkbox",
            ],
            [
              "key" => "playground",
              "title" => "Խաղահրապարակ",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "twoWayEntry",
              "title" => "Երկկողմանի մուտք",
              "type" => "checkbox",
            ],
            [
              "key" => "bilateralPosition",
              "title" => "Երկկողմանի դիրք",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "ironDoor",
              "title" => "Երկաթյա դուռ",
              "type" => "checkbox",
            ],
            [
              "key" => "sauna",
              "title" => "Շոգեբաղնիք",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "loggia",
              "title" => "Լոջա",
              "type" => "checkbox",
            ],
            [
              "key" => "pool",
              "title" => "Լողավազան",
              "type" => "checkbox",
              "style" => "309px",
            ],
            [
              "key" => "floor",
              "title" => "Հատակ*",
              "type" => "multiselect",
              "style" => "306px",
              "option" => [
                [
                  "label"=> "Լամինատ",
                  "value" => "laminate"
                ],
                [
                  "label"=> "Մանրահատակ",
                  "value" => "parquetFloor"
                ],
                [
                  "label"=> "Սալիկ",
                  "value" => "tile"
                ],
                [
                  "label"=> "Բետոն",
                  "value" => "concrete"
                ],
                [
                  "label"=> "Այլ",
                  "value" => "other"
                ],
              ],
            ],
            [
              "key" => "roof",
              "title" => "Առաստաղ*",
              "type" => "multiselect",
              "style" => "306px",
              "option" => [
                [
                  "label"=> "Կախովի",
                  "value" => "suspended"
                ],
                [
                  "label"=> "Ձգվող",
                  "value" => "tensile"
                ],
                [
                  "label"=> "Բետոն",
                  "value" => "concrete"
                ],
              ],
            ],
            [
              "key" => "cover",
              "title" => "Ծածկեր*",
              "type" => "multiselect",
              "style" => "306px",
              "option" => [
                [
                  "label"=> "Բետոն",
                  "value" => "concrete"
                ],
                [
                  "label"=> "Բաղդադ",
                  "value" => "baghdad"
                ],
                [
                  "label"=> "Պանել",
                  "value" => "panel"
                ],
              ],
            ],
          ],
        ],
        [
          'name' => "media",
          'title'=> "Մեդիա",
          'added'=> [],
          "fields" => [
            [
              "key" => "uploadedImgs",
              "title" => "",
              "type" => "imgsUpload",
              "style" => "639px",
              "option" => [],
            ],
            [
              "key" => "video",
              "title" => "Վիդեոյի հղում*",
              "placeholder" => "Տեղադրեք հղումը",
              "type" => "inputText",
              "style" => "639px",
              "option" => [],
            ]
          ],
        ],
        [
          'name' => "keywords",
          'title'=> "Բանալի Բառեր",
          'added'=> [],
          "fields" => [
            [
              "key" => "chooseWords",
              "title" => "Ընտրել բառեր*",
              "type" => "keyword",
              "style" => "631px",
              "option" => [],
            ],
          ],
        ],
        [
          'name' => "juridical",
          'title'=> "Իրավաբանական",
          'added'=> [],
          "fields" => [
            [
              "key" => "owner",
              "title" => "Սեփականատեր 1*",
              "placeholder" => "Գրեք սեփականատիրոջ անունը",
              "type" => "inputText",
              "style" => "412px",
              "option" => [],
            ],
            [
              "key" => "ownerTel",
              "title" => "Սեփականատիրոջ Հեռախոսահամար*",
              "type" => "inputNumber",
              "style" => "412px",
              "option" => [],
            ],
            [
              "key" => "addOwner",
              "title" => "Ավելացնել սեփականատեր",
              "type" => "addField",
              "style" => "217px",
              "option" => [
                [
                  "key" => "owner",
                  "title" => "Սեփականատեր 2*",
                  "placeholder" => "Գրեք սեփականատիրոջ անունը",
                  "type" => "inputText",
                  "style" => "412px",
                ],
                [
                  "key" => "ownerTel",
                  "title" => "Սեփականատիրոջ Հեռախոսահամար*",
                  "type" => "inputNumber",
                  "style" => "412px",
                  "option" => [],
                ],
              ],
            ],
          ],
        ],
        [
          'name' => "additionalInfo",
          'title'=> "Լրացուցիչ Ինֆորմացիա",
          'added'=> [],
          "fields" => [
            [
              "key" => "likelyInfo",
              "title" => "Գրեք նախընտրած ինֆորմացիան*",
              "type" => "inputText",
              "style" => "412px",
              "height" => "80px",
              "option" => [],
            ],
            [
              "key" => "uploadFiles",
              "title" => "Կցել Փաստաթուղթ",
              "type" => "uploadFile",
              "style" => "217px",
              "option" => [],
            ],
          ],
        ],
        [
          'name' => "specialists",
          'title'=> "Կից Մասնագետներ",
          'added'=> [],
          "fields" => [
            [
              "key" => "agent",
              "title" => "Գործակալ*",
              "type" => "select",
              "style" => "412px",
              "option" => [],
            ],
            [
              "key" => "meneger",
              "title" => "Մենեջեր*",
              "type" => "select",
              "style" => "412px",
              "option" => [],
            ],
          ],
        ],
      ] ;
     
     return response()->json($str);
  }

  public function addNow() {


        $form = GlobalForm::findorFail(1);
        $form->am = json_encode(  [
          [
            'name' => "announcement",
            'title'=> "Հայտարարություն",
            'added'=> [],
            "fields" => [
              [
                "key" => "transactionType",
                "title" => "ԳՈՐԾԱՐՔԻ ՏԵՍԱԿ*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 1,
                    "name"=> "Ընտրեք տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Վաճառք",
                    "value" => "Վաճառք"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Վարձակալություն",
                    "value" => "Վարձակալություն"
                  ]
                ]
              ],
              [
                "key" => "propertyType",
                "title" => "ԳՈՒՅՔԻ ՏԵՍԱԿ*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 1,
                    "name"=> "Ընտրեք տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Բնակարան",
                    "value" => "Բնակարան"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Առանձնատուն",
                    "value" => "Առանձնատուն"
                  ],
                  [
                    "id"=> 4,
                    "name"=> "Կոմերցիոն (առանձնատուն)",
                    "value" => "Կոմերցիոն (առանձնատուն)"
                  ],
                  [
                    "id"=> 5,
                    "name"=> "Կոմերցիոն (բնակարան)",
                    "value" => "Կոմերցիոն (բնակարան)"
                  ],
                ]
              ],
              [
                "key" => "announcementTitle",
                "title" => "ՀԱՅՏԱՐԱՐՈՒԹՅԱՆ ՎԵՐՆԱԳԻՐ*",
                "type" => "text",
                "value" => "",
                "style" => "629px",
                "option" => []
              ],
              [
                "key" => "announcementDesc",
                "title" => "ՀԱՅՏԱՐԱՐՈՒԹՅԱՆ ՆԿԱՐԱԳՐՈՒԹՅՈՒՆ*",
                "type" => "text",
                "style" => "629px",
                "value" => "",
                "option" => []
              ],
             [
              "key" => "announcementType",
              "title" => "ՀԱՅՏԱՐԱՐՈՒԹՅԱՆ ՏԵՍԱԿ*",
              "type" => "select",
              "style" => "306px",
              "value" => "",
              "option" => [
                [
                  "id"=> 1,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => ""
                ],
                [
                  "id"=> 2,
                  "name"=> "Հասարակ",
                  "value" => "Հասարակ"
                ],
                [
                  "id"=> 3,
                  "name"=> "Տոպ",
                  "value" => "Տոպ"
                ],
                [
                  "id"=> 4,
                  "name"=> "Շտապ",
                  "value" => "Շտապ"
                ],
              ]
            ],
            ]
          ],
          [
            'name' => "location",
            'title'=> "Գտնվելու Վայրը - Երևան",
            'added'=> [],
            "fields" => [
              [
                "key" => "community",
                "title" => "Համայնք*",
                "type" => "select",
                "style" => "629px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 1,
                    "name"=> "Ընտրեք",
                    "value" => ""
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Աջափնյակ",
                    "value" => "Աջափնյակ"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Արաբկիր",
                    "value" => "Արաբկիր"
                  ],
                  [
                    "id"=> 4,
                    "name"=> "Ավան",
                    "value" => "Ավան"
                  ],
                  [
                    "id"=> 5,
                    "name"=> "Դավթաշեն",
                    "value" => "Դավթաշեն"
                  ],
                  [
                    "id"=> 6,
                    "name"=> "Էրեբունի",
                    "value" => "Էրեբունի"
                  ],
                  [
                    "id"=> 7,
                    "name"=> "Քանաքեռ-Զեյթուն",
                    "value" => "Քանաքեռ-Զեյթուն"
                  ],
                  [
                    "id"=> 8,
                    "name"=> "Կենտրոն",
                    "value" => "Կենտրոն"
                  ],
                  [
                    "id"=> 9,
                    "name"=> "Մալաթիա-Սեբաստիա",
                    "value" => "Մալաթիա-Սեբաստիա"
                  ],
                  [
                    "id"=> 10,
                    "name"=> "Նորք-Մարաշ",
                    "value" => "Նորք-Մարաշ"
                  ],
                  [
                    "id"=> 11,
                    "name"=> "Նոր Նորք",
                    "value" => "Նոր Նորք"
                  ],
                  [
                    "id"=> 12,
                    "name"=> "Նուբարաշեն",
                    "value" => "Նուբարաշեն"
                  ],
                  [
                    "id"=> 13,
                    "name"=> "Շենգավիթ",
                    "value" => "Շենգավիթ"
                  ],
                  [
                    "id"=> 14,
                    "name"=> "Վահագնի թաղամաս",
                    "value" => "Վահագնի թաղամաս"
                  ],
                  [
                    "id"=> 15,
                    "name"=> "Այլ",
                    "value" => "Այլ"
                  ]
                ]
              ],
              [
                "key" => "street",
                "title" => "Փողոց*",
                "type" => "select",
                "style" => "283px",
                "value" => "",
                "option" => []
              ],
              [
                "key" => "building",
                "title" => "Շենք*",
                "type" => "inputNumber",
                "style" => "100px",
                "value" => "",
                "option" => []
              ],
              [
                "key" => "entrance",
                "title" => "Մուտք*",
                "type" => "inputNumber",
                "style" => "100px",
                "value" => "",
                "option" => []
              ],
              [
                "key" => "apartment",
                "title" => "Բնակարան*",
                "type" => "inputNumber",
                "style" => "100px",
                "value" => "",
                "option" => []
              ],
              [
                "key" => "map",
                "title" => "MAP PIN*",
                "type" => "map",
                "style" => "631px",
                "value" => "",
                "option" => []
              ],
              [
                "key" => "realAddress",
                "title" => "Իրական հասցե*",
                "type" => "inputText",
                "style" => "629px",
                "value" => "",
                "option" => []
              ],
            ]
          ],
          [
            'name' => "price",
            'title'=> "Գինը",
            'added'=> [
              [
                "key" => "priceAdded",
                "title" => "Ավել Գինը*",
                "type" => "inputText",
                "style" => "width:80%",
                "value" => "",
                "option" => []
              ],
            ],
            "fields" => [
              [
                "key" => "totalPrice",
                "title" => "Ընդհանուր գինը*",
                "type" => "inputNumberSymbol",
                "style" => "202px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "priceUsd",
                    "name"=> "Գինը դոլարով",
                    "symbol"=>'usd',
                    "value" => ""
                  ],
                  [
                    "id"=> "priceAmd",
                    "name"=> "Գինը դրամով",
                    "symbol"=>'amd',
                    "value" => ""
                  ],
                  [
                    "id"=> "priceRub",
                    "name"=> "Գինը ռուբլիով",
                    "symbol"=>'rub',
                    "value" => ""
                  ]
                ]
              ],
              [
                "key" => "priceNegotiable",
                "title" => "Գինը պայմանագրային",
                "type" => "checkbox",
                "style" => "",
                "value" => "",
                "option" => [
                  "status" => false
                 ]
              ],
              [
                "key" => "sqmPrice",
                "title" => "Գինը 1 քմ*",
                "type" => "inputNumberSymbol",
                "style" => "202px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "sqmPriceUsd",
                    "name"=> "Գինը դոլարով",
                    "symbol"=>'usd',
                    "value" => ""
                  ],
                  [
                    "id"=> "sqmPriceAmd",
                    "name"=> "Գինը դրամով",
                    "symbol"=>'amd',
                    "value" => ""
                  ],
                  [
                    "id"=> "sqmPriceRub",
                    "name"=> "Գինը ռուբլիով",
                    "symbol"=>'rub',
                    "value" => ""
                  ]
                ]
              ],
              [
                "key" => "downPayment",
                "title" => "Կանխավճարի չափ*",
                "type" => "inputNumberSymbol",
                "style" => "202px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "downPaymentUsd",
                    "name"=> "Գինը դոլարով",
                    "symbol"=>'usd',
                    "value" => ""
                  ],
                  [
                    "id"=> "downPaymentAmd",
                    "name"=> "Գինը դրամով",
                    "symbol"=>'amd',
                    "value" => ""
                  ],
                  [
                    "id"=> "downPaymentRub",
                    "name"=> "Գինը ռուբլիով",
                    "symbol"=>'rub',
                    "value" => ""
                  ]
                ]
              ],
              [
                "key" => "paymentMethod",
                "title" => "Վճարման կարգը*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                [
                  "id"=> 0,
                  "name"=> "Ընտրեք",
                  "value" => ""
                ],
                [
                  "id"=> 1,
                  "name"=> "Բանկային փոխանցում",
                  "value" => "Բանկային փոխանցում"
                ],
                [
                  "id"=> 2,
                  "name"=> "Հիպոթեքային վարկ",
                  "value" => "Հիպոթեքային վարկ"
                ],
                [
                  "id"=> 3,
                  "name"=> "Այլ",
                  "value" => "Այլ"
                ],
                ]
              ],
              [
                "key" => "preferredBank",
                "title" => "Նախընտրած բանկը*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք նախընտրած բանկը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Ամերիա բանկ",
                    "value" => "Ամերիա բանկ"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Էվոկաբանկ",
                    "value" => "Էվոկաբանկ"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Ինեկոբանկ",
                    "value" => "Ինեկոբանկ"
                  ],
                  [
                    "id"=> 4,
                    "name"=> "ԱյԴի բանկ",
                    "value" => "ԱյԴի բանկ"
                  ],
                  [
                    "id"=> 5,
                    "name"=> "Ակբա բանկ",
                    "value" => "Ակբա բանկ"
                  ],
                  [
                    "id"=> 6,
                    "name"=> "Մելլաթ բանկ",
                    "value" => "Մելլաթ բանկ"
                  ],
                  [
                    "id"=> 7,
                    "name"=> "ՀայԷկոնոմ բանկ",
                    "value" => "ՀայԷկոնոմ բանկ"
                  ],
                  [
                    "id"=> 8,
                    "name"=> "HSBC բանկ",
                    "value" => "HSBC բանկ"
                  ],
                  [
                    "id"=> 9,
                    "name"=> "Յունիբանկ",
                    "value" => "Յունիբանկ"
                  ],
                  [
                    "id"=> 10,
                    "name"=> "Հայբիզնեսբանկ",
                    "value" => "Հայբիզնեսբանկ"
                  ],
                  [
                    "id"=> 11,
                    "name"=> "Կոնվերս բանկ",
                    "value" => "Կոնվերս բանկ"
                  ],
                  [
                    "id"=> 12,
                    "name"=> "Արարատ բանկ",
                    "value" => "Արարատ բանկ"
                  ],
                  [
                    "id"=> 13,
                    "name"=> "Ֆասթ բանկ",
                    "value" => "Ֆասթ բանկ"
                  ],
                  [
                    "id"=> 14,
                    "name"=> "Արմսվիսբանկ",
                    "value" => "Արմսվիսբանկ"
                  ],
                  [
                    "id"=> 15,
                    "name"=> "Արցախ բանկ",
                    "value" => "Արցախ բանկ"
                  ],
                  [
                    "id"=> 16,
                    "name"=> "Բիբլոս Բանկ Արմենիա",
                    "value" => "Բիբլոս Բանկ Արմենիա"
                  ],
                  [
                    "id"=> 17,
                    "name"=> "Արդշինբանկ",
                    "value" => "Արդշինբանկ"
                  ],
                  [
                    "id"=> 18,
                    "name"=> "ՎՏԲ-Հայաստան բանկ",
                    "value" => "ՎՏԲ-Հայաստան բանկ"
                  ],
                  [
                    "id"=> 19,
                    "name"=> "Այլ",
                    "value" => "Այլ"
                  ],
                ]
              ],
            ]
          ],
          [
            'name' => "houseDescription",
            'title'=> "Տան Նկարագիր",
            'added'=> [],
            "fields" => [
              [
                "key" => "surface",
                "title" => "Մակերես*",
                "type" => "inputNumberSymbol",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "surface",
                    "name"=> "Նշեք մակերեսը",
                    "symbol"=>'մ.ք.',
                    "value" => ""
                  ],
                ]
              ],
              [
                "key" => "ceilingHeight",
                "title" => "Առաստաղի բարձրությունը*",
                "type" => "inputNumberSymbol",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "ceilingHeight",
                    "name"=> "Նշեք բարձրությունը ",
                    "symbol"=>'մետր',
                    "value" => ""
                  ],
                ]
              ],
              [
                "key" => "NumberOfRooms",
                "title" => "Սենյակների քանակ*",
                "type" => "numSelect",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "NumberOfRooms",
                    "name"=> "1",
                    "value" => "1"
                  ],
                  [
                    "id"=> "NumberOfRooms",
                    "name"=> "2",
                    "value" => "2"
                  ],
                  [
                    "id"=> "NumberOfRooms",
                    "name"=> "3",
                    "value" => "3"
                  ],
                  [
                    "id"=> "NumberOfRooms",
                    "name"=> "4",
                    "value" => "4"
                  ],
                  [
                    "id"=> "NumberOfRooms",
                    "name"=> "5",
                    "value" => "5"
                  ],
                  [
                    "id"=> "NumberOfRooms",
                    "name"=> "6",
                    "value" => "6"
                  ],
                  [
                    "id"=> "NumberOfRooms",
                    "name"=> "7+",
                    "value" => "7+"
                  ],
                ]
              ],
              [
                "key" => "NumberOfBedrooms",
                "title" => "Նջասենյակի քանակ*",
                "type" => "numSelect",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "NumberOfBedrooms",
                    "name"=> "1",
                    "value" => "1"
                  ],
                  [
                    "id"=> "NumberOfBedrooms",
                    "name"=> "2",
                    "value" => "2"
                  ],
                  [
                    "id"=> "NumberOfBedrooms",
                    "name"=> "3",
                    "value" => "3"
                  ],
                  [
                    "id"=> "NumberOfBedrooms",
                    "name"=> "4",
                    "value" => "4"
                  ],
                  [
                    "id"=> "NumberOfBedrooms",
                    "name"=> "5",
                    "value" => "5"
                  ],
                  [
                    "id"=> "NumberOfBedrooms",
                    "name"=> "6",
                    "value" => "6"
                  ],
                  [
                    "id"=> "NumberOfBedrooms",
                    "name"=> "7+",
                    "value" => "7+"
                  ],
                ]
              ],
              [
                "key" => "NumberOfBathrooms",
                "title" => "Սահանգույցների քանակ*",
                "type" => "numSelect",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "NumberOfBathrooms",
                    "name"=> "1",
                    "value" => "1"
                  ],
                  [
                    "id"=> "NumberOfBathrooms",
                    "name"=> "2",
                    "value" => "2"
                  ],
                  [
                    "id"=> "NumberOfBathrooms",
                    "name"=> "3",
                    "value" => "3"
                  ],
                  [
                    "id"=> "NumberOfBathrooms",
                    "name"=> "4",
                    "value" => "4"
                  ],
                  [
                    "id"=> "NumberOfBathrooms",
                    "name"=> "5+",
                    "value" => "5+"
                  ],
                ]
              ],
              [
                "key" => "NumberOpenBalconies",
                "title" => "Բաց պատշգամբների քանակ*",
                "type" => "numSelect",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "NumberOpenBalconies",
                    "name"=> "0",
                    "value" => "0"
                  ],
                  [
                    "id"=> "NumberOpenBalconies",
                    "name"=> "1",
                    "value" => "1"
                  ],
                  [
                    "id"=> "NumberOpenBalconies",
                    "name"=> "2",
                    "value" => "2"
                  ],
                  [
                    "id"=> "NumberOpenBalconies",
                    "name"=> "3",
                    "value" => "3"
                  ],
                  [
                    "id"=> "NumberOpenBalconies",
                    "name"=> "4",
                    "value" => "4"
                  ],
                  [
                    "id"=> "NumberOpenBalconies",
                    "name"=> "5",
                    "value" => "5"
                  ],
                  [
                    "id"=> "NumberOpenBalconies",
                    "name"=> "6",
                    "value" => "6"
                  ],
                ]
              ],
              [
                "key" => "NumberCloseBalconies",
                "title" => "Փակ պատշգամբների քանակ*",
                "type" => "numSelect",
                "style" => "629px",
                "value" => "",
                "option" => [
                  [
                    "id"=> "NumberCloseBalconies",
                    "name"=> "0",
                    "value" => "0"
                  ],
                  [
                    "id"=> "NumberCloseBalconies",
                    "name"=> "1",
                    "value" => "1"
                  ],
                  [
                    "id"=> "NumberCloseBalconies",
                    "name"=> "2",
                    "value" => "2"
                  ],
                  [
                    "id"=> "NumberCloseBalconies",
                    "name"=> "3",
                    "value" => "3"
                  ],
                  [
                    "id"=> "NumberCloseBalconies",
                    "name"=> "4",
                    "value" => "4"
                  ],
                  [
                    "id"=> "NumberCloseBalconies",
                    "name"=> "5",
                    "value" => "5"
                  ],
                  [
                    "id"=> "NumberCloseBalconies",
                    "name"=> "6",
                    "value" => "6"
                  ],
                ]
              ],
              [
                "key" => "floor",
                "title" => "Հարկը*",
                "type" => "inputNumber",
                "style" => "306px",
                "value" => "",
                "option" => []
              ],
              [
                "key" => "houseCondition",
                "title" => "Տան վիճակ*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Պետական վիճակ",
                    "value" => "Պետական վիճակ"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Լավ",
                    "value" => "Լավ"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Զրոյական",
                    "value" => "Զրոյական"
                  ],
                  [
                    "id"=> 4,
                    "name"=> "Վերանորոգված",
                    "value" => "Վերանորոգված"
                  ],
                ]
              ],
              [
                "key" => "parking",
                "title" => "Ավտոկայանատեղի*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Քարե",
                    "value" => "Քարե"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Ստորգետնյա",
                    "value" => "Ստորգետնյա"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Բաց ավտոկայանատեղի",
                    "value" => "Բաց ավտոկայանատեղի"
                  ],
                  [
                    "id"=> 4,
                    "name"=> "Ազատ տարածություն",
                    "value" => "Ազատ տարածություն"
                  ],
                ]
              ],
              [
                "key" => "kitchenType",
                "title" => "Խոհանոցի տիպ*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Առանձին",
                    "value" => "Առանձին"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Ստուդիո",
                    "value" => "Ստուդիո"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Նախագծված չէ",
                    "value" => "Նախագծված չէ"
                  ],
                ]
              ],
            ]
          ],
          [
            'name' => "buildingDescription",
            'title'=> "Շինության նկարագիր",
            'added'=> [],
            "fields" => [
             [
              "key" => "buildingType",
              "title" => "Շինության տիպ*",
              "type" => "select",
              "style" => "306px",
              "value" => "",
              "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Մոնոլիտ",
                    "value" => "Մոնոլիտ"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Քարե",
                    "value" => "Քարե"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Պանելային",
                    "value" => "Պանելային"
                  ],
                  [
                    "id"=> 4,
                    "name"=> "Այլ",
                    "value" => "Այլ"
                  ],
              ]
             ],
             [
              "key" => "statement",
              "title" => "ՀԱՐԿԱՅՆՈՒԹՅՈՒՆ*",
              "type" => "inputText",
              "style" => "306px",
              "value" => "",
              "option" => [],
             ],
             [
              "key" => "buildingConstructionYear",
              "title" => "Շենքի կառուցման տարին*",
              "type" => "inputNumber",
              "style" => "306px",
              "value" => "",
              "option" => [],
             ],
             [
              "key" => "orentation",
              "title" => "կողմնորոշումը*",
              "type" => "select",
              "style" => "306px",
              "value" => "",
              "option" => [
                [
                  "id"=> 0,
                  "name"=> "Ընտրեք տեսակը",
                  "value" => ""
                ],
                [
                  "id"=> 1,
                  "name"=> "Հյուսիսային",
                  "value" => "Հյուսիսային"
                ],
                [
                  "id"=> 2,
                  "name"=> "Հարավային",
                  "value" => "Հարավային"
                ],
                [
                  "id"=> 3,
                  "name"=> "Արևելյան",
                  "value" => "Արևելյան"
                ],
                [
                  "id"=> 4,
                  "name"=> "Արևմտյան",
                  "value" => "Արևմտյան"
                ],
                [
                  "id"=> 5,
                  "name"=> "Հարավ-Արևելյան",
                  "value" => "Հարավ-Արևելյան"
                ],
                [
                  "id"=> 6,
                  "name"=> "Հարավ-Արևմտյան",
                  "value" => "Հարավ-Արևմտյան"
                ],
                [
                  "id"=> 7,
                  "name"=> "Հյուսիս-Արևելյան",
                  "value" => "Հյուսիս-Արևելյան"
                ],
                [
                  "id"=> 8,
                  "name"=> "Հյուսիս-Արևմտյան",
                  "value" => "Հյուսիս-Արևմտյան"
                ],
              ]
             ],
             [
              "key" => "monthlyFee",
              "title" => "ԱմսԱկան Սպասարկման Վճար*",
              "type" => "inputNumSymbol",
              "style" => "202px",
              "value" => "",
              "option" => [
                [
                  "id"=> "monthlyFeeUsd",
                  "name"=> "Գինը դոլարով",
                  "symbol"=>'usd',
                  "value" => ""
                ],
                [
                  "id"=> "monthlyFeeAmd",
                  "name"=> "Գինը դրամով",
                  "symbol"=>'amd',
                  "value" => ""
                ],
                [
                  "id"=> "monthlyFeeRub",
                  "name"=> "Գինը ռուբլիով",
                  "symbol"=>'rub',
                  "value" => ""
                ]
              ]
             ],
             [
              "key" => "propertyTax",
              "title" => "Տարեկան գույքահարկ*",
              "type" => "inputNumSymbol",
              "style" => "202px",
              "value" => "",
              "option" => [
                [
                  "id"=> "propertyTaxUsd",
                  "name"=> "Գինը դոլարով",
                  "symbol"=>'usd',
                  "value" => ""
                ],
                [
                  "id"=> "propertyTaxAmd",
                  "name"=> "Գինը դրամով",
                  "symbol"=>'amd',
                  "value" => ""
                ],
                [
                  "id"=> "propertyTaxRub",
                  "name"=> "Գինը ռուբլիով",
                  "symbol"=>'rub',
                  "value" => ""
                ]
              ]
             ],
            ]
          ],
          [
            'name' => "mainFacility",
            'title'=> "Կոմունալ հարմարություններ",
            'added'=> [],
            "fields" => [
              [
                "key" => "individualHeatingSystem",
                "title" => "Անհատական ջեռուցման համակարգ",
                "type" => "checkbox",
                "style" => "371px",
                "value" => "",
              ],
              [
                "key" => "electricity",
                "title" => "Էլեկտրոէներգիա",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "centralizedHeatingSystem",
                "title" => "Կենտրոնացված ջեռուցման համակարգ",
                "type" => "checkbox",
                "style" => "371px",
                "value" => "",
              ],
              [
                "key" => "gas",
                "title" => "Գազ",
                "type" => "checkbox",
                "style" => "157px",
                "value" => "",
              ],
              [
                "key" => "airConditioner",
                "title" => "Օդորակիչ",
                "type" => "checkbox",
                "style" => "371px",
                "value" => "",
              ],
              [
                "key" => "centralizedCoolingSystem",
                "title" => "Կենտրոնացած հովացման համակարգ",
                "type" => "checkbox",
                "value" => "",
              ],
            ],
          ],
          [
            'name' => "otherFacility",
            'title'=> "Այլ հարմարություններ",
            'added'=> [],
            "fields" => [
              [
                "key" => "furniture",
                "title" => "Կահույք",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "storageRoom",
                "title" => "Խորդանոց",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "technics",
                "title" => "Տեխնիկա",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "wardrobe",
                "title" => "Զգեստապահարան",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "elevator",
                "title" => "Վերելակ",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "laundryRoom",
                "title" => "Լվացքատուն",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "closedEconomyBalcony",
                "title" => "Փակ տնտեսական պատշգամբ",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "park",
                "title" => "Զբոսայգի",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "europeWindow",
                "title" => "Եվրոպատուհան",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "firstLine",
                "title" => "Առաջին գիծ",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "laminate",
                "title" => "Լամինատ",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "courtyardBuilding",
                "title" => "Միջբակային շենք",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "parquetFloor",
                "title" => "Մանրահատակ",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "nearStop",
                "title" => "Կանգառի մոտ",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "tiled",
                "title" => "Սալիկապատված",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "sunnySide",
                "title" => "Արևկողմ",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "presgranite",
                "title" => "Պռեսգրանիտ",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "heatedFloor",
                "title" => "Տաքացվող հատակ",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "beautifulView",
                "title" => "Գեղեցիկ տեսարան",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              
              [
                "key" => "gate",
                "title" => "Դարպաս",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "securitySystem",
                "title" => "Անվտանգության համակարգ",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "fenced",
                "title" => "Պարսպապատ",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "playground",
                "title" => "Խաղահրապարակ",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "twoWayEntry",
                "title" => "Երկկողմանի մուտք",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "bilateralPosition",
                "title" => "Երկկողմանի դիրք",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "ironDoor",
                "title" => "Երկաթյա դուռ",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "sauna",
                "title" => "Շոգեբաղնիք",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "loggia",
                "title" => "Լոջա",
                "type" => "checkbox",
                "value" => "",
              ],
              [
                "key" => "pool",
                "title" => "Լողավազան",
                "type" => "checkbox",
                "style" => "309px",
                "value" => "",
              ],
              [
                "key" => "floor",
                "title" => "Հատակ*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք հատակի տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Լամինատ",
                    "value" => "Լամինատ"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Մանրահատակ",
                    "value" => "Մանրահատակ"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Սալիկ",
                    "value" => "Սալիկ"
                  ],
                  [
                    "id"=> 4,
                    "name"=> "Բետոն",
                    "value" => "Բետոն"
                  ],
                  [
                    "id"=> 5,
                    "name"=> "Այլ",
                    "value" => "Այլ"
                  ],
                ],
              ],
              [
                "key" => "roof",
                "title" => "Առաստաղ*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք առաստաղի տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Կախովի",
                    "value" => "Կախովի"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Ձգվող",
                    "value" => "Ձգվող"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Բետոն",
                    "value" => "Բետոն"
                  ],
                ],
              ],
              [
                "key" => "cover",
                "title" => "Ծածկեր*",
                "type" => "select",
                "style" => "306px",
                "value" => "",
                "option" => [
                  [
                    "id"=> 0,
                    "name"=> "Ընտրեք ծածկի տեսակը",
                    "value" => ""
                  ],
                  [
                    "id"=> 1,
                    "name"=> "Բետոն",
                    "value" => "Բետոն"
                  ],
                  [
                    "id"=> 2,
                    "name"=> "Բաղդադ",
                    "value" => "Բաղդադ"
                  ],
                  [
                    "id"=> 3,
                    "name"=> "Պանել",
                    "value" => "Պանել"
                  ],
                ],
              ],
            ],
          ],
          [
            'name' => "media",
            'title'=> "Մեդիա",
            'added'=> [],
            "fields" => [
              [
                "key" => "uploadedImgs",
                "title" => "",
                "type" => "imgsUpload",
                "style" => "639px",
                "value" => "",
                "option" => [],
              ],
              [
                "key" => "video",
                "title" => "Վիդեոյի հղում*",
                "type" => "inputText",
                "style" => "639px",
                "value" => "",
                "option" => [],
              ]
            ],
          ],
          [
            'name' => "keywords",
            'title'=> "Բանալի Բառեր",
            'added'=> [],
            "fields" => [
              [
                "key" => "chooseWords",
                "title" => "Ընտրել բառեր*",
                "type" => "keyword",
                "style" => "631px",
                "value" => "",
                "option" => [],
              ],
            ],
          ],
          [
            'name' => "juridical",
            'title'=> "Իրավաբանական",
            'added'=> [],
            "fields" => [
              [
                "key" => "owner",
                "title" => "Սեփականատեր*",
                "type" => "inputText",
                "style" => "412px",
                "value" => "",
                "option" => [],
              ],
              [
                "key" => "ownerTel",
                "title" => "Սեփականատիրոջ Հեռախոսահամար*",
                "type" => "inputNumber",
                "style" => "412px",
                "value" => "",
                "option" => [],
              ],
              [
                "key" => "addOwner",
                "title" => "Ավելացնել սեփականատեր",
                "type" => "addField",
                "style" => "217px",
                "value" => "",
                "option" => [],
              ],
            ],
          ],
          [
            'name' => "additionalInfo",
            'title'=> "Լրացուցիչ Ինֆորմացիա",
            'added'=> [],
            "fields" => [
              [
                "key" => "owner",
                "title" => "Գրեք նախընտրած ինֆորմացիան*",
                "type" => "inputText",
                "style" => "412px",
                "value" => "",
                "option" => [],
              ],
              [
                "key" => "uploadFiles",
                "title" => "Կցել Փաստաթուղթ",
                "type" => "uploadFile",
                "style" => "217px",
                "value" => "",
                "option" => [],
              ],
            ],
          ],
          [
            'name' => "specialists",
            'title'=> "Կից Մասնագետներ",
            'added'=> [],
            "fields" => [
              [
                "key" => "agent",
                "title" => "Գործակալ*",
                "type" => "select",
                "style" => "412px",
                "value" => "",
                "option" => [],
              ],
              [
                "key" => "meneger",
                "title" => "Մենեջեր*",
                "type" => "select",
                "style" => "412px",
                "value" => "",
                "option" => [],
              ],
            ],
          ],
        ]);
       $form->save();
//         $form->am = json_encode(
//           [
//             [
//               'name' => "announcement",
//                'title'=> "Հայտարարություն",
//               'data'=> [
//                 "ԳՈՐԾԱՐՔԻ ՏԵՍԱԿ" => [
//                   "Ընտրեք տեսակը",
//                   "Վարձակալություն",
//                   "Վաճառք",
//                 ],
//                 "ԳՈՒՅՔԻ ՏԵՍԱԿ" => [
//                   "Ընտրեք տեսակը",
                  
//                 ],
//                 "Հայտարարության վերնագիր",
//                 "Հայտարարության Նկարագիր",
//                 "Հայտարարության ՏԵՍԱԿ",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "location",
//               'title'=> "Գտնվելու Վայրը - Երևան",
//               'data'=> [
//                 "Համայնք",
//                 "Փողոց",
//                 "ՇԵՆՔ",
//                 "ՄՈՒՏՔ",
//                 "ԲՆԱԿԱՐԱՆ",
//                 "Քարտեզ",
//                 "ԻՐԱԿԱՆ ՀԱՍՑԵ",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "price",
//               'title'=> "Գինը",
//               'data'=> [
//                 "ԸՆԴՀԱՆՈՒՐ ԳԻՆԸ",
//                 "ԳԻՆԸ ՊԱՅՄԱՆԱԳՐԱՅԻՆ",
//                 "ԳԻՆԸ 1քմ",
//                 "ՆԱԽԸՆՏՐԵԼԻ ԿԱՆԽԱՎՃԱՐԻ ՉԱՓ",
//                 "ՎՃԱՐՄԱՆ ԿԱՐԳԸ",
//                 "ՆԱԽԸՆՏՐԱԾ ԲԱՆԿԸ",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "houseDescription",
//               'title'=> "Տան Նկարագիր",
//               'data'=> [
//                 "ՄԱԿԵՐԵՍ",
//                 "ԱՌԱՍՏԱՂԻ ԲԱՐՁՐՈՒԹՅՈՒՆԸ",
//                 "ՍԵՆՅԱԿՆԵՐԻ ՔԱՆԱԿ",
//                 "ՆՆՋԱՍԵՆՅԱԿՆԵՐԻ ՔԱՆԱԿ",
//                 "ՍԱՆՀԱՆԳՈՒՅՑՆԵՐԻ ՔԱՆԱԿ",
//                 "ԲԱՑ ՊԱՏՇԳԱՄԲՆԵՐԻ ՔԱՆԱԿ",
//                 "ՓԱԿ ՊԱՏՇԳԱՄԲՆԵՐԻ ՔԱՆԱԿ",
//                 "ՀԱՐԿԸ",
//                 "ՏԱՆ ՎԻՃԱԿ",
//                 "ԱՎՏՈԿԱՅԱՆԱՏԵՂԻ",
//                 "ԽՈՀԱՆՈՑԻ ՏԻՊ",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "buildingDescription",
//               'title'=> "Շինության Նկարագիր",
//               'data'=> [
//                 "ՇԻՆՈՒԹՅԱՆ ՏԻՊ",
//                 "ՀԱՐԿԱՅՆՈՒԹՅՈՒՆ",
//                 "ՇԵՆՔԻ ԿԱՌՈՒՑՄԱՆ ՏԱՐԻՆ",
//                 "ԿՈՂՄՆՈՐՈՇՈՒՄԸ",
//                 "ՏԱՐԵԿԱՆ ԳՈՒՅՔԱՀԱՐԿ",
//                 "ԱՄՍԱԿԱՆ ՍՊԱՍԱՐԿՄԱՆ ՎՃԱՐ",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "mainFacilities",
//               'title'=> "Կոմունալ Հարմարություններ",
//               'data'=> [
//                 "ԱՆՀԱՏԱԿԱՆ ՋԵՌՈՒՑՄԱՆ ՀԱՄԱԿԱՐԳ",
//                 "Կենտրոնացված ջեռուցման համակարգ",
//                 "Օդորակիչ",
//                 "ԿԵՆՏՐՈՆԱՑԱԾ ՀՈՎԱՑՄԱՆ ՀԱՄԱԿԱՐԳ",
//                 "ԷԼԵԿՏՐՈԷՆԵՐԳԻԱ",
//                 "ԳԱԶ",
//               ],
//               'added'=> [],
//             ],
//              [
//               'name'=> "otherFacilities",
//               'title'=> "Այլ Հարմարություններ",
//               'data'=> [
//                 "ԿԱՀՈՒՅՔ",
//                 "Տեխնիկա",
//                 "Վերելակ",
//                 "ՓԱԿ ՏՆՏԵՍԱԿԱՆ ՊԱՏՇԳԱՄԲ",
//                 "ԵՎՐՈՊԱՏՈՒՀԱՆ",
//                 "ԼԱՄԻՆԱՏ",
//                 "ՄԱՆՐԱՀԱՏԱԿ",
//                 "ՍԱԼԻԿԱՊԱՏՎԱԾ",
//                 "ՊՌԵՍԳՐԱՆԻՏ",
//                 "ԳԵՂԵՑԻԿ ՏԵՍԱՐԱՆ",
//                 "ԱՆՎՏԱՆԳՈՒԹՅԱՆ ՀԱՄԱԿԱՐԳ",
//                 "ԽԱՂԱՀՐԱՊԱՐԱԿ",
//                 "ԵՐԿԿՈՂՄԱՆԻ ԴԻՐՔ",
//                 "ՇՈԳԵԲԱՂՆԻՔ",
//                 "ԼՈՋԱ",
//                 "ԽՈՐԴԱՆՈՑ",
//                 "ԶԳԵՍՏԱՊԱՀԱՐԱՆ",
//                 "ԼՎԱՑՔԱՏՈՒՆ",
//                 "ԶԲՈՍԱՅԳԻ",
//                 "ԱՌԱՋԻՆ ԳԻԾ",
//                 "ՄԻՋԲԱԿԱՅԻՆ ՇԵՆՔ",
//                 "ԿԱՆԳԱՌԻ ՄՈՏ",
//                 "ԱՐԵՎԿՈՂՄ",
//                 "ՏԱՔԱՑՎՈՂ ՀԱՏԱԿ",
//                 "ԴԱՐՊԱՍ",
//                 "ՊԱՐՍՊԱՊԱՏ",
//                 "ԵՐԿԿՈՂՄԱՆԻ ՄՈՒՏՔ",
//                 "ԵՐԿԱԹՅԱ ԴՈՒՌ",
//                 "ԼՈՂԱՎԱԶԱՆ",
//                 "ՀԱՏԱԿ",
//                 "ԱՌԱՍՏԱՂ",
//                 "ԾԱԾԿԵՐ",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "juridical",
//               'title'=> "Իրավաբանական",
//               'data'=> ["ՍԵՓԱԿԱՆԱՏԵՐ", "ՍԵՓԱԿԱՆԱՏԻՐՈՁ ՀԵՌԱԽՈՍԱՀԱՄԱՐ"],
//               'added'=> [],
//             ],
//             [
//               'name'=> "information",
//               'title'=> "Լրացուցիչ ինֆորմացիա",
//               'data'=> ["ԻՆՖՈՐՄԱՑԻԱ", "ՓԱՍԹԱԹՈՒՂԹ"],
//               'added'=> [],
//             ],
//             [
//               'name'=> "specialists",
//               'title'=> "Կից Մասնագետներ",
//               'data'=> ["ԳՈՐԾԱԿԱԼ", "ՄԵՆԵՋԵՐ"],
//               'added'=> [],
//             ],
//           ]
//         );

//         $form->ru = json_encode(
//           [
//             [
//               'name' => "announcement",
//               'title'=> "Объявление",
//               'data'=> [
//                 "Тип операции",
//                 "Тип недвижимости",
//                 "Название объявления",
//                 "Описание объявления",
//                 "Тип объявления",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "location",
//               'title'=> "Расположение - Ереван",
//               'data'=> [
//                 "Административный район",
//                 "Улица",
//                 "ЗДАНИЕ",
//                 "Вход",
//                 "Квартира",
//                 "На карте",
//                 "Реальный адрес",
//               ],
//               'added'=>[],
//             ],
//             [
//               'name'=> "price",
//               'title'=> "Цена",
//               'data'=> [
//                 "Цена",
//                 "Цена договорная",
//                 "Цена/ кв. м.",
//                 "Сумма предоплаты",
//                 "Способ оплаты",
//                 "Предпочтительный банк владельца",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "houseDescription",
//               'title'=> "Описание дома",
//               'data'=> [
//                 "Площадь",
//                 "Высота потолка",
//                 "Кол-во комнат",
//                 "Кол-во спален",
//                 "Кол-во ванных комнат",
//                 "Кол-во открытых балконов",
//                 "Кол-во закрытых балконов",
//                 "Этаж",
//                 "Состояние квартиры",
//                 "Парковка",
//                 "Тип кухни",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "buildingDescription",
//               'title'=> "Описание Здания",
//               'data'=> [//                 "Тип здания",
//                 "Кольво этажей",
//                 "Дата строительства",
//                 "Ориентация",
//                 "Ежегодный налог на недвижимость",
//                 "Ежемесячная плата за обслуживание",
//               ],
//               'added'=>[],
//             ],
//             [
//               'name'=> "mainFacilities",
//               'title'=> "Коммунальные услуги",
//               'data'=> [
//                 "Индивидуальная система отопления",
//                 "Центральная система отопления",
//                 "Кондиционер",
//                 "Концентрированная система охлаждения",
//                 "Электричество",
//                 "Газ",
//               ],
//               'added'=> [],
//             ],
//              [
//               'name'=> "otherFacilities",
//               'title'=> "Другие удобства",
//               'data'=> [
//                 "МЕБЕЛЬ",
//                 "Техника",
//                 "Лифт",
//                 "Закрытый балкон",
//                 "ЕВРОПЕЙСКОЕ ОКНО",
//                 "ЛАМИНАТ",
//                 "Паркет",
//                 "Плиточный",
//                 "Керамогранит",
//                 "Красивый вид",
//                 "Система безопасности",
//                 "Детская площадка",
//                 "Двусторонняя позиция",
//                 "Сауна",
//                 "Лоджия",
//                 "Чулан",
//                 "Гардероб",
//                 "Прачечная",
//                 "Парк",
//                 "Первая линия",
//                 "Внутридворовое здание",
//                 "Рядом с остановкой",
//                 "На солнечной стороне",
//                 "Тёплые полы",
//                 "Ворота",
//                 "Огорожен",
//                 "Двусторонний вход",
//                 "Железная дверь",
//                 "Бассейн",
//                 "Пол",
//                 "Потолок",
//                 "Покрытия",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "juridical",
//               'title'=> "Юридический",
//               'data'=> ["Владелец", "Номер телефона владельца"],
//               'added'=> [],
//             ],
//             [
//               'name'=> "information",
//               'title'=> "Дополнительная информация",
//               'data'=> ["Информация", "Документ"],
//               'added'=> [],
//             ],
//             [
//               'name'=> "specialists",
//               'title'=> "Дополнительные специалисты",
//               'data'=> ["Агент", "Менеджер"],
//               'added'=> [],
//             ],
// ]
//         );
//         $form->en = json_encode(
//           [
//             [
//               'name' => "announcement",
//                'title'=> "Announcement",
//               'data'=> [
//                 "Transactions",
//                 "Property Type",
//                 "Announcement Title",
//                 "Announcement Description",
//                 "Announcement Type",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "location",
//               'title'=> "Location - Yerevan",
//               'data'=> [
//                 "Administrative District",
//                 "Street",
//                 "Building",
//                 "Entrance",
//                 "Apartment",
//                 "See on Map",
//                 "Real Address",
//               ],
//               'added'=>[],
//             ],
//             [
//               'name'=> "price",
//               'title'=> "Price",
//               'data'=> [
//                 "Price",
//                 "Price negotiable",
//                 "SQM /price",
//                 "Down Payment",
//                 "Payment method",
//                 "Owner's preferred bank",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "houseDescription",
//               'title'=> "House Description",
//               'data'=> [
//                 "Area",
//                 "Ceiling height",
//                 "Number of rooms:",
//                 "Number of bedrooms:",
//                 "Number of bathrooms",
//                 "Number of open balconies",
//                 "Number of closed balconies",
//                 "Floor",
//                 "Home Conditions",
//                 "Parking lot",
//                 "Kitchen type",
//               ],
//               'added'=> [],
//             ],
//             [
//               'name'=> "buildingDescription",
//               'title'=> "Building Description",
//               'data'=> [
//                 "Building type",
//                 "Number of floors",
//                 "Construction date",
//                 "Orientation",
//                 "Yearly property tax",
//                 "Monthly service fee",
//               ],
//               'added'=>[],
//             ],
//             [
//               'name'=> "mainFacilities",
//               'title'=> "Utility Facilities",
//               'data'=> [
//                 "Individual heating system",
//                 "Central heating system",
//                 "A/C",
//                 "Concentrated cooling system",
//                 "Electricity",
//                 "Gas",
//               ],
//               'added'=> [],
//             ],
//              [
//               'name'=> "otherFacilities",
//               'title'=> "Other Facilities",
//               'data'=> [
//                 "Furniture",
//                 "Equipment",
//                 "Elevator",
//                 "Closed balcony",
//                 "European window",
//                 "Laminate flooring",
//                 "Parquet",
//                 "Tiled",
//                 "Porcelain stoneware",
//                 "Beautiful view",
//                 "Security system",
//                 "Playground",
//                 "Two-way position",
//                 "Sauna",
//                 "Loggia",
//                 "Storage room",
//                 "Wardrobe",
//                 "Laundry",
//                 "Park",
//                 "First line",
//                 "Courtyard building",
//                 "Near the stop",
//                 "Sun-facing",
//                 "Heated floors",
//                 "Gate",
//                 "Fenced",
//                 "Two-way entrance",
//                 "Iron door",
//                 "Pool",
//                 "Floor",
//                 "Ceiling",
//                 "Wall-covering",
//               ],
//               'added'=> [],
//             ],
//             [//               'name'=> "juridical",
//               'title'=> "Juridical",
//               'data'=> ["Owner", "Owner's telephone number"],
//               'added'=> [],
//             ],
//             [
//               'name'=> "information",
//               'title'=> "Additional Information",
//               'data'=> ["Information", "Document"],
//               'added'=> [],
//             ],
//             [
//               'name'=> "specialists",
//               'title'=> "Adjunct Specialists",
//               'data'=> ["Agent", "Moderator"],
//               'added'=> [],
//             ],
// ]

//         );
//         $form->save();

    }
  }