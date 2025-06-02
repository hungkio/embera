<select class="form-control w-auto" name="section">
    <option value="{{ \App\Enums\BannerSection::Slide }}" {{ $section == \App\Enums\BannerSection::Slide ? 'selected' : '' }}>{{ __('Slide') }}</option>
</select>
