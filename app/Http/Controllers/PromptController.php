<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function edit()
    {
        $prompt = $this->singleton();

        return view('prompt.edit', compact('prompt'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string|max:20000',
        ]);

        $prompt = $this->singleton();
        $prompt->update(['content' => $data['content']]);

        return redirect()->route('prompt.edit')->with('success', 'Prompt został zapisany.');
    }

    /**
     * Zwraca singleton rekord promptu (id=1). Atomiczne — bez race condition
     * możliwej przy firstOrCreate([], ...).
     */
    private function singleton(): Prompt
    {
        return Prompt::firstOrCreate(
            ['id' => 1],
            ['content' => '']
        );
    }
}
