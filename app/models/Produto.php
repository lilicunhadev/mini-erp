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
            $stmt = self::db()->prepare("
                SELECT quantidade FROM estoque 
                WHERE produto_id = ? AND variacao_id = ?
            ");
            $stmt->execute([$produtoId, $variacaoId]);
        } else {
            $stmt = self::db()->prepare("
                SELECT quantidade FROM estoque 
                WHERE produto_id = ? AND variacao_id IS NULL
            ");
            $stmt->execute([$produtoId]);
        }
        
        $result = $stmt->fetch();
        return $result ? (int)$result['quantidade'] : 0;
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
}
