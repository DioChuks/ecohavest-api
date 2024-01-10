<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    use HttpResponses;

    public function subscribe(Request $request)
    {
        try {
            $request->validate([
                'email' =>'required|email|unique:newsletters'
            ]);
    
            $newsletter = Newsletter::create([
                'email' => $request->email
            ]);
    
            return $this->success(null, 'Subscribed successfully', 201);
        } catch (\Throwable $th) {
            return $this->error(null, $th->getMessage());
        }
    }
}
