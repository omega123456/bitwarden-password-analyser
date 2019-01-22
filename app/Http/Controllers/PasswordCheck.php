<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\PasswordCheck as PasswordCheckModel;
use Illuminate\Support\Collection;

class PasswordCheck extends BaseController
{
    /**
     * @var PasswordCheckModel
     */
    private $model;
    /**
     * @var Request
     */
    private $request;

    public function __construct(PasswordCheckModel $model, Request $request)
    {
        $this->model   = $model;
        $this->request = $request;
    }

    public function index()
    {
        if (($result = session('processedPasswords')) !== null) {
            return $this->renderResult($result);
        }

        return view(
            'uploadFile'
        );
    }

    public function upload()
    {
        $file = $this->request->file('passwordFile');

        $this->model->processPasswords($file->openFile());

        return redirect('/');
    }

    private function renderResult(Collection $result)
    {
        return view(
            'result',
            [
                'items' => $result
            ]
        );
    }
}
