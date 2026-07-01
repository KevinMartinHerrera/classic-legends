<?php

namespace App\Console\Commands;

class SyncYupooPage1 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-1';

    protected $description = 'Sync only Yupoo page 1';

    protected function pageNumber(): int
    {
        return 1;
    }
}
