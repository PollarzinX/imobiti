<?php
require_once(__DIR__ . '/../config/conexao.php');


class Corretor
{
    private ?int $id = null;
    private string $creci = '';
    private string $telefone = '';
    private string $whatsapp = '';
    private bool $ativo = true;

    public function __construct(
        ?int $id = null,
        string $creci = '',
        string $telefone = '',
        string $whatsapp = '',
        bool $ativo = true
    ) {
        $this->id = $id;
        $this->creci = trim($creci);
        $this->telefone = trim($telefone);
        $this->whatsapp = trim($whatsapp);
        $this->ativo = (bool) $ativo;
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
            case "creci":
                $this->creci = trim($valor);
                break;
            case "telefone":
                $this->telefone = trim($valor);
                break;
            case "whatsapp":
                $this->whatsapp = trim($valor);
                break;
            case "ativo":
                $this->ativo = (bool) $valor;
                break;
            default:
                throw new Exception("Propriedade {$prop} não permitida");
        }
    }
    public static function listarImoveis(): array
    {
        $pdo = Conexao::conexao();
        $sql = "SELECT * FROM corretores";
        $stmt = $pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $corretores = [];
        foreach ($resultados as $row) {
            $corretores[] = new Corretor($row['id_corretor'], $row['creci'], $row['telefone'], $row['whatsapp'], $row['ativo']);
        }
        return $corretores;
    }
    public static function listarVisitas(int $idCorretor): array
    {
        $pdo = Conexao::conexao();
        $sql = "SELECT * FROM visitas WHERE id_corretor = :id_corretor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_corretor' => $idCorretor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function confirmarVisita(int $idVisita): bool
    {
        $pdo = Conexao::conexao();
        $sql = "UPDATE visitas SET status = 'confirmada' WHERE id_visita = :id_visita";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id_visita' => $idVisita]);
    }
    public static function Desativar(int $idCorretor): bool
    {
        $pdo = Conexao::conexao();
        $sql = "UPDATE corretores SET ativo = 0 WHERE id_corretor = :id_corretor";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id_corretor' => $idCorretor]);
    }
    public static function Reativar(int $idCorretor): bool
    {
        $pdo = Conexao::conexao();
        $sql = "UPDATE corretores SET ativo = 1 WHERE id_corretor = :id_corretor";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id_corretor' => $idCorretor]);
    }
}