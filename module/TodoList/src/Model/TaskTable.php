<?php
namespace TodoList\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class TaskTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Retrieves all task rows from the database as a ResultSet
     * @return ResultSet result
     */
    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * Retrieves all task rows with status $status from the database as a ResultSet
     * @param $status
     * @return mixed
     */
    public function fetchAllByStatus($status, $userid)
    {
        return $this->tableGateway->select(array('status' => $status, 'userid' => $userid));
    }

    /**
     * Retrieves a single row as an Task object
     * @param $id
     * @return Task $row
     */
    public function getTask($id)
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

    /**
     * Either creates a new row in the database or updates a row that already exists
     * @param Task $task
     */
    public function saveTask(Task $task)
    {
        $data = [
            'title'  => $task->title,
            'status' => $task->status,
            'userid' => $task->userid,
        ];

        $id = (int) $task->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if (! $this->getTask($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update task with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Removes the row completely
     * @param $id
     */
    public function deleteTask($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}