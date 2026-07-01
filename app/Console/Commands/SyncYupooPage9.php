<?php

namespace App\Console\Commands;

class SyncYupooPage9 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-9';

    protected $description = 'Sync only Yupoo page 9';

    protected function pageNumber(): int
    {
        return 9;
    }
}
