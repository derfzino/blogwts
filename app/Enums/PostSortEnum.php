<?php

namespace App\Enums;

enum PostSortEnum: string
{
    case DATE_ASC = 'date_asc';
    case DATE_DESC = 'date_desc';
    case TITLE_ASC = 'title_asc';
    case TITLE_DESC = 'title_desc';

    public function getOrderBy(): array
    {
        return match ($this) {
            self::DATE_ASC   => ['created_at', 'asc'],
            self::DATE_DESC  => ['created_at', 'desc'],
            self::TITLE_ASC  => ['title', 'asc'],
            self::TITLE_DESC => ['title', 'desc'],
        };
    }
}