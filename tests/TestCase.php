<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function login($email, $password)
    {
        $response = $this->call('POST', 'login', [
            'email' => $email,
            'password' => $password,
        ]);

        return json_decode($response->getContent())->data;
    }
}
