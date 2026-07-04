<x-layout>
  <div class="page-header">
    <h1 class="page-title">System Prompt</h1>
  </div>

  <div class="form-card" style="max-width: 900px;">

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
  </div>
</x-layout>