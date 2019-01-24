<?php

namespace App\Http\Controllers;

use App\Models\PasswordCheck as PasswordCheckModel;
use Illuminate\{
    Http\Request,
    Routing\Controller as BaseController};
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

    public function __construct(PasswordCheckModel $model, Request $request)
    {
        $this->model     = $model;
        $this->request   = $request;
    }

    public function index()
    {
        if (!session('processing_file') && $this->model->getProcessedResult()->count()) {
            return $this->renderResult();
        }

        return view(
            'index',
            [
                'error'        => session('error'),
                'isProcessing' => session('processing_file')
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
            $this->model->queueRequest($file);
            session(['processing_file' => true]);
        } catch (JsonDecodingException $e) {
            session()->flash('error', 'Uploaded file is an invalid json');
        }

        return redirect('/');
    }

    public function checkFile()
    {
        $hasFileBeenProcessed = $this->model->getProcessedResult()->count() > 0;

        if ($hasFileBeenProcessed) {
            session(['processing_file' => false]);
        }

        return response()->json($hasFileBeenProcessed);
    }

    private function renderResult()
    {
        return view(
            'result',
            [
                'items' => $this->model->getProcessedResult(),
                'error' => session('error')
            ]
        );
    }
}
