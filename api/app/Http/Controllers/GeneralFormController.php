<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GlobalForm;
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

    public function getFormStructure() {
        $structure = $this->generalFormService->getFormStructure();
        return response()->json($structure);
    }

    public function addGlobalFormField(Request $request) {
        $data = $request->all();
        $this->generalFormService->addGeneralField($data);
        $structure = $this->generalFormService->getFormStructure();
        return response()->json($structure);
    }

    public function removeGlobalFormField(Request $request) {
        $data = $request->all();
        $this->generalFormService->removeGeneralField($data);
        $structure = $this->generalFormService->getFormStructure();
        return response()->json($structure);
    }
}
