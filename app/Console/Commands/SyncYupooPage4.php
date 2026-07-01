<?php

namespace App\Console\Commands;

class SyncYupooPage4 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-4';

    protected $description = 'Sync only Yupoo page 4';

    protected function pageNumber(): int
    {
        return 4;
    }
}
