<?php

namespace App\Console\Commands;

class SyncYupooPage3 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-3';

    protected $description = 'Sync only Yupoo page 3';

    protected function pageNumber(): int
    {
        return 3;
    }
}
