<?php

namespace App\Console\Commands;

class SyncYupooPage5 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-5';

    protected $description = 'Sync only Yupoo page 5';

    protected function pageNumber(): int
    {
        return 5;
    }
}
