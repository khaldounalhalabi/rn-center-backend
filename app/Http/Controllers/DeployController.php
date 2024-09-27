<?php

namespace App\Http\Controllers;

class DeployController extends Controller
{
    public function frontend()
    {
        set_time_limit(0);
        try {
            $output = shell_exec('
                export PATH=/opt/cpanel/ea-nodejs18/bin/:$PATH
                && cd /home/pom/public_html/pom-front/
                && pm2 kill
                && rm -r -f .next
                && git reset --hard
                && git clean -df
                && git pull origin
                && npm i
                && npm run build
                && pm2 start ./ecosystem.config.js
            ');
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
