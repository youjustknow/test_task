<?php

namespace FpDbTest;

use Exception;

class DatabaseTest
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function testBuildQuery(): void
    {
        $results = [];

        $results[] = $this->db->buildQuery('SELECT name FROM users WHERE user_id = 1');

        $results[] = $this->db->buildQuery(
            'SELECT * FROM users WHERE name = ? AND block = 0',
            ['Jack']
        );

        $results[] = $this->db->buildQuery(
            'SELECT ?# FROM users WHERE user_id = ?d AND block = ?d',
            [['name', 'email'], 2, true]
        );

        $results[] = $this->db->buildQuery(
            'UPDATE users SET ?a WHERE user_id = -1',
            [['name' => 'Jack', 'email' => null]]
        );

        foreach ([null, true] as $block) {
            $results[] = $this->db->buildQuery(
                'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}',
                ['user_id', [1, 2, 3], $block ?? $this->db->skip()]
            );
        }

        $results[] = $this->db->buildQuery(
            'UPDATE users SET ? WHERE user_id = ? AND balance >= ?{ OR blocked = ? OR name like ? }{ AND surname = ?}{ AND packed = ? }',
            [['name' => 'Jack', 'email' => null], 100, 3.14, $this->db->skip(), 'John', 'William', "\0"]
        );

        $results[] = $this->db->buildQuery(
            'UPDATE users SET balance = ?f WHERE name = ?',
            [10.01, "Jack or 1' = '1"]
        );


        $correct = [
            'SELECT name FROM users WHERE user_id = 1',
            'SELECT * FROM users WHERE name = \'Jack\' AND block = 0',
            'SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = 1',
            'UPDATE users SET `name` = \'Jack\', `email` = NULL WHERE user_id = -1',
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3)',
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1',
            'UPDATE users SET `name` = \'Jack\', `email` = NULL WHERE user_id = 100 AND balance >= 3.14 AND surname = \'William\' AND packed = \'\0\' ',
            'UPDATE users SET balance = 10.01 WHERE name = \'Jack or 1\\\' = \\\'1\''
        ];

        if ($results !== $correct) {
            throw new Exception('Failure.');
        }
    }
}
