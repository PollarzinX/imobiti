<?php
require_once(__DIR__."/../config/conexao.php");

class Perfil {
    private int $id;
    private string $nome;

    public function __construct(int $id = 0, string $nome = "") {
        $this->id = $id;
        $this->nome = $nome;
    }

    public function __get(string $prop) {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
        throw new Exception("Propriedade {$prop} não existe na classe Perfil");
    }

    public function __set(string $prop, $valor) {
        switch ($prop) {
            case "id":
                $this->id = (int)$valor;
                break;
            case "nome":
                $this->nome = trim($valor);
                break;
            default:
                throw new Exception("Propriedade {$prop} não permitida");
        }
    }

    public static function listar(): array {
        $pdo = Conexao::conexao();

        $sql = "SELECT * FROM perfis";

        $stmt = $pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $Perfis = [];
        foreach ($resultados as $row) {
            $Perfis[] = new Perfil($row['id_perfil'], $row['nome_perfil']);
        }
        return $Perfis;
    }
    
    public static function buscarPorId(int $id){

        $pdo = Conexao::conexao();
        $sql = "SELECT id_perfil as id, nome_perfil as nome FROM perfis WHERE id_perfil = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            return new Perfil($resultado['id'], $resultado['nome']);
        }
        return null; 
    }

}

print_r(Perfil::listar());

?>