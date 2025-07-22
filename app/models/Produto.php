<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Produto extends Model
{
    public static function all(): array
    {
        $stmt = self::db()->query("
            SELECT p.*, 
                   COALESCE(SUM(e.quantidade), 0) as estoque_total
            FROM produtos p
            LEFT JOIN estoque e ON p.id = e.produto_id
            WHERE p.ativo = 1
            GROUP BY p.id
            ORDER BY p.nome
        ");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM produtos WHERE id = ? AND ativo = 1");
        $stmt->execute([$id]);
        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO produtos (nome, preco, descricao) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $data['nome'],
            $data['preco'],
            $data['descricao'] ?? ''
        ]);
        return self::db()->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $stmt = self::db()->prepare("
            UPDATE produtos 
            SET nome = ?, preco = ?, descricao = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['nome'],
            $data['preco'],
            $data['descricao'] ?? '',
            $id
        ]);
    }

    public static function getVariacoes(int $produtoId): array
    {
        $stmt = self::db()->prepare("
            SELECT pv.*, 
                   COALESCE(e.quantidade, 0) as estoque
            FROM produto_variacoes pv
            LEFT JOIN estoque e ON pv.id = e.variacao_id
            WHERE pv.produto_id = ? AND pv.ativo = 1
            ORDER BY pv.nome
        ");
        $stmt->execute([$produtoId]);
        return $stmt->fetchAll();
    }

    public static function getEstoque(int $produtoId, ?int $variacaoId = null): int
    {
        if ($variacaoId) {
            // Estoque de uma variação específica
            $stmt = self::db()->prepare("
                SELECT quantidade FROM estoque 
                WHERE produto_id = ? AND variacao_id = ?
            ");
            $stmt->execute([$produtoId, $variacaoId]);
            $result = $stmt->fetch();
            return $result ? (int)$result['quantidade'] : 0;
        } else {
            // Estoque total do produto (soma de todas as variações)
            $stmt = self::db()->prepare("
                SELECT COALESCE(SUM(quantidade), 0) as estoque_total
                FROM estoque 
                WHERE produto_id = ?
            ");
            $stmt->execute([$produtoId]);
            $result = $stmt->fetch();
            return (int)$result['estoque_total'];
        }
    }

    public static function updateEstoque(int $produtoId, ?int $variacaoId, int $quantidade): bool
    {
        $stmt = self::db()->prepare("
            INSERT INTO estoque (produto_id, variacao_id, quantidade) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE quantidade = VALUES(quantidade)
        ");
        return $stmt->execute([$produtoId, $variacaoId, $quantidade]);
    }

    public static function createVariacao(int $produtoId, string $tipo, string $valor): int
    {
        $stmt = self::db()->prepare("
            INSERT INTO produto_variacoes (produto_id, nome, tipo, valor) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$produtoId, $tipo, $tipo, $valor]);
        return self::db()->lastInsertId();
    }

    public static function updateVariacao(int $variacaoId, string $tipo, string $valor): bool
    {
        $stmt = self::db()->prepare("
            UPDATE produto_variacoes 
            SET nome = ?, tipo = ?, valor = ?
            WHERE id = ?
        ");
        return $stmt->execute([$tipo, $tipo, $valor, $variacaoId]);
    }

    public static function removeVariacao(int $variacaoId): bool
    {
        // Primeiro remove o estoque da variação
        $stmt = self::db()->prepare("DELETE FROM estoque WHERE variacao_id = ?");
        $stmt->execute([$variacaoId]);
        
        // Depois marca a variação como inativa (soft delete)
        $stmt = self::db()->prepare("
            UPDATE produto_variacoes 
            SET ativo = 0
            WHERE id = ?
        ");
        return $stmt->execute([$variacaoId]);
    }

    public static function delete(int $id): bool
    {
        // Primeiro remove todo o estoque do produto
        $stmt = self::db()->prepare("DELETE FROM estoque WHERE produto_id = ?");
        $stmt->execute([$id]);
        
        // Remove todas as variações do produto
        $stmt = self::db()->prepare("UPDATE produto_variacoes SET ativo = 0 WHERE produto_id = ?");
        $stmt->execute([$id]);
        
        // Marca o produto como inativo (soft delete)
        $stmt = self::db()->prepare("
            UPDATE produtos 
            SET ativo = 0
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public static function removeEstoqueGeral(int $produtoId): bool
    {
        // Remove estoque geral (variacao_id = NULL) para evitar duplicação
        // quando produto passa a ter variações
        $stmt = self::db()->prepare("DELETE FROM estoque WHERE produto_id = ? AND variacao_id IS NULL");
        return $stmt->execute([$produtoId]);
    }

    public static function removeTodasVariacoes(int $produtoId): bool
    {
        // Remove estoque de todas as variações
        $stmt = self::db()->prepare("DELETE FROM estoque WHERE produto_id = ? AND variacao_id IS NOT NULL");
        $stmt->execute([$produtoId]);
        
        // Marca todas as variações como inativas
        $stmt = self::db()->prepare("UPDATE produto_variacoes SET ativo = 0 WHERE produto_id = ?");
        return $stmt->execute([$produtoId]);
    }
}
