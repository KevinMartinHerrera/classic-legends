<?php

namespace App\Console\Commands;

class SyncYupooPage6 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-6';

    protected $description = 'Sync only Yupoo page 6';

    protected function pageNumber(): int
    {
        return 6;
    }
}
