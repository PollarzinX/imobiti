<?php
require_once(__DIR__ . '/../model/Imovel.php');


function criarSlug($titulo) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
}

// Verifica se a variável $_POST está definida e se o método de requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // MAPEAMENTO DO OBJETO COM AS INFORMAÇÕES DO FRONT-END
        $imovel = new Imovel(
            id: 0, // 0 para novos cadastros
            titulo: $_POST['titulo'] ?? '',
            tipo: $_POST['tipo'] ?? '',
            tipo_negocio: $_POST['tipo_negocio'] ?? '',
            descricao: $_POST['descricao'] ?? '',
            preco: (float)($_POST['preco'] ?? 0),
            valor_condominio: (float)($_POST['valor_condominio'] ?? 0),
            valor_iptu: (float)($_POST['valor_iptu'] ?? 0),
            cep: $_POST['cep'] ?? '',
            cidade: $_POST['cidade'] ?? '',
            bairro: $_POST['bairro'] ?? '',
            estado: $_POST['estado'] ?? '',
            endereco: $_POST['endereco'] ?? '',
            quartos: (int)($_POST['quartos'] ?? 0),
            banheiros: (int)($_POST['banheiros'] ?? 0),
            vagas: (int)($_POST['vagas'] ?? 0),
            area: (float)($_POST['area'] ?? 0),
            status: $_POST['status'] ?? 'disponivel',
            id_corretor: (int)($_POST['id_corretor'] ?? 0),
            possui_piscina: $_POST['possui_piscina'] ?? false,
            possui_churrasqueira: $_POST['possui_churrasqueira'] ?? false,
            slug: criarSlug($_POST['titulo'] ?? '')
        );

        if ($imovel->salvar()) {
            // Sucesso !!
            echo "Imóvel salvo com sucesso!";

        } else {
            // Falha ao salvar
            echo "Erro ao salvar o imóvel.";
        }
    } catch (Exception $e) {
        // Tratar erros de validação ou outros erros
        echo "Erro: " . $e->getMessage();
    }
}














echo "<pre>";
print_r($_POST);

echo "<pre>";
print_r($_FILES);
