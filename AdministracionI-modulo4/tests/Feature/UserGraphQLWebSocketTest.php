<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rol;
use App\Events\UserCreado;
use App\Events\UserActualizado;
use App\Events\UserEliminado;
use Illuminate\Support\Facades\Hash;

/**
 * Tests completos para USER: GraphQL CRUD + WebSocket Events
 */
class UserGraphQLWebSocketTest extends GraphQLWebSocketTestCase
{
    /**
     * @test
     * Test: Crear usuario via GraphQL + verificar evento WebSocket
     */
    public function test_crear_usuario_graphql_y_websocket()
    {
        $testData = $this->createTestData()['user'];
        
        $mutation = '
            mutation CrearUser($input: UserInput!) {
                crearUser(input: $input) {
                    id_usuario
                    empleado_id
                    username
                    rol_id
                    created_at
                    updated_at
                }
            }
        ';

        $response = $this->executeMutation($mutation, ['input' => $testData]);

        // Verificar respuesta GraphQL
        $this->assertGraphQLSuccess($response, 'crearUser');
        
        $responseData = $response->json('data.crearUser');
        $this->assertEquals($testData['empleado_id'], $responseData['empleado_id']);
        $this->assertEquals($testData['username'], $responseData['username']);
        $this->assertEquals($testData['rol_id'], $responseData['rol_id']);
        $this->assertNotNull($responseData['id_usuario']);

        // Verificar base de datos
        $this->assertDatabaseHas('users', [
            'empleado_id' => $testData['empleado_id'],
            'username' => $testData['username']
        ]);

        // Verificar que la contraseña está hasheada
        $user = User::where('username', $testData['username'])->first();
        $this->assertTrue(Hash::check($testData['password'], $user->password));

        // Verificar evento WebSocket
        $this->assertEventDispatched(UserCreado::class);
        
        $this->assertBroadcastEventData(UserCreado::class, [
            'entity_type' => 'usuario',
            'action' => 'created',
            'type' => 'usuario.creado'
        ]);

        $this->assertBroadcastChannels(UserCreado::class, ['usuarios', 'dashboard']);
    }

    /**
     * @test
     * Test: Leer usuarios via GraphQL
     */
    public function test_leer_usuarios_graphql()
    {
        // Crear usuarios con roles
        $rol1 = Rol::factory()->create(['nombre' => 'Admin']);
        $rol2 = Rol::factory()->create(['nombre' => 'Usuario']);
        
        $user1 = User::factory()->create(['rol_id' => $rol1->id_rol]);
        $user2 = User::factory()->create(['rol_id' => $rol2->id_rol]);

        // Query individual con relaciones
        $query = '
            query ObtenerUsuario($id: ID!) {
                user(id_usuario: $id) {
                    id_usuario
                    empleado_id
                    username
                    rol_id
                    rol {
                        id_rol
                        nombre
                        descripcion
                    }
                    empleado {
                        nombre
                        cargo
                        correo
                    }
                }
            }
        ';

        $response = $this->executeQuery($query, ['id' => $user1->id_usuario]);
        $this->assertGraphQLSuccess($response, 'user');
        
        $responseData = $response->json('data.user');
        $this->assertEquals($user1->username, $responseData['username']);
        $this->assertEquals($user1->empleado_id, $responseData['empleado_id']);
        $this->assertEquals('Admin', $responseData['rol']['nombre']);

        // Query lista completa
        $queryLista = '
            query ObtenerUsuarios {
                users {
                    id_usuario
                    username
                    empleado_id
                    rol {
                        nombre
                    }
                }
            }
        ';

        $responseLista = $this->executeQuery($queryLista);
        $this->assertGraphQLSuccess($responseLista, 'users');
        
        $users = $responseLista->json('data.users');
        $this->assertCount(2, $users);
        
        // Verificar que ambos usuarios tienen roles
        foreach ($users as $userData) {
            $this->assertNotNull($userData['rol']);
            $this->assertNotEmpty($userData['rol']['nombre']);
        }
    }

