<?php

namespace App\Filament\Admin\Resources\BlogCommentResource\Pages;

use App\Filament\Admin\Resources\BlogCommentResource;
use Filament\Resources\Pages\ListRecords;

class ListBlogComments extends ListRecords
{
    protected static string $resource = BlogCommentResource::class;
}
