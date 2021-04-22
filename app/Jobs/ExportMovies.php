<?php

namespace App\Jobs;

use App\Exports\MoviesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportMovies extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::store(new MoviesExport(), 'movies.xlsx');
    }
}
