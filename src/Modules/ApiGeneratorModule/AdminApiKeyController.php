<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CbValidator;

class AdminApiKeyController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->titleField = "nama";
        $this->buttonShow = false;
        $this->deleteBtn = false;
        $this->buttonAdd = false;
        $this->buttonImport = false;
        $this->buttonExport = false;
    }

    public function getSecretKey()
    {
        $this->cbLoader();
        $data = [
            'page_title' => 'API Generator',
            'apikeys' => ApiKeysRepository::get(),
        ];

        return view('CbApiGen::api_key', $data);
    }

    function getGenerateSecretKey()
    {
        $this->cbLoader();
        //Generate a random string.
        $token = openssl_random_pseudo_bytes(16);

        //Convert the binary data into hexadecimal representation.
        $token = bin2hex($token);
        $id = ApiKeysRepository::insertGetId($token);

        $response = [
            'id' => $id,
            'key' => $token,
        ];

        return response()->json($response);
    }

    public function getStatusApikey()
    {
        CbValidator::valid(['id' => 'required', 'status' => 'required'], 'view');

        $id = request('id');
        $status = (request('status') == 1) ? "active" : "non active";

        ApiKeysRepository::updateById($status, $id);

        backWithMsg('You have been update api key status !');
    }

    public function getDeleteApiKey()
    {
        if (ApiKeysRepository::deleteById(request('id'))) {
            return response()->json(['status' => 1]);
        }

        return response()->json(['status' => 0]);
    }
}
