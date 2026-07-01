<?php

namespace App\Console\Commands;

class SyncYupooPage2 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-2';

    protected $description = 'Sync only Yupoo page 2';

    protected function pageNumber(): int
    {
        return 2;
    }
}
