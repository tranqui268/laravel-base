<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

use Illuminate\Support\Facades\Log;
class CloudinaryService{
    protected $cloudinary;
    public function __construct(){
        $config = [
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key' => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
            'url' => [
                'secure' => true,
            ],
        ];
        Configuration::instance($config);
        $this->cloudinary = new Cloudinary($config);
    }

    public function uploadImage($file, $publicId = null)
    {
        try {
            $checksum = hash_file('sha256', $file->getRealPath());

            // Dùng AdminApi để kiểm tra ảnh trùng lặp
            $adminApi = new AdminApi();
            $existingResources = $adminApi->assets([
                'resource_type' => 'image',
                'max_results' => 100,
            ]);

            foreach ($existingResources['resources'] as $resource) {
                $context = $resource['context']['custom'] ?? [];
                if (!empty($context['checksum']) && $context['checksum'] === $checksum) {
                    Log::info("Ảnh trùng lặp, sử dụng URL hiện có: " . $resource['secure_url']);
                    return $resource['secure_url'];
                }
            }

            // Upload ảnh mới
            $uploadApi = new UploadApi();
            $uploadResult = $uploadApi->upload($file->getRealPath(), [
                'public_id' => $publicId ?? 'product_' . time(),
                'overwrite' => false,
                'context' => ['checksum' => $checksum], // dùng context thay cho metadata
            ]);

            Log::info("Ảnh đã upload lên Cloudinary: " . $uploadResult['secure_url']);
            return $uploadResult['secure_url'];
        } catch (\Exception $e) {
            Log::error('Lỗi upload ảnh lên Cloudinary: ' . $e->getMessage());
            return null;
        }
    }


}