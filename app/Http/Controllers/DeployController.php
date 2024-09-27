<?php

namespace App\Http\Controllers;

class DeployController extends Controller
{
    public function frontend()
    {
        try {
            $output = shell_exec('cd ../../ && ./deploy-main.sh');
            return response()->json([
                'message' => 'success',
                'output'  => $output
            ]);
        } catch (\Exception $e) {
            return response()
                ->json([
                    'message' => "Deploying failed",
                    'error'   => $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile(),
                ]);
        }
    }
}
