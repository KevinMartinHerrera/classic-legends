<?php

namespace App\Console\Commands;

class SyncYupooPage7 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-7';

    protected $description = 'Sync only Yupoo page 7';

    protected function pageNumber(): int
    {
        return 7;
    }
}
