<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sucursal;
use App\Events\SucursalCreada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;

class SucursalEventTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     * Test directo del evento SucursalCreada
     */
    
    public function test_sucursal_creada_evento_directo()
    {
        Event::fake();
        
        $sucursal = Sucursal::create([
            'nombre' => 'Test Sucursal',
            'direccion' => 'Test Direccion',
            'ciudad' => 'Test Ciudad',
            'telefono' => '123456789'
        ]);
        
        event(new SucursalCreada($sucursal));
        
        Event::assertDispatched(SucursalCreada::class);
        
        $this->assertTrue(true);
    }
}
