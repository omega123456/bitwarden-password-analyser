<?php

namespace App\Http\Controllers;

use App\Models\PasswordCheck as PasswordCheckModel;
use Illuminate\{
    Http\Request,
    Routing\Controller as BaseController,
    Support\Collection,
    Validation\Factory as Validator
};
use JsonSchema\Exception\JsonDecodingException;

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
    /**
     * @var Validator
     */
    private $validator;

    public function __construct(PasswordCheckModel $model, Request $request, Validator $validator)
    {
        $this->model     = $model;
        $this->request   = $request;
        $this->validator = $validator;
    }

    public function index()
    {
        if (($result = session('processedPasswords')) !== null) {
            return $this->renderResult($result);
        }

        return view(
            'index',
            [
                'error' => session('error')
            ]
        );
    }

    public function upload()
    {
        $file = $this->request->file('passwordFile');

        if (empty($file)) {
            session()->flash('error', 'No file has been uploaded');

            return redirect(route('/'));
        }

        try {
            $this->model->processPasswords($file->openFile());
        } catch (JsonDecodingException $e) {
            session()->flash('error', 'Uploaded file is an invalid json');
        }

        return redirect('/');
    }

    private function renderResult(Collection $result)
    {
        return view(
            'result',
            [
                'items' => $result,
                'error' => session('error')
            ]
        );
    }
}
