<?php

require_once(__DIR__ . '/../config/conexao.php');

class Usuario
{

    private int $id;
    private string $nome;
    private string $email;
    private string $senhaHash;
    private int $idPerfil;
    private bool $ativo;

    public function __construct(


        ?int $id = 0,
        string $nome,
        string $email,
        string $senhaHash,
        int $idPerfil,
        ?bool $ativo
    ) {

        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senhaHash =  password_hash($senhaHash, PASSWORD_DEFAULT);
        $this->idPerfil = $idPerfil;
        $this->ativo = $ativo;
    }


    public function __get(string $prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
        throw new Exception("Propriedade {$prop} não existe.");
    }


    public function __set(string $prop, $valor)
    {
        switch ($prop) {
            case "id":
                $this->id = (int) $valor;
                break;
            case "nome":
                $this->nome = trim($valor);
                break;
            case "email":
                if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Email inválido: {$valor}");
                }
                $this->email = $valor;
                break;
            case "senhaHash":
                $this->senhaHash = password_hash($valor, PASSWORD_DEFAULT);
                break;
            case "idPerfil":
                $this->idPerfil = (int) $valor;
                break;
            case "ativo":
                $this->ativo = (bool) $valor;
                break;
            case "perfilNome":
                $this->perfilNome = $valor;
                break;
            default:
                throw new Exception("Propriedade {$prop} não permitida.");
        }
    }

    private static function getConexao()
    {
        return (new Conexao())->conexao();
    }

    public function inserir()
    {
        $pdo = self::getConexao();

        $sql = "INSERT INTO `usuarios` (`nome`, `email`, `senha`, `ativo`, `id_perfil`) 
VALUES (:nome, :email, :senha, :ativo, :idPerfil)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nome'     => $this->nome,
            ':email'    => $this->email,
            ':senha'    => $this->senhaHash,
            ':ativo'    => $this->ativo,
            ':idPerfil' => $this->idPerfil,
        ]);

        $ultimoId = $pdo->lastInsertId();
        if ($ultimoId <= 0) {
            throw new Exception("Não foi possível inserir o usuário.");
        }

        return $ultimoId;
    }

    public function listar()
    {
        $pdo = self::getConexao();


        $sql = "SELECT u.id_usuario,
            u.nome,
            u.email,
            u.ativo,
            u.id_perfil,
            p.nome_perfil AS perfil_nivel
        FROM usuarios AS u
        INNER JOIN perfis AS p ON p.id_perfil = u.id_perfil
        ORDER BY u.nome";

        $stmt = $pdo->query($sql);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("Nenhum usuário encontrado.");
            return null;
        }

        $usuario = new Usuario(
            id: (int) $row['id_usuario'],
            nome: $row['nome'],
            email: $row['email'],
            senhaHash: '',
            idPerfil: (int) $row['id_perfil'],
            ativo: (bool) $row['ativo']
        );


        $usuario->perfilNome = $row['perfil_nivel'];


        return $usuario;
    }


    public static function buscarPorId(int $id)
    {
        $pdo = self::getConexao();

        $sql = "SELECT u.id_usuario,
            u.nome,
            u.email,
            u.ativo,
            u.id_perfil,
            p.nome_perfil AS perfil_nivel
        FROM usuarios AS u
        INNER JOIN perfis AS p ON p.id_perfil = u.id_perfil
        WHERE u.id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);



        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = new Usuario(
                id: (int) $row['id_usuario'],
                nome: $row['nome'],
                email: $row['email'],
                senhaHash: '',
                idPerfil: (int) $row['id_perfil'],
                ativo: (bool) $row['ativo']
            );

            $usuario->perfilNome = $row['perfil_nivel'];

            return $usuario;
        } else {
            throw new Exception("Usuário com ID {$id} não encontrado.");
        }
    }

    public function listarPorEmail(string $email)
    {
        $pdo = self::getConexao();

        $sql = "SELECT u.id_usuario,
            u.nome,
            u.email,
            u.ativo,
            u.id_perfil,
            p.nome_perfil AS perfil_nivel
        FROM usuarios AS u
        INNER JOIN perfis AS p ON p.id_perfil = u.id_perfil
        WHERE u.email = :email";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = new Usuario(
                id: (int) $row['id_usuario'],
                nome: $row['nome'],
                email: $row['email'],
                senhaHash: '',
                idPerfil: (int) $row['id_perfil'],
                ativo: (bool) $row['ativo']
            );

            $usuario->perfilNome = $row['perfil_nivel'];

            return $usuario;
        } else {
            throw new Exception("Usuário com email {$email} não encontrado.");
        }
    }

    public function Excluir()
    {
        $pdo = self::getConexao();

        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([':id' => $this->id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Nenhum usuário foi excluído com ID {$this->id}");
        }
        return $stmt;
    }

    public function atualizar()
    {
        $pdo = self::getConexao();

        $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senhaHash, id_perfil = :idPerfil, ativo = :ativo
        WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':nome' => $this->nome,
            ':email' => $this->email,
            ':ativo' => $this->ativo,
            ':idPerfil' => $this->idPerfil,
            ':id' => $this->id
        ]);

        if ($stmt->rowCount() === 0) {
            return false;
        }

        return true;
    }
}

// $usuario1 = new Usuario(
//     nome: "nathan",
//     email: "natanzinhoDelas@gmail.com",
//     idPerfil: "3",
//     senhaHash: "nathan123",
//     ativo: true
// );

$usuario2 = new Usuario(
    id: 3,
    nome: "Apollo David",
    email: "apollo.david@example.com",
    senhaHash: "apollo123",
    idPerfil: 2,
    ativo: true
);

echo "<pre>";
try {
    print_r($usuario2->atualizar());
} catch (Exception $err) {
    echo "Erro: " . $err->getMessage();
}
