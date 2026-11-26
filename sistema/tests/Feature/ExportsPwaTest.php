<?php

namespace Tests\Feature;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Reportes en PDF (DomPDF), Excel (maatwebsite/excel) y PWA (erag/laravel-pwa)
class ExportsPwaTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function login(string $email): void
    {
        $this->actingAs(User::where('email', $email)->firstOrFail());
    }

    public function test_report_page_has_export_buttons_with_filters(): void
    {
        $this->login('admin@sanjuan.com');
        $this->get('/reports/sales?user_id=2')->assertOk()
            ->assertSee('Exportar PDF')
            ->assertSee('Exportar Excel')
            ->assertSee(route('reports.sales.pdf', ['user_id' => 2]), false);
    }

    public function test_report_pdf_downloads(): void
    {
        $this->login('admin@sanjuan.com');
        $response = $this->get('/reports/sales/pdf?from=' . now()->subDays(90)->toDateString() . '&to=' . now()->toDateString());

        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
    }

    public function test_report_excel_downloads(): void
    {
        $this->login('rquispe@sanjuan.com');
        $response = $this->get('/reports/sales/excel?from=' . now()->subDays(90)->toDateString());

        $response->assertOk()->assertDownload('reporte-ventas-' . now()->subDays(90)->toDateString() . '-al-' . now()->toDateString() . '.xlsx');
        // Un .xlsx es un archivo zip: empieza con "PK"
        $this->assertStringStartsWith('PK', file_get_contents($response->getFile()->getPathname()));
    }

    public function test_cashier_cannot_export_reports_but_can_print_sale(): void
    {
        $this->login('cmendoza@sanjuan.com');
        $this->get('/reports/sales/pdf')->assertForbidden();
        $this->get('/reports/sales/excel')->assertForbidden();

        $sale = Sale::first();
        $response = $this->get(route('sales.pdf', $sale));
        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_pwa_tags_and_files(): void
    {
        // El login (sin sesión) y la plantilla incluyen el manifest y el service worker
        $this->get('/login')->assertOk()->assertSee('manifest.json')->assertSee('sw.js');

        $this->login('admin@sanjuan.com');
        $this->get('/')->assertOk()->assertSee('rel="manifest"', false)->assertSee('serviceWorker');

        $manifest = json_decode(file_get_contents(public_path('manifest.json')), true);
        $this->assertSame('FARM SJB', $manifest['short_name']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('offline.html'));
        $this->assertFileExists(public_path('logo.png'));
    }
}
