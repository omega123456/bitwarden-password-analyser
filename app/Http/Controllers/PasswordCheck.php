<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPasswordRequest;
use App\Jobs\PasswordQueue;
use App\Processors\PasswordProcessor;
use Cookie;
use Illuminate\{
    Routing\Controller as BaseController};

class PasswordCheck extends BaseController
{
    /**
     * @var PasswordProcessor
     */
    private $passwordProcessor;

    public function __construct(PasswordProcessor $passwordProcessor)
    {
        $this->passwordProcessor = $passwordProcessor;
    }

    public function index()
    {
        if (!session('processing_file') && $this->passwordProcessor->getProcessedResult()->count()) {
            return $this->renderResult();
        }

        return view(
            'index',
            [
                'isProcessing' => session('processing_file'),
                'error'        => session('error')
            ]
        );
    }

    public function upload(UploadPasswordRequest $passwordRequest)
    {
        $file = $passwordRequest->file('passwordFile');

        $this->passwordProcessor->clearCurrentFileData();
        PasswordQueue::dispatch($file, $passwordRequest->post('password_manager_type'));
        session(['processing_file' => true]);

        return redirect('/');
    }

    public function checkFile()
    {
        $hasFileBeenProcessed = $this->passwordProcessor->getProcessedResult()->count() > 0;
        $hasJobFailed         = PasswordQueue::hasJobFailed(Cookie::get('file_hash'));

        if ($hasFileBeenProcessed || $hasJobFailed) {
            session(['processing_file' => false]);
        }

        if ($hasJobFailed) {
            $this->passwordProcessor->clearCurrentFileData();
            session()->flash('error', 'Could not process the uploaded password file');
        }

        return response()->json($hasFileBeenProcessed || $hasJobFailed);
    }

    private function renderResult()
    {
        return view(
            'result',
            [
                'items' => $this->passwordProcessor->getProcessedResult(),
                'error' => session('error')
            ]
        );
    }
}
