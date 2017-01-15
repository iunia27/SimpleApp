<?php
namespace TodoList\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Retrieves all user rows from the database as a ResultSet
     * @return ResultSet result
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * Retrieves a single row as an User object
     * @param $id
     * @return User $row
     */
    public function getUser($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function getUserByUsername($username)
    {
        $rowset = $this->tableGateway->select(['username' => $username]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with username %d',
                $username
            ));
        }

        return $row;
    }

    /**
     * Either creates a new row in the database or updates a row that already exists
     * @param User $user
     */
    public function saveUser(User $user)
    {
        $data = [
            'username'  => $user->username,
            'password' => $user->password,
        ];

        $id = (int) $user->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if (! $this->getUser($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update user with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Removes the row completely
     * @param $id
     */
    public function deleteUser($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}