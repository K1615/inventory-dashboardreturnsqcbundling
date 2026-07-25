<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function erpPageProvider(): array
    {
        return [
            'dashboard' => ['/'],
            'alerts and reorders' => ['/alerts'],
            'inventory items' => ['/items'],
            'stock movements' => ['/movement'],
            'warehouse layout' => ['/warehouse'],
        ];
    }

    #[DataProvider('erpPageProvider')]
    public function test_guests_are_redirected_from_every_erp_page(string $uri): void
    {
        $this->get($uri)->assertRedirect('/login');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function dataEndpointProvider(): array
    {
        return [
            'dashboard state' => ['/inventory/api/state'],
            'alerts summary' => ['/inventory/api/alerts-summary'],
            'stock movement data' => ['/api/stock-movements/data'],
            'warehouse data' => ['/warehouse-layout/data'],
        ];
    }

    #[DataProvider('dataEndpointProvider')]
    public function test_guests_receive_unauthorized_json_from_data_endpoints(string $uri): void
    {
        $this->getJson($uri)->assertUnauthorized();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function mutationEndpointProvider(): array
    {
        return [
            'inventory request' => ['POST', '/api/requests'],
            'inventory resolve' => ['POST', '/api/requests/1/resolve'],
            'stock movement create' => ['POST', '/api/stock-movements'],
            'stock movement resolve' => ['PATCH', '/api/stock-movements/TX-1/status'],
            'inspection' => ['POST', '/inventory/api/inspection'],
            'rma' => ['POST', '/inventory/api/rma'],
            'return resolution' => ['POST', '/inventory/api/resolve-return'],
            'bundle request' => ['POST', '/inventory/api/bundle'],
            'bundle resolution' => ['POST', '/inventory/api/resolve-bundle'],
            'item limits' => ['POST', '/inventory/api/limits/P001'],
            'auto reorder' => ['POST', '/inventory/api/auto-reorder/P001'],
            'manual purchase order' => ['POST', '/inventory/api/submit-po'],
            'draft submit' => ['POST', '/inventory/api/draft/1/submit'],
            'draft discard' => ['POST', '/inventory/api/draft/1/discard'],
            'purchase order decision' => ['POST', '/inventory/api/pipeline/1'],
            'purchase order receipt' => ['POST', '/inventory/api/pipeline/1/receive'],
            'single transfer' => ['POST', '/warehouse-layout/request'],
            'batch transfer' => ['POST', '/warehouse-layout/batch-request'],
            'transfer decision' => ['POST', '/warehouse-layout/process/1'],
        ];
    }

    #[DataProvider('mutationEndpointProvider')]
    public function test_guests_cannot_execute_any_mutation_category(string $method, string $uri): void
    {
        $this->json($method, $uri)->assertUnauthorized();
    }

    #[DataProvider('erpPageProvider')]
    public function test_authenticated_users_can_access_every_erp_page(string $uri): void
    {
        $this->actingAs(User::factory()->create())
            ->get($uri)
            ->assertOk();
    }

    #[DataProvider('dataEndpointProvider')]
    public function test_authenticated_users_can_access_existing_data_endpoints(string $uri): void
    {
        $this->actingAs(User::factory()->create())
            ->getJson($uri)
            ->assertOk();
    }
}
