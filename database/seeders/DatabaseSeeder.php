<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Discount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@shopdo3.com',
            'password' => bcrypt('password'),
            'phone' => '0987654321',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create regular user
        User::create([
            'name' => 'Test User',
            'email' => 'user@shopdo3.com',
            'password' => bcrypt('password'),
            'phone' => '0912345678',
            'role' => 'user',
            'is_active' => true,
        ]);

        // Create categories
        $categories = [
            ['name' => 'Điện tử', 'slug' => 'dien-tu', 'description' => 'Các sản phẩm điện tử'],
            ['name' => 'Quần áo', 'slug' => 'quan-ao', 'description' => 'Quần áo nam nữ'],
            ['name' => 'Sách', 'slug' => 'sach', 'description' => 'Các cuốn sách hay'],
            ['name' => 'Thực phẩm', 'slug' => 'thuc-pham', 'description' => 'Thực phẩm tươi ngon'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Create products
        $products_data = [
            ['name' => 'Laptop Dell XPS 13', 'category' => 1, 'price' => 20000000, 'stock' => 10, 'featured' => true],
            ['name' => 'iPhone 15 Pro', 'category' => 1, 'price' => 25000000, 'stock' => 15, 'featured' => true],
            ['name' => 'Áo thun nam cao cấp', 'category' => 2, 'price' => 250000, 'stock' => 50, 'featured' => false],
            ['name' => 'Quần jeans nam', 'category' => 2, 'price' => 400000, 'stock' => 40, 'featured' => false],
            ['name' => 'Sách Làm Giàu Không Có Bí Mật', 'category' => 3, 'price' => 120000, 'stock' => 100, 'featured' => true],
            ['name' => 'Lúa mạch nguyên hạt', 'category' => 4, 'price' => 50000, 'stock' => 200, 'featured' => false],
        ];

        foreach ($products_data as $prod) {
            $category_id = $prod['category'];
            $product = Product::create([
                'name' => $prod['name'],
                'slug' => Str::slug($prod['name']),
                'description' => 'Mô tả chi tiết về ' . $prod['name'],
                'category_id' => $category_id,
                'price' => $prod['price'],
                'discount_price' => $prod['price'] * 0.9,
                'stock' => $prod['stock'],
                'is_featured' => $prod['featured'],
                'is_active' => true,
            ]);

            // Create a dummy image path
            ProductImage::create([
                'product_id' => $product->id,
                'image' => 'products/dummy.jpg',
                'sort_order' => 0,
            ]);
        }

        // Create discount codes
        Discount::create([
            'code' => 'PROMO10',
            'description' => 'Giảm 10% cho đơn hàng',
            'discount_type' => 1, // percentage
            'discount_value' => 10,
            'min_order_value' => 100000,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        Discount::create([
            'code' => 'SAVE50000',
            'description' => 'Giảm 50000 đồng',
            'discount_type' => 2, // fixed amount
            'discount_value' => 50000,
            'min_order_value' => 500000,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);
    }
}

