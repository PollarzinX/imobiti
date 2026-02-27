<?php
    class Conexao {

        // Variaveis de conexao
        private static $host   = '10.91.45.61'; // Ajuste para localhost ou mantenha o IP da rede conforme o ambiente
        private static $bd     = 'imobiliaria'; 
        private static $user   = 'admin'; // ou 'root' caso esteja no XAMPP padrão
        private static $pass   = '123456'; // ou '' (vazio) caso não tenha senha no XAMPP local
        
        // Variável estática para guardar a conexão única (Singleton)
        private static $pdo = null; 
        
        public static function conexao(){
            // Só tenta conectar se a conexão ainda não existir
            if (self::$pdo === null) {
                try {
                    $strCon = "mysql:host=" . self::$host . ";dbname=" . self::$bd . ";charset=utf8";
                    
                    self::$pdo = new PDO($strCon, self::$user, self::$pass);
                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    // Garante que os dados retornem como arrays associativos por padrão, facilitando a leitura
                    self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                } catch(PDOException $err) {
                    // Em um ambiente de produção real, é melhor logar o erro num arquivo em vez de exibir na tela
                    die("Erro na conexão com o banco de dados: " . $err->getMessage());
                }
            }

            // Retorna a conexão existente
            return self::$pdo;
        }
    }
?>