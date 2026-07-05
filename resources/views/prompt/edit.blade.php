<x-layout>
  <div class="page-header">
    <h1 class="page-title">System Prompt</h1>
  </div>

  <div class="form-card" style="max-width: 900px;">

    @if(config('app.demo_mode'))
      <div style="background:#e8f4fd; color:#1e40af; padding:20px; border-radius:8px; border-left:4px solid #3b82f6; margin-bottom:20px;">
        <h3 style="margin-bottom:10px;">Edycja promptu systemowego</h3>
        <p>W tym miejscu administrator może edytować system prompt chatbota AI — czyli instrukcję, która definiuje jego zachowanie, ton i zakres odpowiedzi.</p>
        <p style="margin-top:10px;">Chatbot korzysta z tego promptu przy każdej rozmowie, co pozwala łatwo dostosować jego styl, język i dostępne funkcje bez modyfikacji kodu.</p>
        <p style="margin-top:10px; font-size:13px; color:#6b7280;">W trybie demo edycja jest wyłączona.</p>
      </div>

      <div>
        <label class="form-label">Aktualny prompt (podgląd):</label>
        <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px; font-family:monospace; font-size:13px; white-space:pre-wrap; max-height:400px; overflow-y:auto;">{{ $prompt->content }}</div>
      </div>
    @else
      @if (session('success'))
      <div style="background:#d4edda; color:#155724; padding:12px; border-radius:6px; margin-bottom:20px;">
        {{ session('success') }}
      </div>
      @endif

      <form method="POST" action="{{ route('prompt.update') }}">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label class="form-label">Treść promptu</label>
          <textarea name="content" class="form-textarea" rows="20"
            style="font-family: monospace; font-size: 13px;">{{ $prompt->content }}</textarea>
          @error('content')
          <div style="color:#e74c3c; font-size:13px; margin-top:5px;">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary">Zapisz</button>
        </div>
      </form>
    @endif
  </div>
</x-layout>