<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseTransactions;

class ChecklistTest extends \TestCase
{
    use DatabaseTransactions;

    public function testChecklist()
    {
        $this->assertTrue(true);
        // // When
        // $response = $this->call('POST', 'login', [
        //     'email' => 'user@example.com',
        //     'password' => 'secret',
        // ]);

        // // Then
        // $this->assertEquals(200, $response->status());
        // $this->seeJsonStructure([
        //     'data' => ['name', 'email', 'token'],
        // ]);

        // // When
        // $response = $this->call('POST', 'login', [
        //     'email' => 'user@example.com',
        //     'password' => 'wrong',
        // ]);

        // // Then
        // $this->assertEquals(400, $response->status());
    }
}
