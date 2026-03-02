<div class="form-group">
    <label for="category_id">Select Category / Board</label>
    <select name="category_id" id="category_id" class="form-control" required>
        <option value="">Select Category</option>
        @if (!empty($getCategories) && count($getCategories) > 0)
            @foreach ($getCategories as $category)
                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
            @endforeach
        @endif
    </select>
</div>
