<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Vite; // Не забудь импортировать фасад

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Заставляет Laravel игнорировать отсутствие скомпилированного манифеста Vite
        Vite::spy();
    }
}
