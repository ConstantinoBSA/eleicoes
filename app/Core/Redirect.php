<?php

namespace App\Core;

class Redirect
{
    // Redireciona para uma URL específica
    public function to($url, $statusCode = 302, $headers = [])
    {
        $this->setHeaders($headers);
        http_response_code($statusCode);
        header("Location: $url");
        exit();
    }

    /**
     * Redireciona para uma URL com parâmetros dinâmicos.
     *
     * @param string $urlTemplate A URL template que pode conter placeholders de parâmetros.
     * @param array $parameters Um array de parâmetros para substituir placeholders na URL.
     * @param int $statusCode O código de status HTTP para o redirecionamento.
     * @param array $headers Cabeçalhos HTTP adicionais para incluir na resposta.
     */
    public function route($urlTemplate, $parameters = [], $statusCode = 302, $headers = [])
    {
        // Substituir parâmetros dinâmicos na URL
        foreach ($parameters as $key => $value) {
            $urlTemplate = str_replace('{' . $key . '}', $value, $urlTemplate);
        }

        // Redirecionar para a URL final construída
        $this->to($urlTemplate, $statusCode, $headers);
    }

    public function buildUrl($routeName, $parameters = [])
    {
        // Substituir parâmetros dinâmicos na URL
        foreach ($parameters as $key => $value) {
            $routeName = str_replace('{' . $key . '}', $value, $routeName);
        }

        return $routeName;
    }

    // Redireciona de volta para a URL anterior
    public function back($statusCode = 302, $headers = [])
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->to($referer, $statusCode, $headers);
    }

    // Adiciona dados à sessão para serem usados após o redirecionamento
    public function with($key, $value)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash'][$key] = $value;
        return $this;
    }

    // Adiciona dados antigos de entrada à sessão
    public function withInput($inputData)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['old_input'] = $inputData;
        return $this;
    }

    // Adiciona erros de validação à sessão
    public function withErrors($errors)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['errors'] = $errors;
        return $this;
    }

    // Define quaisquer cabeçalhos adicionais para o redirecionamento
    private function setHeaders($headers)
    {
        foreach ($headers as $header => $value) {
            header("$header: $value");
        }
    }
}
