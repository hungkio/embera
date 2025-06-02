<?php
declare(strict_types=1);

namespace App\Domain\Taxonomy\DTO;

use App\Http\Requests\Admin\TaxonUpdateRequest;
use Illuminate\Http\UploadedFile;
use Spatie\DataTransferObject\DataTransferObject;

class TaxonUpdateData extends DataTransferObject
{
    public string $name;

    public string $slug;

    public string $description;

    public ?UploadedFile $icon;

    public ?string $meta_title;
    public ?string $meta_title_vi;

    public ?string $meta_description;
    public ?string $meta_description_vi;

    public ?string $meta_keywords;
    public ?string $meta_keywords_vi;

    public ?string $content;
    public ?string $content_bottom;
    public ?string $content_vi;
    public ?string $content_bottom_vi;


    public static function fromRequest(TaxonUpdateRequest $request): TaxonUpdateData
    {
        return new self([
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description') ?? '',
            'icon' => $request->file('icon'),
            'meta_title' => $request->input('meta_title'),
            'meta_title_vi' => $request->input('meta_title_vi'),
            'meta_description' => $request->input('meta_description'),
            'meta_description_vi' => $request->input('meta_description_vi'),
            'meta_keywords' => $request->input('meta_keywords'),
            'meta_keywords_vi' => $request->input('meta_keywords_vi'),
            'content' => $request->input('content'),
            'content_bottom' => $request->input('content_bottom'),
            'content_vi' => $request->input('content_vi'),
            'content_bottom_vi' => $request->input('content_bottom_vi'),
        ]);
    }

}
