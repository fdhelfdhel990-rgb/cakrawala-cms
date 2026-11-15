<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Tests\TestCase;

class CloudflareR2DiskTest extends TestCase
{
    public function test_r2_disk_uses_the_flysystem_aws_s3_adapter(): void
    {
        config([
            'filesystems.disks.r2.key' => 'test-access-key',
            'filesystems.disks.r2.secret' => 'test-secret-key',
            'filesystems.disks.r2.region' => 'auto',
            'filesystems.disks.r2.bucket' => 'test-bucket',
            'filesystems.disks.r2.endpoint' => 'https://account-id.r2.cloudflarestorage.com',
        ]);

        Storage::forgetDisk('r2');

        $this->assertInstanceOf(AwsS3V3Adapter::class, Storage::disk('r2')->getAdapter());
    }
}
