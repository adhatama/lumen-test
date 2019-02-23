<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseTransactions;

class ChecklistTest extends \TestCase
{
    use DatabaseTransactions;

    public function testCRUDChecklist()
    {
        // Given
        $loginData = $this->login('user@example.com', 'secret');

        // When
        $response = $this->post('checklists', [
            'data' => [
                'attributes' => [
                    'object_domain' => 'contact',
                    'object_id' => '1',
                    'due' => '2019-01-25T07:50:14+00:00',
                    'urgency' => 1,
                    'description' => 'Need to verify this guy house.',
                ],
            ],
        ], [
            'Authorization' => $loginData->token,
        ]);

        // Then
        $response->seeStatusCode(201)
            ->seeJsonStructure([
                'data' => ['type', 'id', 'attributes', 'links'],
            ])
            ->seeJson([
                'object_domain' => 'contact',
                'object_id' => '1',
            ]);

        // Given
        $createdData = json_decode($response->response->getContent());

        // When
        $response = $this->get('checklists/'.$createdData->data->id);

        // // Then
        $response->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => ['type', 'id', 'attributes', 'links'],
            ]);

        // Given
        $createdData = json_decode($response->response->getContent());

        // When
        $response = $this->patch('checklists/'.$createdData->data->id, [
            'data' => [
                'type' => 'checklists',
                'id' => '1',
                'attributes' => [
                    'description' => 'The changed checklist',
                ],
            ],
        ], [
            'Authorization' => $loginData->token,
        ]);

        // Then
        $response->seeStatusCode(200)
            ->seeJsonStructure([
                'data' => ['type', 'id', 'attributes', 'links'],
            ])
            ->seeJson([
                'description' => 'The changed checklist',
            ]);

        // Given
        $createdData = json_decode($response->response->getContent());

        // When
        $response = $this->delete('checklists/'.$createdData->data->id, [], [
            'Authorization' => $loginData->token,
        ]);

        // Then
        $response->seeStatusCode(204);
    }
}