    /**
     * @test
     * Test: Actualizar usuario via GraphQL + WebSocket
     */
    public function test_actualizar_usuario_graphql_y_websocket()
    {
        $user = User::factory()->create();
        
        $updateData = [
            'empleado_id' => $user->empleado_id, // Mantener el mismo empleado
            'username' => 'usuario_actualizado',
            'password' => 'newpassword123',
            'rol_id' => $user->rol_id // Mantener el mismo rol
        ];

        $mutation = '
            mutation ActualizarUser($id: ID!, $input: UserInput!) {
                actualizarUser(id_usuario: $id, input: $input) {
                    id_usuario
                    username
                    empleado_id
                    updated_at
                }
            }
        ';

        $variables = ['id' => $user->id_usuario, 'input' => $updateData];
        $response = $this->executeMutation($mutation, $variables);

        // Verificar respuesta
        $this->assertGraphQLSuccess($response, 'actualizarUser');
        
        $responseData = $response->json('data.actualizarUser');
        $this->assertEquals($updateData['username'], $responseData['username']);
        $this->assertEquals($user->empleado_id, $responseData['empleado_id']);

        // Verificar base de datos
        $this->assertDatabaseHas('users', [
            'id_usuario' => $user->id_usuario,
            'username' => $updateData['username'],
            'empleado_id' => $user->empleado_id
        ]);

        // Verificar nueva contraseña
        $userUpdated = User::find($user->id_usuario);
        $this->assertTrue(Hash::check($updateData['password'], $userUpdated->password));

        // Verificar evento WebSocket
        $this->assertEventDispatched(UserActualizado::class);
        
        $this->assertBroadcastEventData(UserActualizado::class, [
            'entity_type' => 'usuario',
            'action' => 'updated',
            'type' => 'usuario.actualizado'
        ]);
    }

    /**
     * @test
     * Test: Eliminar usuario via GraphQL + WebSocket
     */
    public function test_eliminar_usuario_graphql_y_websocket()
    {
        $user = User::factory()->create();
        $userId = $user->id_usuario;

        $mutation = '
            mutation EliminarUser($id: ID!) {
                eliminarUser(id_usuario: $id) {
                    success
                    message
                }
            }
        ';

        $response = $this->executeMutation($mutation, ['id' => $userId]);

        // Verificar respuesta
        $this->assertGraphQLSuccess($response, 'eliminarUser');
        
        $responseData = $response->json('data.eliminarUser');
        $this->assertTrue($responseData['success']);
        $this->assertStringContainsString('eliminado', $responseData['message']);

        // Verificar eliminación
        $this->assertDatabaseMissing('users', [
            'id_usuario' => $userId
        ]);

        // Verificar evento WebSocket
        $this->assertEventDispatched(UserEliminado::class);
        
        $this->assertBroadcastEventData(UserEliminado::class, [
            'entity_type' => 'usuario',
            'action' => 'deleted',
            'type' => 'usuario.eliminado'
        ]);
    }

    /**
     * @test
     * Test: Validaciones de usuario
     */
    public function test_validaciones_usuario_graphql()
    {
        $rol = Rol::factory()->create();
        
        $mutation = '
            mutation CrearUsuario($input: UserInput!) {
                crearUser(input: $input) {
                    id
                    name
                }
            }
        ';

        // Test: Email duplicado
        $userExistente = User::factory()->create(['rol_id' => $rol->id_rol]);
        
        $response = $this->executeMutation($mutation, [
            'input' => [
                'name' => 'Test User',
                'email' => $userExistente->email,
                'password' => 'password123',
                'rol_id' => $rol->id_rol
            ]
        ]);

        $this->assertGraphQLError($response);

        // Test: Email inválido
        $response2 = $this->executeMutation($mutation, [
            'input' => [
                'name' => 'Test User',
                'email' => 'email-invalido',
                'password' => 'password123',
                'rol_id' => $rol->id_rol
            ]
        ]);

        $this->assertGraphQLError($response2);

        // Test: Contraseña muy corta
        $response3 = $this->executeMutation($mutation, [
            'input' => [
                'name' => 'Test User',
                'email' => 'test@new.com',
                'password' => '123', // Muy corta
                'rol_id' => $rol->id_rol
            ]
        ]);

        $this->assertGraphQLError($response3);

        // Test: Rol inexistente
        $response4 = $this->executeMutation($mutation, [
            'input' => [
                'name' => 'Test User',
                'email' => 'test@rol.com',
                'password' => 'password123',
                'rol_id' => 999999 // ID inexistente
            ]
        ]);

        $this->assertGraphQLError($response4);

        // Verificar que no se dispararon eventos para mutaciones fallidas
        $this->assertEventNotDispatched(UserCreado::class);
    }

