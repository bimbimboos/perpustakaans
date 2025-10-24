<?php
// tests/Unit/StorageConfigTest.php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class StorageConfigTest extends TestCase
{
    public function test_private_disk_is_configured()
    {
        $this->assertTrue(array_key_exists('private', config('filesystems.disks')));
    }

    public function test_can_store_and_retrieve_from_private_disk()
    {
        $content = 'test content';
        $path = 'test/file.txt';

        Storage::disk('private')->put($path, $content);

        $this->assertTrue(Storage::disk('private')->exists($path));
        $this->assertEquals($content, Storage::disk('private')->get($path));

        // Cleanup
        Storage::disk('private')->delete($path);
    }

    public function test_private_files_not_publicly_accessible()
    {
        $path = 'test/private.txt';
        Storage::disk('private')->put($path, 'secret');

        // Try to access via URL (should fail)
        $response = $this->get('/storage/' . $path);
        $response->assertStatus(404);

        // Cleanup
        Storage::disk('private')->delete($path);
    }
}
