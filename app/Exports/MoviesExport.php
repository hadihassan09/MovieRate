<?php

namespace App\Exports;

use App\Models\Movie;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MoviesExport implements FromCollection, WithHeadings
{

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function collection()
    {
        return Movie::
            with('genres')
            ->with('trailers')
            ->get()
            ->sortByDesc('average_rating')
            ->take(200);
    }

    public function prepareRows($rows): array
    {
        return array_map(function ($row) {
            $genres ='';
            $trailers = '';

            for($i=0; $i < count($row->genres); $i++)
                $genres=$genres.$row->genres[$i]->genre.',';
            for($i=0; $i < count($row->trailers); $i++)
                $trailers=$trailers.$row->trailers[$i]->trailer.',';

            $row->genres = $genres;
            $row->trailers = $trailers;
            $row->created_at = Carbon::parse($row->created_at)->toFormattedDateString();
            $row->updated_at = Carbon::parse($row->updated_at)->toFormattedDateString();

            return $row;
        }, $rows);
    }

    public function headings() : array {
        return [
            '#',
            'Title',
            'Description',
            'Poster Link',
            'Release Date',
            'Created At',
            'Updated At',
            'Genres',
            'Trailers',
            'Average Rating'
        ] ;
    }

}
