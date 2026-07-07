<?php

namespace App\Filament\Resources\ContentSlides\Pages;

use App\Filament\Resources\ContentSlides\ContentSlideResource;
use Filament\Resources\Pages\ListRecords;

class ListContentSlides extends ListRecords
{
    protected static string $resource = ContentSlideResource::class;
}