    /**
     * @test
     * Test: Usuarios por rol
     */
    public function test_usuarios_por_rol_graphql()
    {
        // Crear roles y usuarios
        $rolAdmin = Rol::factory()->create(['nombre' => 'Administrador']);
        $rolUser = Rol::factory()->create(['nombre' => 'Usuario Regular']);
        
        User::factory()->count(2)->create(['rol_id' => $rolAdmin->id_rol]);
        User::factory()->count(3)->create(['rol_id' => $rolUser->id_rol]);

        $query = '
            query {
                users {
                    id_usuario
                    username
                    empleado_id
                    rol {
                        id_rol
                        nombre
                    }
                }
            }
        ';

        $response = $this->executeQuery($query);
        $this->assertGraphQLSuccess($response, 'users');
        
        $users = $response->json('data.users');
        $this->assertCount(5, $users);

        // Verificar distribución por roles
        $admins = collect($users)->filter(function ($user) {
            return $user['rol']['nombre'] === 'Administrador';
        });
        
        $regulares = collect($users)->filter(function ($user) {
            return $user['rol']['nombre'] === 'Usuario Regular';
        });

        $this->assertCount(2, $admins);
        $this->assertCount(3, $regulares);
    }

    /**
     * @test
     * Test: Autenticación y seguridad de contraseñas
     */
    public function test_seguridad_contrasenas_usuario()
    {
        $testData = $this->createTestData()['user'];
        
        $mutation = '
            mutation CrearUsuario($input: UserInput!) {
                crearUser(input: $input) {
                    id_usuario
                    username
                    empleado_id
                }
            }
        ';

        $response = $this->executeMutation($mutation, ['input' => $testData]);
        $this->assertGraphQLSuccess($response, 'crearUser');

        // Verificar que la contraseña no se devuelve en la respuesta
        $responseData = $response->json('data.crearUser');
        $this->assertArrayNotHasKey('password', $responseData);

        // Verificar que la contraseña está correctamente hasheada en BD
        $user = User::where('username', $testData['username'])->first();
        $this->assertNotEquals($testData['password'], $user->password);
        $this->assertTrue(Hash::check($testData['password'], $user->password));

        // Verificar longitud del hash
        $this->assertGreaterThan(50, strlen($user->password));
    }

    /**
     * @test
     * Test: Actualización parcial de usuario
     */
    public function test_actualizacion_parcial_usuario()
    {
        $user = User::factory()->create();
        $originalUsername = $user->username;
        $originalPassword = $user->password;

        // Actualizar solo el username
        $mutation = '
            mutation ActualizarUsuario($id: ID!, $input: UserInput!) {
                actualizarUser(id_usuario: $id, input: $input) {
                    id_usuario
                    username
                    empleado_id
                }
            }
        ';

        $response = $this->executeMutation($mutation, [
            'id' => $user->id_usuario,
            'input' => [
                'empleado_id' => $user->empleado_id, // Requerido
                'username' => 'username_actualizado',
                'password' => $originalPassword, // Mantener la misma contraseña
                'rol_id' => $user->rol_id // Requerido
            ]
        ]);

        $this->assertGraphQLSuccess($response, 'actualizarUser');
        
        $responseData = $response->json('data.actualizarUser');
        $this->assertEquals('username_actualizado', $responseData['username']);
        $this->assertEquals($user->empleado_id, $responseData['empleado_id']);

        // Verificar que empleado_id y rol no cambiaron
        $userUpdated = User::where('id_usuario', $user->id_usuario)->first();
        $this->assertEquals($user->empleado_id, $userUpdated->empleado_id);
        $this->assertEquals($user->rol_id, $userUpdated->rol_id);

        // Verificar evento
        $this->assertEventDispatched(UserActualizado::class);
    }
}
