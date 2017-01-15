<?php

namespace TodoList\Controller;

use TodoList\Model\Task;
use TodoList\Model\TaskTable;
use TodoList\Form\TaskForm;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;

class TaskController extends AbstractRestfulController
{
    private $table;

    // The constructor
    public function __construct(TaskTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        $userid = $this->params()->fromRoute('userid', 0);

        return new ViewModel([
            'userid' => $userid,
            'pendingTasks' => $this->table->fetchAllByStatus(1, $userid),
            'finishedTasks' => $this->table->fetchAllByStatus(2, $userid),
        ]);
    }

    public function addAction()
    {
        $userid = $this->params()->fromRoute('userid', 0);

        $form = new TaskForm();
        $form->get('submit')->setValue('Add');
        $form->get('userid')->setValue($userid);

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $task = new Task();
        $form->setInputFilter($task->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $task->exchangeArray($form->getData());
        $this->table->saveTask($task);
        return $this->redirect()->toRoute('task', ['userid' =>$task->userid]);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('task', ['action' => 'add']);
        }

        try {
            $task = $this->table->getTask($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('user', ['action' => 'login']);
        }


        $form = new TaskForm();
        $form->bind($task);
        $form->get('submit')->setAttribute('value', 'Apply');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($task->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveTask($task);

        // Redirect to todo list
        return $this->redirect()->toRoute('task', ['action' => 'index', 'userid' => $task->userid]);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('task');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                try {
                    $task = $this->table->getTask($id);
                } catch (\Exception $e) {
                    return $this->redirect()->toRoute('user', ['action' => 'login']);
                }
                $this->table->deleteTask($id);
            }

            // Redirect to list of tasks
            return $this->redirect()->toRoute('task',  ['action' => 'index', 'userid' => $task->userid]);
        }

        return [
            'id'    => $id,
            'task' => $this->table->getTask($id),
        ];
    }
}