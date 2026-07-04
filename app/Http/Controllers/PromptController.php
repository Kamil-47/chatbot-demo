<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function edit()
    {
        $prompt = Prompt::firstOrCreate([], ['content' => '']);

        return view('prompt.edit', compact('prompt'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $prompt = Prompt::firstOrCreate([], ['content' => '']);
        $prompt->update(['content' => $request->content]);

        return redirect()->route('prompt.edit')->with('success', 'Prompt został zapisany.');
    }
}