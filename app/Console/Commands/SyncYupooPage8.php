<?php

namespace App\Console\Commands;

class SyncYupooPage8 extends SyncYupooPage
{
    protected $signature = 'yupoo:page-8';

    protected $description = 'Sync only Yupoo page 8';

    protected function pageNumber(): int
    {
        return 8;
    }
}
