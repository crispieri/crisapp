<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Product;
use App\Models\Category;
use App\Enums\ClientType;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456')
        ]);

        // Crear un usuario
        $user = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan.perez@example.com',
            'password' => bcrypt('password'),
        ]);

        // Crear un perfil de cliente
        $user->clientProfile()->create([
            'client_type' => 'individual',
            'rut' => '12345678-9',
        ]);

        // Crear varias direcciones para el usuario
        $user->addresses()->createMany([
            [
                'street_address' => 'Av. Siempre Viva 123',
                'commune' => 'Providencia',
                'city' => 'Santiago',
                'region' => 'Metropolitana',
            ],
            [
                'street_address' => 'Av. Libertador 456',
                'commune' => 'Las Condes',
                'city' => 'Santiago',
                'region' => 'Metropolitana',
            ],
        ]);

        // Crea 10 usuarios
        // User::factory(10)->create();

        // Crea 5 marcas
        Brand::factory(5)->create();

        // Crea 5 categorías
        Category::factory(5)->create();

        Coupon::factory()->create();

        // Crear 5 cupones de tipo porcentaje
        Coupon::factory()->count(5)->percentage()->create();

        // Crear 5 cupones de tipo fijo
        Coupon::factory()->count(5)->fixed()->create();
        // Crea 50 productos
        Product::factory(50)
            ->create([
                'category_id' => Category::factory()->create()->id, // Asigna una categoría aleatoria
                'brand_id' => Brand::factory()->create()->id, // Asigna una marca aleatoria
            ]);

        // Crea 20 órdenes
        Order::factory(20)
            ->has(OrderItem::factory()->count(3)) // Cada orden tendrá 3 items
            ->create();

        // Crea 10 direcciones
        // Address::factory(10)->create();

        // Crea 5 tiendas
        Store::factory(5)->create();

        // Crea 10 pagos
        // Payment::factory(10)->create();
    }
}
