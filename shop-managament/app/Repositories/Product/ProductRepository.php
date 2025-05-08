<?php
namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface{

    protected $cloudinaryService;
    public function __construct(Product $model, CloudinaryService $cloudinaryService){
        parent::__construct($model);
        $this->cloudinaryService = $cloudinaryService;
    }

    public function filters($filters)  {
        $query = $this->model->query();

        if (!empty($filters['name'])) {
            $query->where('product_name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('is_sales', $filters['status']);
        }

        if (!empty($filters['priceFrom'])) {
            $query->where('product_price', '>=', $filters['priceFrom']);
        }

        if (!empty($filters['priceTo'])) {
            $query->where('product_price', '<=', $filters['priceTo']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    public function create(array $data){
        try {
            $product = new Product();
            $product->product_id = $this -> generateProductId( $data['name'] );
            $product->product_name = $data['name'];
            $product->product_price = $data['price'];
            $product->description = $data['description'] ?? null;
            $product->is_sales = $data['status'];

            if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
                $imageUrl = $this->cloudinaryService->uploadImage($data['file']);
                if ($imageUrl) {
                    $product->product_image = $imageUrl;
                } else {
                    throw new \Exception('Lỗi upload ảnh');
                }
            }

            $product->save();
            return $product;
        } catch (\Exception $e) {
            Log::error('Lỗi tạo sản phẩm: ' . $e->getMessage());
            return null;
        }

    }

    public function update($id, array $data){
        try {
            $product = Product::where('product_id', $id)->firstOrFail();

            $product->product_name = $data['name'];
            $product->product_price = $data['price'];
            $product->description = $data['description'] ?? $product->description;
            $product->is_sales = $data['status'];

            if (isset($data['file']) && $data['file'] instanceof \Illuminate\Http\UploadedFile) {
                $imageUrl = $this->cloudinaryService->uploadImage($data['file'], 'product_' . $id);
                if ($imageUrl) {              
                    $product->product_image = $imageUrl;
                } else {
                    throw new \Exception('Lỗi upload ảnh');
                }
            } elseif (isset($data['oldImageUrl']) && $data['oldImageUrl'] === '') {
                $product->product_image= null;
            }

            $product->save();
            return $product;
        } catch (\Exception $e) {
            Log::error('Lỗi cập nhật sản phẩm: ' . $e->getMessage());
            return null;
        }
    }

    public function softDelete($id){
        $product = Product::where('product_id', $id)->first();
        $product->update([
            'is_sales' => 0
        ]);
        return true;
    }

    protected function generateProductId($productName)
    {       
        $firstChar = Str::ascii($productName)[0];
        $firstChar = strtoupper($firstChar); 


        $count = Product::where('product_id', 'LIKE', $firstChar . '%')->count();

        $number = str_pad($count + 1, 9, '0', STR_PAD_LEFT);

        return $firstChar . $number;
    }

}
