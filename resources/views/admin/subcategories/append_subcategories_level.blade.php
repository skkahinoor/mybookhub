<div class="form-group">
    <label for="subcategory_id">Select Subcategory</label>
    <select name="subcategory_id" id="subcategory_id" class="form-control" style="color: #000">
        <option value="">Select Subcategory</option>
        @if (!empty($getSubcategories))
            @foreach ($getSubcategories as $subcategory)
                <option value="{{ $subcategory['id'] }}" @if (
                    (isset($selected_id) && $selected_id == $subcategory['id']) ||
                        (isset($subject) && !empty($subject['subcategory_id']) && $subject['subcategory_id'] == $subcategory['id'])) selected @endif>
                    {{ $subcategory['subcategory_name'] }}
                </option>
            @endforeach
        @endif
    </select>
</div>
