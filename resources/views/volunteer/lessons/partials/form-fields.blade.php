@php
    $currentDelivery = old('delivery_mode', $lesson?->delivery_mode ?? 'youtube_link');
@endphp

<div class="col-md-8">
    <label class="form-label fw-semibold">Title</label>
    <input type="text" name="title" value="{{ old('title', $lesson?->title) }}" class="form-control @error('title') is-invalid @enderror" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-4">
    <label class="form-label fw-semibold">Subject</label>
    <input type="text" name="subject" value="{{ old('subject', $lesson?->subject) }}" class="form-control @error('subject') is-invalid @enderror" placeholder="Math, English, Science" required>
    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">Grade Min</label>
    <input type="number" name="grade_min" min="1" max="12" value="{{ old('grade_min', $lesson?->grade_min) }}" class="form-control @error('grade_min') is-invalid @enderror" required>
    @error('grade_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">Grade Max</label>
    <input type="number" name="grade_max" min="1" max="12" value="{{ old('grade_max', $lesson?->grade_max) }}" class="form-control @error('grade_max') is-invalid @enderror" required>
    @error('grade_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">Lesson Type</label>
    <select name="lesson_type" class="form-select @error('lesson_type') is-invalid @enderror" required>
        <option value="">Select</option>
        <option value="recorded" @selected(old('lesson_type', $lesson?->lesson_type) === 'recorded')>Recorded</option>
        <option value="live" @selected(old('lesson_type', $lesson?->lesson_type) === 'live')>Live</option>
        <option value="worksheet" @selected(old('lesson_type', $lesson?->lesson_type) === 'worksheet')>Worksheet</option>
    </select>
    @error('lesson_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">Language</label>
    <input type="text" name="language" value="{{ old('language', $lesson?->language ?? 'English') }}" class="form-control @error('language') is-invalid @enderror" required>
    @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">Duration (minutes)</label>
    <input type="number" name="duration_minutes" min="1" value="{{ old('duration_minutes', $lesson?->duration_minutes) }}" class="form-control @error('duration_minutes') is-invalid @enderror">
    @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-9">
    <label class="form-label fw-semibold">Delivery Mode</label>
    <select name="delivery_mode" id="delivery_mode" class="form-select @error('delivery_mode') is-invalid @enderror" required>
        <option value="">Select</option>
        <option value="youtube_link" @selected($currentDelivery === 'youtube_link')>YouTube link</option>
        <option value="video_upload" @selected($currentDelivery === 'video_upload')>Upload video (mp4, mov)</option>
        <option value="document_upload" @selected($currentDelivery === 'document_upload')>Upload document (pdf, doc, docx)</option>
    </select>
    @error('delivery_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-12" id="youtube_wrap">
    <label class="form-label fw-semibold">YouTube URL</label>
    <input type="url" name="youtube_url" value="{{ old('youtube_url', $lesson?->youtube_url) }}" class="form-control @error('youtube_url') is-invalid @enderror" placeholder="https://www.youtube.com/watch?v=...">
    @error('youtube_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-12" id="video_wrap">
    <label class="form-label fw-semibold">Video File</label>
    <input type="file" name="video" accept=".mp4,.mov,video/mp4,video/quicktime" class="form-control @error('video') is-invalid @enderror">
    @error('video')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if($mode === 'edit' && $lesson?->video_path)
        <div class="form-text">Current: {{ $lesson->video_path }}</div>
    @endif
</div>

<div class="col-12" id="document_wrap">
    <label class="form-label fw-semibold">Document File</label>
    <input type="file" name="document" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="form-control @error('document') is-invalid @enderror">
    @error('document')<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if($mode === 'edit' && $lesson?->document_path)
        <div class="form-text">Current: {{ $lesson->document_path }}</div>
    @endif
</div>

<div class="col-12">
    <label class="form-label fw-semibold">Description</label>
    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $lesson?->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-12">
    <label class="form-label fw-semibold">Learning Objectives</label>
    <textarea name="learning_objectives" rows="3" class="form-control @error('learning_objectives') is-invalid @enderror">{{ old('learning_objectives', $lesson?->learning_objectives) }}</textarea>
    @error('learning_objectives')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<script>
    (() => {
        const mode = document.getElementById('delivery_mode');
        const youtubeWrap = document.getElementById('youtube_wrap');
        const videoWrap = document.getElementById('video_wrap');
        const documentWrap = document.getElementById('document_wrap');

        if (!mode || !youtubeWrap || !videoWrap || !documentWrap) return;

        const sync = () => {
            const value = mode.value;
            youtubeWrap.style.display = value === 'youtube_link' ? '' : 'none';
            videoWrap.style.display = value === 'video_upload' ? '' : 'none';
            documentWrap.style.display = value === 'document_upload' ? '' : 'none';
        };

        mode.addEventListener('change', sync);
        sync();
    })();
</script>
