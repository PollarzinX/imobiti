<?php
require_once(__DIR__."/../config/conexao.php");
require_once(__DIR__."/Corretor.php");


class Fotoimovel
{
    private int     $id_foto;
    private int     $id_imovel;
    private string  $caminho;
    private bool    $destaque;
    private int     $ordem;


    public function __construct(
        int     $id_foto =0,
        int     $id_imovel,
        string  $caminho,
        bool    $destaque,
        int     $ordem
    ) {
        $this->id_foto =  $id_foto;
        $this->id_imovel = $id_imovel;
        $this->caminho =  $caminho;
        $this->destaque = $destaque;
        $this->ordem =    $ordem;
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
            case "id_foto":
                $this->id_foto =     (int)$valor;
                break;
            case "id_imovel":
                $this->id_imovel =   (int)$valor;
                break;
            case "caminho":
                $this->caminho =      trim($valor);
                break;
            case "destaque":
                $this->destaque =    (bool)$valor;
                break;
            case "ordem":
                $this->ordem = (int)$valor;
            default:
                throw new Exception("Propriedade {$prop} não permitida");
        }
    }
    public function getConexao()
    {
        return (new Conexao())->conexao();
    }

    public function salvar()
    {
        $pdo = self::getConexao();

        // Se for definir como principal, desmarca as outras fotos deste imóvel primeiro
        if ($this->destaque) {
            $sqlReset = "UPDATE fotos_imovel SET destaque = 0 WHERE id_imovel = :id_imovel";
            $stmtReset = $pdo->prepare($sqlReset);
            $stmtReset->execute([':id_imovel' => $this->id_imovel]);
        }

        $sql = "INSERT INTO fotos_imovel (id_imovel, caminho, destaque) 
                VALUES (:id_imovel, :caminho, :destaque)";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id_imovel' => $this->id_imovel,
            ':caminho' => $this->caminho,
            ':destaque' => (int)$this->destaque
        ]);
    }
}


// $FotoImovel = new FotoImovel(
//     id_foto: 1,
//     id_imovel: 0,
//     caminho: "c:System 32",
//     destaque: true,
//     ordem: 1
// );

// echo "<pre>";
// print_r($FotoImovel);

// $FotoImovel->salvar();
