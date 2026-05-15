<?php
// app/Services/ProductService.php

namespace App\Services;

use App\Contracts\Services\ProductServiceInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    protected $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllProducts(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->all($filters);
    }

    public function getProductById(int $id): ?Product
    {
        return $this->repository->findById($id);
    }

    public function getProductBySlug(string $slug): ?Product
    {
        return $this->repository->findBySlug($slug);
    }

    public function createProduct(array $data): Product
    {
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = json_encode($this->uploadImages($data['images']));
        }

        if (isset($data['main_image']) && $data['main_image'] instanceof UploadedFile) {
            $data['main_image'] = $this->uploadSingleImage($data['main_image'], 'products/main');
        }

        if (isset($data['specifications']) && is_array($data['specifications'])) {
            $data['specifications'] = json_encode($data['specifications']);
        }

        $threshold = $data['low_stock_threshold'] ?? 10;
        $data['is_low_stock'] = $data['stock_quantity'] <= $threshold;

        $product = $this->repository->create($data);

        if (isset($data['variants']) && is_array($data['variants'])) {
            $this->syncVariants($product->id, $data['variants']);
        }

        return $product->load('variants');
    }

    public function updateProduct(int $id, array $data): ?Product
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            return null;
        }

        if (isset($data['delete_images']) && is_array($data['delete_images'])) {
            $this->deleteImages($data['delete_images']);
        }

        $finalImages = [];

        if (isset($data['existing_images']) && is_array($data['existing_images'])) {
            $finalImages = $data['existing_images'];
        } elseif ($product->images) {
            $finalImages = json_decode($product->images, true) ?? [];
        }

        if (isset($data['images']) && is_array($data['images'])) {
            $newImages = $this->uploadImages($data['images']);
            $finalImages = array_merge($finalImages, $newImages);
        }

        $data['images'] = !empty($finalImages) ? json_encode($finalImages) : null;

        if (isset($data['main_image']) && $data['main_image'] instanceof UploadedFile) {
            if ($product->main_image) {
                $this->deleteSingleImage($product->main_image);
            }
            $data['main_image'] = $this->uploadSingleImage($data['main_image'], 'products/main');
        }

        if (isset($data['specifications']) && is_array($data['specifications'])) {
            $data['specifications'] = json_encode($data['specifications']);
        }

        if (isset($data['stock_quantity'])) {
            $threshold = $data['low_stock_threshold'] ?? $product->low_stock_threshold ?? 10;
            $data['is_low_stock'] = $data['stock_quantity'] <= $threshold;
        }

        $product = $this->repository->update($id, $data);

        if (isset($data['variants']) && is_array($data['variants'])) {
            $this->syncVariants($product->id, $data['variants']);
        }

        return $product->load('variants');
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            return false;
        }

        if ($product->images) {
            $images = json_decode($product->images, true);
            if (!empty($images)) {
                $this->deleteImages($images);
            }
        }

        if ($product->main_image) {
            $this->deleteSingleImage($product->main_image);
        }

        ProductVariant::where('product_id', $id)->delete();

        return $this->repository->delete($id);
    }

    public function updateProductStock(int $id, int $quantity): ?Product
    {
        return $this->repository->updateStock($id, $quantity);
    }

    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->repository->getFeatured($limit);
    }

    public function getLowStockProducts(): Collection
    {
        return $this->repository->getLowStock();
    }

    public function getRelatedProducts(int $productId, int $limit = 5): Collection
    {
        return $this->repository->getRelated($productId, $limit);
    }

    // ========== PRIVATE METHODS ==========

    private function uploadImages(array $images): array
    {
        $uploaded = [];

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $uploaded[] = $this->uploadSingleImage($image, 'products');
            }
        }

        return $uploaded;
    }

    private function uploadSingleImage(UploadedFile $image, string $folder): string
    {
        $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs($folder, $filename, 'public');
        return Storage::url($path);
    }

    private function deleteImages(array $imagePaths): void
    {
        foreach ($imagePaths as $path) {
            $this->deleteSingleImage($path);
        }
    }

    private function deleteSingleImage(string $imagePath): void
    {
        try {
            $relativePath = str_replace('/storage/', '', $imagePath);
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        } catch (\Exception $e) {
            // silent fail
        }
    }

    private function syncVariants(int $productId, array $variants): void
    {
        $existingIds = [];

        foreach ($variants as $variantData) {
            if (isset($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
                if ($variant && $variant->product_id === $productId) {
                    $variant->update([
                        'name' => $variantData['name'],
                        'attributes' => json_encode($variantData['attributes'] ?? []),
                        'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                        'stock_quantity' => $variantData['stock_quantity'],
                    ]);
                    $existingIds[] = $variant->id;
                }
            } else {
                $variant = ProductVariant::create([
                    'product_id' => $productId,
                    'name' => $variantData['name'],
                    'attributes' => json_encode($variantData['attributes'] ?? []),
                    'price_adjustment' => $variantData['price_adjustment'] ?? 0,
                    'stock_quantity' => $variantData['stock_quantity'],
                ]);
                $existingIds[] = $variant->id;
            }
        }

        ProductVariant::where('product_id', $productId)
            ->whereNotIn('id', $existingIds)
            ->delete();
    }
}