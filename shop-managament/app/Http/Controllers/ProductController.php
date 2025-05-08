<?php

namespace App\Http\Controllers;

use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $productRepository;
    public function __construct(ProductRepositoryInterface $productRepository){
        $this->productRepository = $productRepository;
    }

    public function index(Request $request)
    {
        if ($request->has('productId')) {
            return view('products.detail'); 
        }
        return view('products.index'); 
    }

    public function getProduct($id){     
        $product = $this->productRepository->getById($id);
        if(!$product){
            return response()->json([
                'success'=> false,
                'error' => 'Lỗi khi lấy dữ liệu'
            ], 500);
        }else{
            return response()->json([
                'success'=> true,
                'message' => 'successfully',
                'data'=> $product
                ],200);
        }
    }

    public function showDetail(Request $request){
        return view('products.detail');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([              
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'status' => 'required|boolean',
                'file' => 'nullable|image|mimes:jpeg,png|max:2048',
            ]);

            $product = $this->productRepository->create($data);

            if ($product) {
                return response()->json([
                    'success'=> true,
                    'message' => 'Sản phẩm đã được thêm'
                ], 201);
            }
            return response()->json(['error' => 'Lỗi khi tạo sản phẩm'], 500);
        } catch (\Exception $e) {
            Log::error('Lỗi trong store: ' . $e->getMessage());
            return response()->json(['success'=> false,'error' => 'Lỗi khi tạo sản phẩm: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Dữ liệu nhận được', $request->all());

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'status' => 'required|boolean',
                'file' => 'nullable|image|mimes:jpeg,png|max:2048',
            ]);

            $product = $this->productRepository->update($id, $data);

            if ($product) {
                return response()->json(['success'=> true,'message' => 'Sản phẩm đã được cập nhật']);
            }
            return response()->json(['error' => 'Lỗi khi cập nhật sản phẩm'], 500);
        } catch (\Exception $e) {
            Log::error('Lỗi trong update: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success'=> false,'error' => 'Lỗi khi cập nhật sản phẩm: ' . $e->getMessage()], 500);
        }
    }

    public function getAllProduct(Request $request){
        try {
            $products = $this->productRepository->filters($request);
            return response()->json([
                'data' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'page_size' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ]
            ]);           
        } catch (\Exception $e) {
            Log::error('Error get data product: ' . $e->getMessage());
            return response()->json([
                'success'=> false,
                'error' => 'Lỗi khi lấy dữ liệu'
            ], 500);
        }

    }

    public function softDeleteProduct($id){
        try {
            $this->productRepository->softDelete($id);
            return response()->json([
                'success' => true,
                'message'=> 'Update successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error delete product: ' . $e->getMessage());
            return response()->json([
                'success'=> false,
                'error' => 'Lỗi khi xóa'
            ], 500);
        }
    }
       
}
